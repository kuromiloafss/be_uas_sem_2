<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Staff (Admin)
        $staffUser = \App\Models\User::create([
            'nama' => 'Staff Vokasi UB',
            'email' => 'staff@ub.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make('passwordstaff'),
            'role' => 'staff',
        ]);

        \App\Models\Staff::create([
            'user_id' => $staffUser->user_id,
            'nip' => '199001012023011001',
            'jabatan' => 'Admin Lost and Found',
        ]);

        // 2. Akun Dosen
        $dosenUser = \App\Models\User::create([
            'nama' => 'Dr. Budi Santoso',
            'email' => 'budi@ub.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make('passworddosen'),
            'role' => 'dosen',
        ]);

        \App\Models\Dosen::create([
            'user_id' => $dosenUser->user_id,
            'nip' => '198505052015011002',
        ]);

        // 3. Akun Mahasiswa Contoh
        $mhsUser = \App\Models\User::create([
            'nama' => 'Dylan Mahasiswa',
            'email' => 'dylan@student.ub.ac.id',
            'password' => \Illuminate\Support\Facades\Hash::make('passwordmhs'),
            'role' => 'mahasiswa',
        ]);

        \App\Models\Mahasiswa::create([
            'user_id' => $mhsUser->user_id,
            'nim' => '253140707111039',
            'program_studi' => 'Teknologi Informasi',
        ]);
    }
}
