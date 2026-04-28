<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(8)->create();

        User::query()->updateOrCreate(['email' => 'admin@itsupport.local'], [
            'id_karyawan' => 'ADM-0001',
            'nama_karyawan' => 'Admin IT Support',
            'divisi' => 'IT Support',
            'posisi_jabatan' => 'IT Manager',
            'role' => 'admin',
            'email' => 'admin@itsupport.local',
            'no_telp' => '081200000001',
            'password' => bcrypt('password123'),
        ]);

        User::query()->updateOrCreate(['email' => 'bagas@itsupport.local'], [
            'id_karyawan' => 'ADM-0002',
            'nama_karyawan' => 'Bagas',
            'divisi' => 'IT Support',
            'posisi_jabatan' => 'IT Support Staff',
            'role' => 'admin',
            'email' => 'bagas@itsupport.local',
            'no_telp' => '081200000002',
            'password' => bcrypt('password123'),
        ]);

        User::query()->updateOrCreate(['email' => 'yusuf@itsupport.local'], [
            'id_karyawan' => 'ADM-0003',
            'nama_karyawan' => 'Yusuf',
            'divisi' => 'IT Support',
            'posisi_jabatan' => 'IT Support Staff',
            'role' => 'admin',
            'email' => 'yusuf@itsupport.local',
            'no_telp' => '081200000003',
            'password' => bcrypt('password123'),
        ]);

        User::query()->updateOrCreate(['email' => 'user@itsupport.local'], [
            'id_karyawan' => 'USR-0001',
            'nama_karyawan' => 'User Employee',
            'divisi' => 'Operations',
            'posisi_jabatan' => 'Staff',
            'role' => 'user',
            'email' => 'user@itsupport.local',
            'no_telp' => '081200000010',
            'password' => bcrypt('password123'),
        ]);
    }
}
