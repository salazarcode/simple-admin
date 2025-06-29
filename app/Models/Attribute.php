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

class Attribute extends Model
{
    use HasUuids;

    protected $table = 'Attributes';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'OwnerTypeID',
        'Name',
        'AttributeTypeID',
        'IsComposition',
        'IsArray'
    ];

    protected $casts = [
        'IsComposition' => 'boolean',
        'IsArray' => 'boolean'
    ];

    /**
     * Get the slug attribute (generated from Name).
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->Name);
    }

    /**
     * Get the owner type of this attribute.
     */
    public function ownerType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'OwnerTypeID');
    }

    /**
     * Get the attribute type.
     */
    public function attributeType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'AttributeTypeID');
    }
}