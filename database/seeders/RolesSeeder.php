<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['nombre' => 'admin'],
            ['nombre' => 'secretaria'],
            ['nombre' => 'tecnico'],
        ];
        foreach ($roles as $role) {
            Rol::create($role);
        }
    }
}
