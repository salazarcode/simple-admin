<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Type extends Model
{
    use HasUuids;

    protected $table = 'Types';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'Name',
        'Slug',
        'IsPrimitive',
        'IsAbstract'
    ];

    protected $casts = [
        'IsPrimitive' => 'boolean',
        'IsAbstract' => 'boolean'
    ];

    /**
     * Get the attributes that belong to this type.
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class, 'OwnerTypeID');
    }

    /**
     * Get the entities of this type.
     */
    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class, 'TypeID');
    }

    /**
     * Get attributes where this type is used as the attribute type.
     */
    public function attributesOfThisType(): HasMany
    {
        return $this->hasMany(Attribute::class, 'AttributeTypeID');
    }

    /**
     * Get the parent types in the inheritance hierarchy.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'TypeHierarchy', 'ChildTypeID', 'ParentTypeID');
    }

    /**
     * Get the child types in the inheritance hierarchy.
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'TypeHierarchy', 'ParentTypeID', 'ChildTypeID');
    }

    /**
     * Get all inherited attributes including those from parent types.
     * Child attributes override parent attributes with the same name.
     */
    public function getAllInheritedAttributes(): Collection
    {
        try {
            $collectedAttributes = [];
            
            // Manually fetch parent types to avoid relationship issues
            $parentIds = \DB::table('TypeHierarchy')
                ->where('ChildTypeID', $this->ID)
                ->pluck('ParentTypeID');
            
            if ($parentIds->isNotEmpty()) {
                $parentTypes = Type::with(['attributes.attributeType'])
                    ->whereIn('ID', $parentIds)
                    ->get();
                
                // Collect parent attributes
                foreach ($parentTypes as $parent) {
                    foreach ($parent->attributes as $attribute) {
                        $slug = Str::slug($attribute->Name ?? '');
                        $collectedAttributes[$slug] = $attribute;
                    }
                }
            }
            
            // Add own attributes (overriding parent attributes with same slug)
            foreach ($this->attributes as $attribute) {
                $slug = Str::slug($attribute->Name ?? '');
                $collectedAttributes[$slug] = $attribute;
            }
            
            return collect(array_values($collectedAttributes));
        } catch (\Exception $e) {
            \Log::error('Error in getAllInheritedAttributes for type: ' . $this->Name, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Fallback to just own attributes
            return $this->attributes;
        }
    }

    /**
     * Recursively collect attributes from this type and its parents.
     */
    protected function collectAttributesRecursively(array &$collectedAttributes, array &$visited): void
    {
        if (in_array($this->ID, $visited)) return;
        $visited[] = $this->ID;

        // Collect from parents first (depth-first)
        foreach ($this->parents as $parent) {
            // Ensure parent has its relationships loaded
            if (!$parent->relationLoaded('attributes')) {
                $parent->load(['attributes.attributeType']);
            }
            if (!$parent->relationLoaded('parents')) {
                $parent->load(['parents']);
            }
            $parent->collectAttributesRecursively($collectedAttributes, $visited);
        }
        
        // Add this type's attributes (will override parent attributes with same slug)
        foreach ($this->attributes as $attribute) {
            // Ensure attribute has its relationships loaded
            if (!$attribute->relationLoaded('attributeType')) {
                $attribute->load('attributeType');
            }
            
            $slug = Str::slug($attribute->Name ?? '');
            $collectedAttributes[$slug] = $attribute;
        }
    }
}