<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrimitiveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usamos DB::table para evitar validaciones de modelo en este punto.
        // Es una forma segura de insertar datos base.
        DB::table('Types')->insert([
            ['ID' => Str::uuid(), 'Name' => 'Text', 'Slug' => 'string', 'IsPrimitive' => true, 'IsAbstract' => false],
            ['ID' => Str::uuid(), 'Name' => 'Integer', 'Slug' => 'integer', 'IsPrimitive' => true, 'IsAbstract' => false],
            ['ID' => Str::uuid(), 'Name' => 'Decimal', 'Slug' => 'double', 'IsPrimitive' => true, 'IsAbstract' => false],
            ['ID' => Str::uuid(), 'Name' => 'Boolean', 'Slug' => 'boolean', 'IsPrimitive' => true, 'IsAbstract' => false],
            ['ID' => Str::uuid(), 'Name' => 'DateTime', 'Slug' => 'datetime', 'IsPrimitive' => true, 'IsAbstract' => false],
        ]);
    }
}