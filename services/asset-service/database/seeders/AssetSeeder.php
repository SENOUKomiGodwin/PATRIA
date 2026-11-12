<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $immobilier = Category::where('name', 'Immobilier')->first();
        $mobilier = Category::where('name', 'Mobilier')->first();
        $vehicules = Category::where('name', 'Véhicules')->first();
        $informatique = Category::where('name', 'Informatique')->first();
        $equipements = Category::where('name', 'Équipements')->first();

        $locations = Location::all();

        $serviceIds = ['SRV-001', 'SRV-002', 'SRV-003', 'SRV-004'];
        $assetCount = 0;
        $codeIndex = 1;

        $assets = [
            ['name' => 'Bureau direction', 'category' => $mobilier, 'value' => 1200.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Chaise ergonomique', 'category' => $mobilier, 'value' => 350.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Armoire métallique', 'category' => $mobilier, 'value' => 280.00, 'status' => 'EN_SERVICE', 'condition' => 'MOYEN'],
            ['name' => 'Table de réunion', 'category' => $mobilier, 'value' => 850.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Canapé d\'accueil', 'category' => $mobilier, 'value' => 1500.00, 'status' => 'EN_SERVICE', 'condition' => 'NEUF'],
            ['name' => 'Ordinateur portable Dell', 'category' => $informatique, 'value' => 1800.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Écran 27 pouces', 'category' => $informatique, 'value' => 350.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Imprimante laser', 'category' => $informatique, 'value' => 450.00, 'status' => 'EN_MAINTENANCE', 'condition' => 'MAUVAIS'],
            ['name' => 'Serveur rack', 'category' => $informatique, 'value' => 5000.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Routeur Cisco', 'category' => $informatique, 'value' => 1200.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Climatisation split', 'category' => $equipements, 'value' => 2500.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Projecteur vidéo', 'category' => $equipements, 'value' => 1800.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Machine à café', 'category' => $equipements, 'value' => 1200.00, 'status' => 'EN_SERVICE', 'condition' => 'MOYEN'],
            ['name' => 'Photocopieur', 'category' => $equipements, 'value' => 3200.00, 'status' => 'EN_MAINTENANCE', 'condition' => 'MAUVAIS'],
            ['name' => 'Destructeur de documents', 'category' => $equipements, 'value' => 450.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Véhicule utilitaire Peugeot', 'category' => $vehicules, 'value' => 25000.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Camionnette livraison', 'category' => $vehicules, 'value' => 35000.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Voiture de service Renault', 'category' => $vehicules, 'value' => 22000.00, 'status' => 'EN_MAINTENANCE', 'condition' => 'MOYEN'],
            ['name' => 'Scooter électrique', 'category' => $vehicules, 'value' => 3500.00, 'status' => 'EN_SERVICE', 'condition' => 'NEUF'],
            ['name' => 'Bicyclette administrative', 'category' => $vehicules, 'value' => 600.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Poste de travail complet', 'category' => $mobilier, 'value' => 2500.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Fauteuil directeur', 'category' => $mobilier, 'value' => 900.00, 'status' => 'EN_SERVICE', 'condition' => 'NEUF'],
            ['name' => 'Bibliothèque bois', 'category' => $mobilier, 'value' => 600.00, 'status' => 'EN_SERVICE', 'condition' => 'MOYEN'],
            ['name' => 'Laptop HP', 'category' => $informatique, 'value' => 1600.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Tablette iPad', 'category' => $informatique, 'value' => 800.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Scanner document', 'category' => $informatique, 'value' => 550.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Onduleur 3000VA', 'category' => $equipements, 'value' => 800.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Refroidisseur de bureau', 'category' => $equipements, 'value' => 300.00, 'status' => 'EN_SERVICE', 'condition' => 'NEUF'],
            ['name' => 'Microscope', 'category' => $equipements, 'value' => 4500.00, 'status' => 'EN_SERVICE', 'condition' => 'BON'],
            ['name' => 'Borne d\'accueil tactile', 'category' => $informatique, 'value' => 2800.00, 'status' => 'EN_SERVICE', 'condition' => 'NEUF'],
        ];

        foreach ($assets as $assetData) {
            $code = sprintf('PAT-2026-%05d', $codeIndex++);
            $serviceId = $serviceIds[$assetCount % count($serviceIds)];
            $location = $locations[$assetCount % count($locations)];

            Asset::create([
                'id' => Str::uuid(),
                'code' => $code,
                'name' => $assetData['name'],
                'category_id' => $assetData['category']->id,
                'acquisition_date' => now()->subMonths(rand(1, 36))->toDateString(),
                'acquisition_value' => $assetData['value'],
                'status' => $assetData['status'],
                'condition' => $assetData['condition'],
                'location_id' => $location->id,
                'service_id' => $serviceId,
                'qr_code' => $code,
            ]);

            $assetCount++;
        }
    }
}
