<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Use Seeders
use Database\Seeders\RolesSeeders;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        // Registros de Clientes
        $clientes = [
            ['nombre' => 'Cliente 1', 'correo' => 'cliente1@example.com', 'telefono' => '1234567890', 'estatus' => true],
            ['nombre' => 'Cliente 2', 'correo' => 'cliente2@example.com', 'telefono' => '2345678901', 'estatus' => true],
            ['nombre' => 'Cliente 3', 'correo' => 'cliente3@example.com', 'telefono' => '3456789012', 'estatus' => true],
        ];

        Cliente::insert($clientes);

        // Registros de Usuarios
        $users = [
            ['nombre' => 'Secretaria 1', 'correo' => 'secretaria1@example.com', 'telefono' => '1234567890', 'password' => Hash::make('12345678'), 'rol_id' => 2, 'estatus' => true],
            ['nombre' => 'Secretaria 2', 'correo' => 'secretaria2@example.com', 'telefono' => '2345678901', 'password' => Hash::make('12345678'), 'rol_id' => 2, 'estatus' => true],
            ['nombre' => 'Secretaria 3', 'correo' => 'secretaria3@example.com', 'telefono' => '3456789012', 'password' => Hash::make('12345678'), 'rol_id' => 2, 'estatus' => true],
            ['nombre' => 'Técnico 4', 'correo' => 'tecnico4@example.com', 'telefono' => '4567890123', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 5', 'correo' => 'tecnico5@example.com', 'telefono' => '5678901234', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 6', 'correo' => 'tecnico6@example.com', 'telefono' => '6789012345', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 7', 'correo' => 'tecnico7@example.com', 'telefono' => '7890123456', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 8', 'correo' => 'tecnico8@example.com', 'telefono' => '8901234567', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 9', 'correo' => 'tecnico9@example.com', 'telefono' => '9012345678', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
            ['nombre' => 'Técnico 10', 'correo' => 'tecnico10@example.com', 'telefono' => '0123456789', 'password' => Hash::make('12345678'), 'rol_id' => 3, 'estatus' => true],
        ];

        User::insert($users);
        for ($i = 0; $i < 10; $i++) {
            DB::table('productos')->insert([
                'nombre' => 'Producto ' . Str::random(5),
                'descripcion' => Str::random(20),
                'precio' => rand(100, 1000) / 10,
                'img' => null,
                'estatus' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
         // Assuming you have some clients in your 'clientes' table
         $clienteIds = DB::table('clientes')->pluck('id');

         for ($i = 0; $i < 10; $i++) {
             DB::table('sucursales')->insert([
                 'nombre' => 'Sucursal ' . Str::random(5),
                 'direccion' => Str::random(20),
                 'telefono' => Str::random(10),
                 'estatus' => 1,
                 'cliente_id' => $clienteIds->random(),
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);
         }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
