<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            ['rol'=>'Administrador'],
            ['rol'=>'Usuario'],
            
            
        ]);
    }
}
