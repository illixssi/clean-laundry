<?php

namespace Database\Seeders;

// database/seeders/UserAccountsTableSeeder.php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAccountsTableSeeder extends Seeder
{
    public function run()
    {
        $roles = DB::table('roles')->pluck('id', 'role_name');

        foreach ($roles as $role => $roleId) {
            DB::table('user_accounts')->insert([
                'name' => ucfirst($role),
                'username' => $role,
                'password' => Hash::make($role),
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
