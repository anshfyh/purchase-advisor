<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Belanja lebih tenang, uang saku tetap aman.')
            ->assertDontSee('Fitur Utama')
            ->assertDontSee('Kesesuaian Ketentuan Mata Kuliah')
            ->assertDontSee('AJAX + REST API')
            ->assertDontSee('Bantu rencanakan pembelian tanpa mengorbankan kebutuhan utama.');
    }
}
