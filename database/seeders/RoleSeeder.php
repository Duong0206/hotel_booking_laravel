<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->delete(); // Xoá toàn bộ dữ liệu cũ

        $roles = [
            ['name' => 'admin'],     // id = 1
            ['name' => 'customer'],  // id = 2
            ['name' => 'staff'],     // id = 3
            ['name' => 'super_admin'],   // id = 4
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
