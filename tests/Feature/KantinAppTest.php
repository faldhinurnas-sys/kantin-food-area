<?php

namespace Tests\Feature;

use Tests\TestCase;

class KantinAppTest extends TestCase
{
    /**
     * Test landing page renders successfully with Polbeng Food Area branding.
     */
    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Polbeng Food Area');
        $response->assertSee('Rusunawa');
    }

    /**
     * Test login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated users cannot access protected dashboards.
     */
    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $adminDashboard = $this->get('/admin/dashboard');
        $adminDashboard->assertRedirect('/login');

        $pedagangDashboard = $this->get('/pedagang/dashboard');
        $pedagangDashboard->assertRedirect('/login');

        $mahasiswaDashboard = $this->get('/mahasiswa/dashboard');
        $mahasiswaDashboard->assertRedirect('/login');

        $waliDashboard = $this->get('/wali/dashboard');
        $waliDashboard->assertRedirect('/login');
    }
}
