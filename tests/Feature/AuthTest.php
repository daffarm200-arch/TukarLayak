<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji 1: Halaman registrasi dapat ditampilkan dengan status 200 OK.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Daftar Akun Baru');
    }

    /**
     * Uji 2: Pengguna baru dapat mendaftar dan otomatis memiliki role 'user'.
     */
    public function test_new_users_can_register_and_automatically_get_user_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'user',
        ]);

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Uji 3: Pendaftaran dengan email duplikat harus ditolak oleh validasi.
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::create([
            'name' => 'Pengguna Pertama',
            'email' => 'duplikat@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/register', [
            'name' => 'Pengguna Kedua',
            'email' => 'duplikat@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Uji 4: Pendaftaran dengan password di bawah 8 karakter harus ditolak.
     */
    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti',
            'email' => 'siti@example.com',
            'password' => '12345',
            'password_confirmation' => '12345',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Uji 5: Halaman login dapat ditampilkan dengan status 200 OK.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali');
    }

    /**
     * Uji 6: Pengguna dapat login dengan email dan password yang valid.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'name' => 'Ahmad Fadli',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'ahmad@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    /**
     * Uji 7: Login dengan password salah harus ditolak.
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        User::create([
            'name' => 'Ahmad Fadli',
            'email' => 'ahmad@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'ahmad@example.com',
            'password' => 'passwordsalah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Uji 8: Rate limiting aktif jika login gagal 5 kali berturut-turut.
     */
    public function test_login_rate_limiting_activates_after_five_failed_attempts(): void
    {
        User::create([
            'name' => 'User Rate Limit',
            'email' => 'ratelimit@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'ratelimit@example.com',
                'password' => 'passwordsalah',
            ]);
        }

        // Percobaan ke-6 harus terblokir oleh rate limiting
        $response = $this->post('/login', [
            'email' => 'ratelimit@example.com',
            'password' => 'passwordsalah',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
    }

    /**
     * Uji 9: Pengguna biasa (role: user) DILARANG mengakses dashboard admin (403 Forbidden).
     */
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        // Harus ditolak dengan status 403 Forbidden
        $response->assertStatus(403);
    }

    /**
     * Uji 10: Administrator (role: admin) DAPAT mengakses dashboard admin (200 OK).
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Panel Pengelola TukarLayak');
        $response->assertSee('Hak Akses: Administrator Sistem');
    }

    /**
     * Uji 11: Pengguna biasa dapat mengakses dashboard pengguna (200 OK).
     */
    public function test_regular_user_can_access_user_dashboard(): void
    {
        $user = User::create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Halo, Pengguna Biasa!');
    }

    /**
     * Uji 12: Pengguna dapat logout dan sesi berakhir.
     */
    public function test_users_can_logout_and_terminate_session(): void
    {
        $user = User::create([
            'name' => 'User Logout',
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * Uji 13: Perintah Artisan `make:admin` dapat membuat akun admin secara interaktif.
     */
    public function test_make_admin_artisan_command_creates_admin(): void
    {
        $this->artisan('make:admin')
            ->expectsQuestion('Masukkan nama lengkap admin', 'Super Admin')
            ->expectsQuestion('Masukkan alamat email admin', 'superadmin@tukarlayak.id')
            ->expectsQuestion('Masukkan password admin (minimal 8 karakter)', 'AdminRahasia123')
            ->expectsQuestion('Konfirmasi password admin', 'AdminRahasia123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'Super Admin',
            'email' => 'superadmin@tukarlayak.id',
            'role' => 'admin',
        ]);

        $admin = User::where('email', 'superadmin@tukarlayak.id')->first();
        $this->assertTrue(Hash::check('AdminRahasia123', $admin->password));
        $this->assertTrue($admin->isAdmin());
    }
}
