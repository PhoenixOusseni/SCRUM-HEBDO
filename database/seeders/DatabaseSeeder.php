<?php

namespace Database\Seeders;

use App\Models\Employe;
use App\Models\Semaine;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // Équipe AUXFIN BF
        $employes = [
            ['nom' => 'Mr Ousseni',    'poste' => 'Responsable IT',                  'ordre' => 1],
            ['nom' => 'Mme Messifa',   'poste' => 'Chargée de projets',              'ordre' => 2],
            ['nom' => 'Mr Kafando',    'poste' => 'Chargé de programmes',            'ordre' => 3],
            ['nom' => 'Mr Evariste',   'poste' => 'Coordinateur terrain',            'ordre' => 4],
            ['nom' => 'Mr Sawadogo',   'poste' => 'Responsable financier',           'ordre' => 5],
            ['nom' => 'Mme Ouedraogo', 'poste' => 'Chargée de suivi-évaluation',    'ordre' => 6],
        ];

        foreach ($employes as $data) {
            Employe::firstOrCreate(['nom' => $data['nom']], $data + ['actif' => true]);
        }

        // Créer la semaine courante automatiquement
        Semaine::courante();
    }
}
