<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase; // Menggunakan trait untuk menyegarkan database antara tes

    // Test untuk memuat semua pengguna
    public function test_load_all_users()
    {
        // Membuat pengguna untuk pengujian
        User::factory()->count(5)->create();

        $response = $this->get('/users'); // Mengakses rute ke loadAllUsers

        $response->assertStatus(200); // Memastikan status 200
        $response->assertViewIs('users'); // Memastikan view yang benar
        $response->assertViewHas('all_users'); // Memastikan view memiliki variabel 'all_users'
    }

    // Test untuk menambahkan pengguna baru
    public function test_add_user()
    {
        $data = [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/add/user', $data); // Mengakses rute ke AddUser

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect('/users'); // Memastikan redirect ke rute yang benar
        $response->assertSessionHas('success', 'User Added Successfully'); // Memastikan pesan sukses
    }

    // Test untuk validasi saat menambahkan pengguna baru
    public function test_add_user_validation()
    {
        $data = []; // Data kosong untuk validasi

        $response = $this->post('/add/user', $data); // Mengakses rute ke AddUser

        $response->assertSessionHasErrors(['full_name', 'email', 'phone_number', 'password']); // Memastikan ada error validasi
    }

    // Test untuk mengedit pengguna
    public function test_edit_user()
    {
        // Membuat pengguna untuk pengujian
        $user = User::factory()->create();

        $data = [
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone_number' => '0987654321',
            'user_id' => $user->id, // Menambahkan ID pengguna
        ];

        $response = $this->post('/edit/user', $data); // Mengakses rute ke EditUser

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $response->assertRedirect('/users'); // Memastikan redirect ke rute yang benar
        $response->assertSessionHas('success', 'User Updated Successfully'); // Memastikan pesan sukses
    }

    // Test untuk menghapus pengguna
    public function test_delete_user()
    {
        // Membuat pengguna untuk pengujian
        $user = User::factory()->create();

        $response = $this->delete('/users/' . $user->id); // Mengakses rute ke deleteUser

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $response->assertRedirect('/users'); // Memastikan redirect ke rute yang benar
        $response->assertSessionHas('success', 'User Deleted Successfully'); // Memastikan pesan sukses
    }
}
