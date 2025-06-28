<?php

namespace App\Models\Values;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntValue extends Model
{
    use HasUuids;

    protected $table = 'IntValues';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'EntityID',
        'AttributeID',
        'Value'
    ];

    protected $casts = [
        'Value' => 'integer'
    ];

    /**
     * Get the entity that owns this value.
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'EntityID');
    }

    /**
     * Get the attribute definition for this value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'AttributeID');
    }
}