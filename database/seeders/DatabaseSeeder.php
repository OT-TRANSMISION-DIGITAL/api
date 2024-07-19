<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Use Seeders
use Database\Seeders\RolesSeeders;
use App\Models\User;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Seeders
        $this->call(RolesSeeder::class);
        User::insert([
            [
                'nombre' => "Angel",
                'correo' => "amvr130@gmail.com",
                'telefono' => "8790123456",
                'password' => Hash::make("12345678"),
                'rol_id' => 1,
                'estatus' => true
            ],
            [
                'nombre' => "Eder",
                'correo' => "egmr.49@gmail.com",
                'telefono' => "8714149701",
                'password' => Hash::make("87654321"),
                'rol_id' => 3,
                'estatus' => true
            ],
            [
                'nombre' => "Gerardo",
                'correo' => "egmr.90@outlook.com",
                'telefono' => "8714149801",
                'password' => Hash::make("87654321"),
                'rol_id' => 1,
                'estatus' => true
            ],
            [
                'nombre' => "Marcela",
                'correo' => "marcelacasesc@gmail.com",
                'telefono' => "8790123457",
                'password' => Hash::make("87654321"),
                'rol_id' => 1,
                'estatus' => true
            ]
        ]);
    }
}
