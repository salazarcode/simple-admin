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
    public $timestamps = false; // El esquema tiene 'CreatedAt' pero no 'UpdatedAt'
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = null;

    protected $fillable = ['TypeID', 'CreatedAt'];

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
        if (!$this->areAttributesLoaded) {
            $this->loadAllDynamicAttributes();
        }

        return array_key_exists($attributeSlug, $this->dynamicAttributesCache) 
            ? $this->dynamicAttributesCache[$attributeSlug] 
            : null;
    }

    public function setDynamicAttributeValue(string $attributeSlug, $value): void
    {
        // Nota: El slug del atributo en la DB debe ser snake_case para que esto funcione.
        $attribute = $this->type->attributes()->where('Slug', $attributeSlug)->first();

        if (!$attribute) {
            throw new \Exception("Attribute '{$attributeSlug}' not found for type '{$this->type->Name}'");
        }

        $type = $attribute->attributeType; // Carga la relación 'attributeType' del modelo Attribute
        $relationName = Str::camel($type->Slug) . 'Values';
        $valueToStore = $value;

        if (!$type->IsPrimitive) {
            $valueToStore = $value instanceof self ? $value->ID : $value;
            $relationName = 'relationValues';
        }
        
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

    /**
     * Carga y procesa TODOS los valores EAV para esta entidad de una sola vez.
     * Esta es la solución al problema N+1.
     */
    protected function loadAllDynamicAttributes(): void
    {
        // La línea completa y corregida para cargar todo de forma anidada
        $this->loadMissing([
            'stringValues.attribute',
            'intValues.attribute',
            'doubleValues.attribute',
            'dateTimeValues.attribute',
            'booleanValues.attribute',
            'relationValues.attribute',
            'relationValues.targetEntity', // Eager-load de la entidad de destino
        ]);

        $primitiveRelations = ['stringValues', 'intValues', 'doubleValues', 'dateTimeValues', 'booleanValues'];
        
        // Procesar valores primitivos
        foreach ($primitiveRelations as $relation) {
            foreach ($this->{$relation} as $valueRecord) {
                if ($valueRecord->attribute) {
                    $this->dynamicAttributesCache[$valueRecord->attribute->Slug] = $valueRecord->Value;
                }
            }
        }
        
        // Procesar relaciones
        foreach ($this->relationValues as $valueRecord) {
            if ($valueRecord->attribute) {
                // El valor es el objeto Entity completo que fue cargado con 'targetEntity'
                $this->dynamicAttributesCache[$valueRecord->attribute->Slug] = $valueRecord->targetEntity;
            }
        }

        $this->areAttributesLoaded = true;
    }

    protected function isStandardAttribute(string $key): bool
    {
        // Convierte camelCase (como en el código) a snake_case (como en la DB) para los atributos base.
        $snakeKey = Str::snake($key);
        return array_key_exists($snakeKey, $this->attributes) 
            || array_key_exists($key, $this->relations)
            || method_exists($this, $key);
    }
}