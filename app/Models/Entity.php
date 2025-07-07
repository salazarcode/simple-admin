<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Values\StringValue;
use App\Models\Values\IntValue;
use App\Models\Values\DoubleValue;
use App\Models\Values\DateTimeValue;
use App\Models\Values\BooleanValue;
use App\Models\Values\RelationValue;

class Entity extends Model
{
    use HasUuids;

    protected $table = 'Entities';
    protected $primaryKey = 'ID';
    public $timestamps = true; // El esquema tiene created_at y updated_at
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['TypeID'];

    /**
     * Cache para los valores de atributos dinámicos ya cargados para esta instancia.
     * @var array
     */
    protected array $dynamicAttributesCache = [];

    /**
     * Flag para saber si ya hemos cargado todos los atributos para esta instancia.
     * @var bool
     */
    protected bool $areAttributesLoaded = false;

    // --- RELACIONES ELOQUENT ESTÁNDAR ---

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'TypeID');
    }

    public function stringValues(): HasMany
    {
        return $this->hasMany(StringValue::class, 'EntityID');
    }

    public function intValues(): HasMany
    {
        return $this->hasMany(IntValue::class, 'EntityID');
    }

    public function doubleValues(): HasMany
    {
        return $this->hasMany(DoubleValue::class, 'EntityID');
    }

    public function dateTimeValues(): HasMany
    {
        return $this->hasMany(DateTimeValue::class, 'EntityID');
    }

    public function booleanValues(): HasMany
    {
        return $this->hasMany(BooleanValue::class, 'EntityID');
    }

    public function relationValues(): HasMany
    {
        return $this->hasMany(RelationValue::class, 'EntityID');
    }

    // --- LÓGICA DE ATRIBUTOS MÁGICOS ---

    /**
     * Get the display name for this entity (tries to find a "nombre" or "name" attribute)
     */
    public function getDisplayName(): string
    {
        // Try different name variations
        $nameVariations = ['nombre', 'name', 'titulo', 'title', 'descripcion', 'description'];
        
        foreach ($nameVariations as $nameField) {
            $value = $this->getDynamicAttributeValue($nameField);
            if ($value && is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }
        
        // Fallback: show last 5 digits of GUID + string attributes summary
        $shortId = substr($this->ID, -5);
        $stringAttributes = $this->getStringAttributesSummary();
        
        if (!empty($stringAttributes)) {
            return "{$shortId} ({$stringAttributes})";
        }
        
        // Final fallback to type name + short ID
        $typeName = $this->type ? $this->type->Name : 'Entity';
        return "{$typeName} {$shortId}";
    }

    private function getStringAttributesSummary(): string
    {
        $stringValues = [];
        
        // Use the specific stringValues relationship if loaded
        if ($this->relationLoaded('stringValues') && $this->stringValues) {
            foreach ($this->stringValues as $value) {
                if ($value->Value && trim($value->Value) !== '') {
                    $stringValues[] = trim($value->Value);
                }
            }
        } elseif ($this->values) {
            // Fallback to general values relationship
            foreach ($this->values as $value) {
                if ($value->attribute && $value->attribute->Type === 'string') {
                    $val = $value->getValue();
                    if ($val && is_string($val) && trim($val) !== '') {
                        $stringValues[] = trim($val);
                    }
                }
            }
        }
        
        return implode(', ', array_slice($stringValues, 0, 3)); // Limit to first 3 string values
    }

    public function __get($key)
    {
        if ($this->isStandardAttribute($key)) {
            return parent::getAttribute($key);
        }
        return $this->getDynamicAttributeValue(Str::snake($key));
    }

    public function __set($key, $value)
    {
        if ($this->isStandardAttribute($key)) {
            parent::setAttribute($key, $value);
            return;
        }
        $this->setDynamicAttributeValue(Str::snake($key), $value);
    }

    public function getDynamicAttributeValue(string $attributeSlug)
    {
        try {
            if (!$this->areAttributesLoaded) {
                $this->loadAllDynamicAttributes();
            }

            // Try exact match first
            if (array_key_exists($attributeSlug, $this->dynamicAttributesCache)) {
                return $this->dynamicAttributesCache[$attributeSlug];
            }

            // Try different slug variations
            $slug = strtolower($attributeSlug);
            $variations = [
                $slug,
                str_replace('-', '_', $slug),
                str_replace('_', '-', $slug),
                str_replace(['-', '_'], ' ', $slug)
            ];

            foreach ($variations as $variation) {
                if (array_key_exists($variation, $this->dynamicAttributesCache)) {
                    return $this->dynamicAttributesCache[$variation];
                }
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Error getting dynamic attribute value', [
                'entity_id' => $this->ID,
                'attribute_slug' => $attributeSlug,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function setDynamicAttributeValue(string $attributeSlug, $value): void
    {
        // No permitir establecer campos estándar como atributos dinámicos
        $standardFields = ['id', 'i_d', 'typeid', 'type_id', 'created_at', 'updated_at'];
        if (in_array(strtolower($attributeSlug), $standardFields)) {
            throw new \Exception("Cannot set standard field '{$attributeSlug}' as dynamic attribute");
        }
        
        // Asegurar que la relación type esté cargada
        if (!$this->relationLoaded('type')) {
            $this->load('type');
        }
        
        if (!$this->type) {
            throw new \Exception("Entity type not found. Cannot set attribute '{$attributeSlug}'");
        }

        // Buscar el atributo por diferentes variaciones del slug incluyendo atributos heredados
        // Temporary workaround: manually get inherited attributes
        $allAttributes = $this->getInheritedAttributesForEntity();
        
        $attribute = $allAttributes->first(function($attr) use ($attributeSlug) {
            $slug = strtolower($attributeSlug);
            $attrName = strtolower($attr->Name);
            $attrSlug = strtolower(str_replace(' ', '-', $attr->Name));
            $attrSlugUnderscore = strtolower(str_replace(' ', '_', $attr->Name));
            
            return $attrSlug === $slug || 
                   $attrSlugUnderscore === $slug ||
                   $attrName === str_replace(['-', '_'], ' ', $slug) ||
                   $attrName === $slug;
        });

        if (!$attribute) {
            throw new \Exception("Attribute '{$attributeSlug}' not found for type '{$this->type->Name}'");
        }

        // Cargar la relación attributeType si no está cargada
        if (!$attribute->relationLoaded('attributeType')) {
            $attribute->load('attributeType');
        }

        $attributeType = $attribute->attributeType;
        $relationName = Str::camel($attributeType->Slug) . 'Values';
        $valueToStore = $value;

        if (!$attributeType->IsPrimitive) {
            $relationName = 'relationValues';
            
            // Handle relation values (arrays or single values)
            if (method_exists($this, $relationName)) {
                // First, delete existing values for this attribute
                $this->{$relationName}()->where('AttributeID', $attribute->ID)->delete();
                
                // Handle array values
                if ($attribute->IsArray && is_array($value)) {
                    foreach ($value as $singleValue) {
                        if (!empty($singleValue)) {
                            $valueToStore = $singleValue instanceof self ? $singleValue->ID : $singleValue;
                            $this->{$relationName}()->create([
                                'AttributeID' => $attribute->ID,
                                'Value' => $valueToStore
                            ]);
                        }
                    }
                } else {
                    // Handle single value
                    if (!empty($value)) {
                        $valueToStore = $value instanceof self ? $value->ID : $value;
                        $this->{$relationName}()->create([
                            'AttributeID' => $attribute->ID,
                            'Value' => $valueToStore
                        ]);
                    }
                }
                
                // Actualizar la caché local para consistencia inmediata
                $this->dynamicAttributesCache[$attributeSlug] = $value;
            } else {
                throw new \Exception("Value relation '{$relationName}' not defined on Entity model.");
            }
        } else {
            // Handle primitive values (existing logic)
            if (method_exists($this, $relationName)) {
                $this->{$relationName}()->updateOrCreate(
                    ['AttributeID' => $attribute->ID],
                    ['Value' => $valueToStore]
                );

                // Actualizar la caché local para consistencia inmediata
                $this->dynamicAttributesCache[$attributeSlug] = $value;
            } else {
                throw new \Exception("Value relation '{$relationName}' not defined on Entity model.");
            }
        }
    }

    /**
     * Carga y procesa TODOS los valores EAV para esta entidad de una sola vez.
     * Esta es la solución al problema N+1.
     */
    protected function loadAllDynamicAttributes(): void
    {
        try {
            // La línea completa y corregida para cargar todo de forma anidada
            $this->loadMissing([
                'stringValues.attribute',
                'intValues.attribute',
                'doubleValues.attribute',
                'dateTimeValues.attribute',
                'booleanValues.attribute',
                'relationValues.attribute',
                'relationValues.relatedEntity', // Eager-load de la entidad de destino
            ]);

            $primitiveRelations = ['stringValues', 'intValues', 'doubleValues', 'dateTimeValues', 'booleanValues'];
            
            // Procesar valores primitivos
            foreach ($primitiveRelations as $relation) {
                foreach ($this->{$relation} as $valueRecord) {
                    if ($valueRecord->attribute) {
                        $slug = Str::slug($valueRecord->attribute->Name);
                        $this->dynamicAttributesCache[$slug] = $valueRecord->Value;
                    }
                }
            }
            
            // Procesar relaciones
            foreach ($this->relationValues as $valueRecord) {
                if ($valueRecord->attribute) {
                    $slug = Str::slug($valueRecord->attribute->Name);
                    
                    // Si el atributo es array, agrupar las entidades relacionadas
                    if ($valueRecord->attribute->IsArray) {
                        if (!isset($this->dynamicAttributesCache[$slug])) {
                            $this->dynamicAttributesCache[$slug] = [];
                        }
                        $this->dynamicAttributesCache[$slug][] = $valueRecord->relatedEntity;
                    } else {
                        // Para atributos únicos, guardar directamente la entidad
                        $this->dynamicAttributesCache[$slug] = $valueRecord->relatedEntity;
                    }
                }
            }

            $this->areAttributesLoaded = true;
        } catch (\Exception $e) {
            \Log::error('Error loading dynamic attributes for entity', [
                'entity_id' => $this->ID,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->areAttributesLoaded = true; // Mark as loaded to prevent infinite loops
        }
    }

    protected function isStandardAttribute(string $key): bool
    {
        // Lista de atributos estándar del modelo Entity
        $standardAttributes = [
            'ID', 'id', 
            'TypeID', 'type_id', 
            'created_at', 'updated_at',
            'createdAt', 'updatedAt'
        ];
        
        // Lista de relaciones del modelo
        $relations = [
            'type', 'stringValues', 'intValues', 'doubleValues', 
            'dateTimeValues', 'booleanValues', 'relationValues'
        ];
        
        // Verificar si es un atributo estándar
        if (in_array($key, $standardAttributes)) {
            return true;
        }
        
        // Verificar si es una relación
        if (in_array($key, $relations)) {
            return true;
        }
        
        // Verificar si existe como método en el modelo
        if (method_exists($this, $key)) {
            return true;
        }
        
        // Verificar snake_case versions
        $snakeKey = Str::snake($key);
        if (in_array($snakeKey, $standardAttributes)) {
            return true;
        }
        
        // Verificar si existe en los atributos ya cargados
        return array_key_exists($key, $this->attributes) 
            || array_key_exists($snakeKey, $this->attributes)
            || array_key_exists($key, $this->relations);
    }
    
    /**
     * Temporary workaround to get inherited attributes until Type model issue is resolved
     */
    private function getInheritedAttributesForEntity()
    {
        try {
            // Ensure type is loaded with all relationships
            if (!$this->relationLoaded('type')) {
                $this->load('type.attributes.attributeType');
            } elseif (!$this->type->relationLoaded('attributes')) {
                $this->type->load('attributes.attributeType');
            }
            
            $attributes = collect();
            
            // Get parent attributes first
            $parentIds = \DB::table('TypeHierarchy')
                ->where('ChildTypeID', $this->type->ID)
                ->pluck('ParentTypeID');
            
            if ($parentIds->isNotEmpty()) {
                // Load parent attributes using Eloquent to get proper models
                $parentAttributes = \App\Models\Attribute::with('attributeType')
                    ->whereIn('OwnerTypeID', $parentIds)
                    ->get();
                
                foreach ($parentAttributes as $attribute) {
                    $attributes->push($attribute);
                }
            }
            
            // Add own type's attributes
            foreach ($this->type->attributes as $attribute) {
                $attributes->push($attribute);
            }
            
            return $attributes;
        } catch (\Exception $e) {
            \Log::error('Error in getInheritedAttributesForEntity', [
                'entity_id' => $this->ID,
                'error' => $e->getMessage()
            ]);
            // Fallback to just type's attributes
            return $this->type ? $this->type->attributes : collect();
        }
    }
}