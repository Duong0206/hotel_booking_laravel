<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Tạo các vai trò trước
        $this->call([
            RoleSeeder::class,
        ]);

        //Tạo tài khoản admin
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('abcd1234'),
            'email_verified_at' => now(),
            'role_id' => 1, // admin role
        ]);

        // Tạo tài khoản super admin
        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@hotel.com',
            'password' => Hash::make('abcd1234'),
            'email_verified_at' => now(),
            'role_id' => 4, // super_admin role
        ]);

        // Tạo tài khoản nhân viên
        User::create([
            'name' => 'Nhân viên',
            'username' => 'staff',
            'email' => 'staff@hotel.com',
            'password' => Hash::make('abcd1234'),
            'email_verified_at' => now(),
            'role_id' => 3, // staff role
        ]);

        // Tạo tài khoản khách hàng test
        User::create([
            'name' => 'Khách hàng',
            'username' => 'customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('abcd1234'),
            'email_verified_at' => now(),
            'role_id' => 2, // customer role
        ]);

        User::factory(10)->create();

        $this->call([
            NewRoomSeeder::class,
            RoomTypeReviewSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
