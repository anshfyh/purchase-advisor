<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\FuzzyTsukamotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvisorFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_submit_ajax_analysis(): void
    {
        $this->post('/register', [
            'name' => 'Ani',
            'email' => 'ani@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('id="analysis-form"', false)
            ->assertSee("fetch('", false)
            ->assertSee('Ani')
            ->assertSee('User')
            ->assertSee('Profil')
            ->assertDontSee('Tentang')
            ->assertDontSee('Edit Profil')
            ->assertDontSee('Keluar')
            ->assertDontSee('Riwayat tersimpan');

        $this->postJson('/analyses', [
            'item_name' => 'Charger Laptop',
            'monthly_allowance' => 1500000,
            'current_money' => 1200000,
            'item_price' => 250000,
            'need_level' => 9,
            'days_until_allowance' => 23,
        ])->assertCreated()
            ->assertJsonPath('result.category', 'SANGAT LAYAK');

        $this->assertDatabaseHas('purchase_analyses', ['item_name' => 'Charger Laptop']);

        $this->get('/history')
            ->assertOk()
            ->assertSee('Riwayat tersimpan');

        $this->get('/profile')
            ->assertOk()
            ->assertSee('Pengaturan Profil')
            ->assertSee('Logout');

        $this->patch('/profile', [
            'name' => 'Ani Shofiyyah',
            'email' => 'ani.new@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Ani Shofiyyah',
            'email' => 'ani.new@example.test',
        ]);
    }

    public function test_admin_can_open_monitoring_while_user_cannot(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertSee('Ringkasan Aktivitas');
    }

    public function test_admin_can_manage_users_and_browse_admin_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user', 'name' => 'Nabila']);
        $result = (new FuzzyTsukamotoService())->analyze(80, 9, 23, 1200000, 250000);

        $analysis = $user->analyses()->create([
            'item_name' => 'Sepatu Kuliah',
            'monthly_allowance' => 1500000,
            'current_money' => 1200000,
            'item_price' => 250000,
            'need_level' => 9,
            'days_until_allowance' => 23,
            'remaining_percentage' => 80,
            'score' => $result['score'],
            'category' => $result['category'],
            'decision' => $result['decision'],
            'recommendation' => $result['recommendation'],
            'result_payload' => $result,
        ]);

        $this->actingAs($admin)->get('/admin/users')
            ->assertOk()
            ->assertSee('Manajemen User')
            ->assertSee('Nabila');

        $this->patch("/admin/users/{$user->id}/role", ['role' => 'admin'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'admin']);

        $this->get('/admin/analyses?search=Sepatu')
            ->assertOk()
            ->assertSee('Riwayat Analisis')
            ->assertSee('Sepatu Kuliah');

        $this->get("/admin/analyses/{$analysis->id}")
            ->assertOk()
            ->assertSee('Detail Analisis')
            ->assertSee('Sepatu Kuliah');

        $this->get('/admin/statistics')
            ->assertOk()
            ->assertSee('Barang Paling Sering Dianalisis')
            ->assertSee('Sepatu Kuliah');

        $this->delete("/admin/users/{$user->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_public_fuzzy_api_calculates_a_preview_without_storing_history(): void
    {
        $this->getJson('/api/fuzzy')
            ->assertOk()
            ->assertJsonPath('data.rules_count', 27);

        $this->postJson('/api/fuzzy/calculate', [
            'item_name' => 'Mouse Wireless',
            'monthly_allowance' => 1500000,
            'current_money' => 1200000,
            'item_price' => 180000,
            'need_level' => 9,
            'days_until_allowance' => 23,
        ])->assertOk()
            ->assertJsonPath('data.result.category', 'SANGAT LAYAK');

        $this->assertDatabaseCount('purchase_analyses', 0);
    }
}
