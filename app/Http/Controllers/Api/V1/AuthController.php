<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah',
            ], 401);
        }

        // Hapus token lama jika perlu, atau biarkan multiple devices
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $userData = [
            'id' => $user->user_id,
            'nama' => $user->nama,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $userData['nim_nip'] = $user->mahasiswa->nim;
            $userData['prodi_jabatan'] = $user->mahasiswa->program_studi;
        } elseif ($user->role === 'staff' && $user->staff) {
            $userData['nim_nip'] = $user->staff->staff_id; // Atau nip jika ada
            $userData['prodi_jabatan'] = $user->staff->jabatan;
        } elseif ($user->role === 'dosen' && $user->dosen) {
            $userData['nim_nip'] = $user->dosen->nip;
            $userData['prodi_jabatan'] = 'Dosen';
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => $userData
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['mahasiswa', 'staff', 'dosen']);

        $userData = [
            'id' => $user->user_id,
            'nama' => $user->nama,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'mahasiswa' && $user->mahasiswa) {
            $userData['nim_nip'] = $user->mahasiswa->nim;
            $userData['prodi_jabatan'] = $user->mahasiswa->program_studi;
        } elseif ($user->role === 'staff' && $user->staff) {
            $userData['nim_nip'] = $user->staff->staff_id;
            $userData['prodi_jabatan'] = $user->staff->jabatan;
        } elseif ($user->role === 'dosen' && $user->dosen) {
            $userData['nim_nip'] = $user->dosen->nip;
            $userData['prodi_jabatan'] = 'Dosen';
        }
        
        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    public function checkNim(Request $request)
    {
        $request->validate(['nim' => 'required|string|size:15']);
        
        $nim = $request->nim;
        $prodiCode = substr($nim, 5, 2);

        $prodiMap = [
            '05' => 'Administrasi Bisnis',
            '07' => 'Teknologi Informasi',
            '15' => 'Keuangan dan Perbankan',
            '03' => 'Manajemen Perhotelan',
            '02' => 'Desain Grafis',
        ];

        if (!isset($prodiMap[$prodiCode])) {
            return response()->json([
                'success' => false,
                'message' => 'NIM tidak valid atau Program Studi tidak terdaftar.',
            ], 422);
        }

        // Cek apakah sudah terdaftar
        $exists = \App\Models\Mahasiswa::where('nim', $nim)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'NIM ini sudah terdaftar sebagai akun mahasiswa.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'prodi' => $prodiMap[$prodiCode]
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|string|size:15|unique:mahasiswa,nim',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $nim = $request->nim;
        $prodiCode = substr($nim, 5, 2);

        $prodiMap = [
            '05' => 'Administrasi Bisnis',
            '07' => 'Teknologi Informasi',
            '15' => 'Keuangan dan Perbankan',
            '03' => 'Manajemen Perhotelan',
            '02' => 'Desain Grafis',
        ];

        // Validasi kode prodi
        if (!isset($prodiMap[$prodiCode])) {
            return response()->json([
                'success' => false,
                'message' => 'NIM tidak valid atau Program Studi tidak terdaftar di sistem kami.',
            ], 422);
        }

        $programStudi = $prodiMap[$prodiCode];

        try {
            DB::beginTransaction();

            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mahasiswa',
            ]);

            Mahasiswa::create([
                'user_id' => $user->user_id,
                'nim' => $request->nim,
                'program_studi' => $programStudi,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil! Silakan login.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
