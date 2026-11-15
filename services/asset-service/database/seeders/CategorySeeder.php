<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(['name' => 'Immobilier'], ['amortization_years' => 20, 'method' => 'LINEAIRE']);
        Category::firstOrCreate(['name' => 'Mobilier'], ['amortization_years' => 5, 'method' => 'LINEAIRE']);
        Category::firstOrCreate(['name' => 'Véhicules'], ['amortization_years' => 5, 'method' => 'LINEAIRE']);
        Category::firstOrCreate(['name' => 'Informatique'], ['amortization_years' => 3, 'method' => 'LINEAIRE']);
        Category::firstOrCreate(['name' => 'Équipements'], ['amortization_years' => 5, 'method' => 'LINEAIRE']);
    }
}
