<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}