<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * Nama dan signature perintah artisan.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * Deskripsi perintah artisan.
     *
     * @var string
     */
    protected $description = 'Membuat akun administrator baru secara interaktif';

    /**
     * Eksekusi perintah konsol.
     */
    public function handle(): int
    {
        $this->info('==========================================');
        $this->info('  Pembuatan Akun Administrator TukarLayak ');
        $this->info('==========================================');

        // Input data secara interaktif
        $name = $this->ask('Masukkan nama lengkap admin');
        $email = $this->ask('Masukkan alamat email admin');
        $password = $this->secret('Masukkan password admin (minimal 8 karakter)');
        $passwordConfirmation = $this->secret('Konfirmasi password admin');

        // Validasi input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar sebelumnya.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.same' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            $this->newLine();
            $this->error('Gagal membuat akun admin:');
            foreach ($validator->errors()->all() as $error) {
                $this->line(" - {$error}");
            }
            return Command::FAILURE;
        }

        // Simpan data akun admin ke database dengan password ter-hash
        $admin = User::create([
            'name' => trim($name),
            'email' => strtolower(trim($email)),
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->newLine();
        $this->info("Berhasil! Akun Admin [{$admin->email}] berhasil dibuat dengan Role: {$admin->role}.");
        $this->info('Silakan login melalui halaman login aplikasi.');

        return Command::SUCCESS;
    }
}
