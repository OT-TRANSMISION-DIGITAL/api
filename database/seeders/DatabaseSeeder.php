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
        User::create([
            'nombre' => "Admin",
            'correo' => "amvr130@gmail.com",
            'telefono' => "8790123456",
            'password' => Hash::make("12345678"),
            'rol_id' => 1,
            'estatus' => true
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
