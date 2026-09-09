<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_redirects_to_tasks(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/tasks');
    }

    public function test_auth_pages_render(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('Masuk ke Jara');
        $this->get('/register')->assertStatus(200)->assertSee('Daftar Akun Baru');
    }

    public function test_tasks_page_renders_successfully(): void
    {
        $response = $this->get('/tasks');
        $response->assertStatus(200);
        $response->assertSee('Jara');
        $response->assertSee('Menza (Anda)');
        $response->assertSee('Sedang Dikerjakan (In Progress)');
        $response->assertSee('Belum Dikerjakan (To Do)');
    }

    public function test_offcanvas_detail_renders_collaborators_and_timeline(): void
    {
        $response = $this->get('/tasks?open_task=1');
        $response->assertStatus(200);
        $response->assertSee('Kolaborator Task');
        $response->assertSee('Pemantauan Progres');
        $response->assertSee('Budi Pratama');
        $response->assertSee('Editor');
        $response->assertSee('Siti Rahma');
        $response->assertSee('Viewer');
    }

    public function test_add_collaborator_works(): void
    {
        $response = $this->post('/tasks/1/collaborators', [
            'user_id' => 4,
            'role' => 'viewer',
        ]);

        $response->assertRedirect('/tasks?open_task=1');
        $response->assertSessionHas('success');

        $page = $this->get('/tasks?open_task=1');
        $page->assertSee('Joshua');
    }

    public function test_add_progress_note_works(): void
    {
        $response = $this->post('/tasks/1/progress-notes', [
            'note' => 'Progress test: Integrasi komponen selesai!',
        ]);

        $response->assertRedirect('/tasks?open_task=1');
        $response->assertSessionHas('success');

        $page = $this->get('/tasks?open_task=1');
        $page->assertSee('Progress test: Integrasi komponen selesai!');
    }

    public function test_novelya_list_management_works(): void
    {
        // Create new list
        $res = $this->post('/lists', ['name' => 'Sprint 3 Testing']);
        $res->assertSessionHas('success');

        $page = $this->get('/tasks');
        $page->assertSee('Sprint 3 Testing');
    }

    public function test_iza_admin_panel_works(): void
    {
        // Admin users page
        $res = $this->get('/admin/users');
        $res->assertStatus(200);
        $res->assertSee('Daftar Akun Pengguna Terdaftar');

        // Admin create user
        $createRes = $this->post('/admin/users', [
            'name' => 'Rian Hidayat',
            'email' => 'rian@jara.test',
            'role' => 'user',
        ]);
        $createRes->assertRedirect('/admin/users');
        $createRes->assertSessionHas('success');

        // Admin logs page
        $logsRes = $this->get('/admin/logs');
        $logsRes->assertStatus(200);
        $logsRes->assertSee('Log Audit & Aktivitas Admin');
        $logsRes->assertSee('Rian Hidayat');
    }
}
