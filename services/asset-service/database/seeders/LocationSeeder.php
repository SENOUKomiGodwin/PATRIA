<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::firstOrCreate(['name' => 'Bâtiment A - Rez-de-chaussée'], ['address' => '1 Rue de la République']);
        Location::firstOrCreate(['name' => 'Bâtiment A - 1er étage'], ['address' => '1 Rue de la République']);
        Location::firstOrCreate(['name' => 'Bâtiment B - Rez-de-chaussée'], ['address' => '2 Avenue du Général de Gaulle']);
        Location::firstOrCreate(['name' => 'Bâtiment B - 2ème étage'], ['address' => '2 Avenue du Général de Gaulle']);
        Location::firstOrCreate(['name' => 'Bâtiment C - Sous-sol'], ['address' => '3 Boulevard de la Liberté']);
        Location::firstOrCreate(['name' => 'Entrepôt principal'], ['address' => '10 Zone Industrielle']);
    }
}
