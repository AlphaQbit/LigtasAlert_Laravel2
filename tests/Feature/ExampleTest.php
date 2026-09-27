<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redirects_to_the_admin_page(): void
    {
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_admin_page_is_available(): void
    {
        $this->get('/admin')->assertOk();
    }

    public function test_facilities_api_returns_facilities(): void
    {
        $this->getJson('/api/facilities')
            ->assertOk()
            ->assertJsonStructure([['id', 'name']]);
    }
}
