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
        $cacheKey = "type_{$this->ID}_inherited_attributes";

        return Cache::remember($cacheKey, now()->addHour(), function () {
            $collectedAttributes = [];
            $visited = [];
            $this->collectAttributesRecursively($collectedAttributes, $visited);
            return collect($collectedAttributes);
        });
    }

    /**
     * Recursively collect attributes from this type and its parents.
     */
    protected function collectAttributesRecursively(array &$collectedAttributes, array &$visited): void
    {
        if (in_array($this->ID, $visited)) return;
        $visited[] = $this->ID;

        foreach ($this->parents as $parent) {
            $parent->collectAttributesRecursively($collectedAttributes, $visited);
        }
        
        foreach ($this->attributes as $attribute) {
            $slug = Str::slug($attribute->Name ?? '');
            $collectedAttributes[$slug] = $attribute;
        }
    }
}