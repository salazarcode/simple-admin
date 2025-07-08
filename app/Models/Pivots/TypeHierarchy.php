<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TypeHierarchy extends Pivot
{
    protected $table = 'TypeHierarchy';
    public $incrementing = false;
    public $timestamps = false;
}