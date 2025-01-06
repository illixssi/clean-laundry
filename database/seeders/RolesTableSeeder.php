<?php

namespace Database\Seeders;

// database/seeders/RolesTableSeeder.php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'owner', 'kepala_operasional', 'kasir', 'staf_laundry'];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'role_name' => $role,
            ]);
        }
    }
}
