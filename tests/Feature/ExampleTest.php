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

    /*
     * -------------------------------------------------------------
     * PENGUJIAN SRS-F-19 (Anggota 2): Otorisasi & HTTP 403 Rejection
     * -------------------------------------------------------------
     */

    public function test_non_owner_cannot_delete_list_returns_403(): void
    {
        // Login sebagai Budi (ID 2 - bukan owner list 1 milik Menza)
        $this->get('/switch-user/2');

        // Budi mencoba menghapus list 1
        $res = $this->delete('/lists/1');

        // Wajib ditolak dengan status HTTP 403 Forbidden
        $res->assertStatus(403);
        $res->assertSee('Akses Ditolak');
        $res->assertSee('403 • FORBIDDEN');
    }

    public function test_owner_can_delete_list(): void
    {
        // Login sebagai Menza (ID 1 - Owner)
        $this->get('/switch-user/1');

        // Buat list sementara untuk dihapus
        $this->post('/lists', ['name' => 'Temporary Delete List']);
        $service = app(\App\Services\MockDataService::class);
        $lists = $service->getLists();
        $targetListId = max(array_keys($lists));

        // Menza menghapus list miliknya
        $res = $this->delete('/lists/' . $targetListId);
        $res->assertStatus(302);
        $res->assertSessionHas('success');
    }

    public function test_non_owner_cannot_delete_task_returns_403(): void
    {
        // Login sebagai Budi (ID 2 - bukan pemilik Task 1)
        $this->get('/switch-user/2');

        // Budi mencoba menghapus Task 1
        $res = $this->delete('/tasks/1');

        // Wajib ditolak HTTP 403
        $res->assertStatus(403);
        $res->assertSee('Akses Ditolak');
    }

    public function test_non_owner_cannot_update_task_details_returns_403(): void
    {
        // Login sebagai Siti (ID 3)
        $this->get('/switch-user/3');

        // Siti mencoba mengubah rincian Task 1
        $res = $this->put('/tasks/1', [
            'title' => 'Judul Dimanipulasi Non-Owner',
            'priority' => 'Rendah',
            'deadline' => date('Y-m-d'),
            'list_id' => 1,
        ]);

        $res->assertStatus(403);
    }

    public function test_non_owner_cannot_move_task_returns_403(): void
    {
        // Login sebagai Budi (ID 2)
        $this->get('/switch-user/2');

        // Budi mencoba memindahkan list Task 1
        $res = $this->post('/tasks/1/move-list', [
            'list_id' => 2,
        ]);

        $res->assertStatus(403);
    }

    public function test_viewer_cannot_update_task_status_returns_403(): void
    {
        // Login sebagai Siti (ID 3 - Role Viewer pada Task 1)
        $this->get('/switch-user/3');

        // Siti mencoba mengubah status Task 1
        $res = $this->post('/tasks/1/status', [
            'status' => 'Selesai',
        ]);

        // Wajib ditolak dengan HTTP 403
        $res->assertStatus(403);
        $res->assertSee('Akses Ditolak');
    }

    public function test_editor_and_owner_can_update_task_status(): void
    {
        // Budi (ID 2 - Editor pada Task 1) diizinkan update status
        $this->get('/switch-user/2');
        $resEditor = $this->post('/tasks/1/status', ['status' => 'Selesai']);
        $resEditor->assertStatus(302);
        $resEditor->assertSessionHas('success');

        // Menza (ID 1 - Owner Task 1) diizinkan update status
        $this->get('/switch-user/1');
        $resOwner = $this->post('/tasks/1/status', ['status' => 'In Progress']);
        $resOwner->assertStatus(302);
        $resOwner->assertSessionHas('success');
    }

    public function test_blade_ui_guard_hides_delete_button_for_non_owner(): void
    {
        // Budi (Non-Owner List 1) melihat halaman
        $this->get('/switch-user/2');
        $pageBudi = $this->get('/tasks');
        $pageBudi->assertStatus(200);
        // Form hapus list 1 tidak boleh ada pada HTML Budi
        $pageBudi->assertDontSee(route('lists.destroy', 1));

        // Menza (Owner List 1) melihat halaman
        $this->get('/switch-user/1');
        $pageMenza = $this->get('/tasks');
        $pageMenza->assertStatus(200);
        // Form hapus list 1 harus ada pada HTML Menza
        $pageMenza->assertSee(route('lists.destroy', 1));
    }

    public function test_403_error_page_renders_properly(): void
    {
        // Budi memicu 403
        $this->get('/switch-user/2');
        $res = $this->delete('/lists/1');

        $res->assertStatus(403);
        $res->assertSee('403 • FORBIDDEN');
        $res->assertSee('Akses Ditolak');
        $res->assertSee('Budi Pratama');
        $res->assertSee('Kembali ke Dashboard Task');
    }
}
