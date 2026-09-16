<?php

namespace Tests\Feature;

use App\Repositories\DatabaseListRepository;
use App\Repositories\DatabaseTaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SqlInjectionPreventionTest extends TestCase
{
    use RefreshDatabase;

    protected DatabaseListRepository $listRepo;
    protected DatabaseTaskRepository $taskRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->listRepo = app(DatabaseListRepository::class);
        $this->taskRepo = app(DatabaseTaskRepository::class);
    }

    /**
     * Pengujian 1: Injeksi SQL pada pembuatan List berhasil dinegasikan oleh Prepared Statement.
     */
    public function test_list_creation_with_sql_injection_payload_is_handled_safely(): void
    {
        // Login sebagai Menza (ID 1)
        $this->get('/switch-user/1');

        $injectionPayload = "Malicious List'; DROP TABLE lists; --";

        $response = $this->post('/lists', [
            'name' => $injectionPayload,
        ]);

        $response->assertSessionHas('success');

        // Verifikasi bahwa tabel lists tetap ada dan utuh (DROP TABLE tidak pernah tereksekusi)
        $this->assertTrue(Schema::hasTable('lists'), 'Tabel lists wajib tetap ada.');

        // Verifikasi menggunakan Prepared Statement bahwa payload disimpan sebagai string harfiah
        $found = DB::selectOne('SELECT id, name FROM lists WHERE name = ?', [$injectionPayload]);
        $this->assertNotNull($found, 'List dengan payload harfiah harus ditemukan tanpa error sintaks SQL.');
        $this->assertEquals($injectionPayload, $found->name);
    }

    /**
     * Pengujian 2: Injeksi SQL pada pembuatan Task berhasil dinegasikan oleh Prepared Statement.
     */
    public function test_task_creation_with_sql_injection_payload_is_handled_safely(): void
    {
        $this->get('/switch-user/1');

        $titlePayload = "Task Payload' OR '1'='1";
        $descPayload = "Deskripsi'); DELETE FROM tasks; --";

        $response = $this->post('/tasks', [
            'title' => $titlePayload,
            'description' => $descPayload,
            'priority' => 'Tinggi',
            'deadline' => date('Y-m-d', strtotime('+5 days')),
            'list_id' => 1,
        ]);

        $response->assertSessionHas('success');

        // Pastikan tabel tasks tidak terhapus / ter-truncate
        $count = DB::selectOne('SELECT COUNT(*) as total FROM tasks');
        $this->assertGreaterThanOrEqual(1, $count->total);

        // Cari task via Prepared Statement
        $task = DB::selectOne('SELECT id, title, description FROM tasks WHERE title = ?', [$titlePayload]);
        $this->assertNotNull($task);
        $this->assertEquals($titlePayload, $task->title);
        $this->assertEquals($descPayload, $task->description);
    }

    /**
     * Pengujian 3: Verifikasi langsung CRUD DatabaseListRepository & DatabaseTaskRepository via Prepared Statement.
     */
    public function test_repositories_pure_prepared_statement_crud(): void
    {
        // 1. List Repository CRUD via Prepared Statements
        $listName = "Test Prepared Statement List' OR 1=1";
        $listId = $this->listRepo->create($listName, 1, 1);
        $this->assertIsInt($listId);

        $fetchedList = $this->listRepo->findById($listId);
        $this->assertNotNull($fetchedList);
        $this->assertEquals($listName, $fetchedList['name']);

        $updated = $this->listRepo->update($listId, "Updated Name'; --");
        $this->assertTrue($updated);

        $deleted = $this->listRepo->delete($listId);
        $this->assertTrue($deleted);
        $this->assertNull($this->listRepo->findById($listId));

        // 2. Task Repository CRUD via Prepared Statements
        $taskTitle = "Prepared Task' UNION SELECT * FROM users; --";
        $taskId = $this->taskRepo->create([
            'title' => $taskTitle,
            'description' => 'Prepared Statement Testing',
            'priority' => 'Sedang',
            'deadline' => date('Y-m-d'),
            'list_id' => 1,
        ], 1);
        $this->assertIsInt($taskId);

        $fetchedTask = $this->taskRepo->findById($taskId);
        $this->assertNotNull($fetchedTask);
        $this->assertEquals($taskTitle, $fetchedTask['title']);

        $statusUpdated = $this->taskRepo->updateStatus($taskId, 'Selesai');
        $this->assertTrue($statusUpdated);

        $taskDeleted = $this->taskRepo->delete($taskId);
        $this->assertTrue($taskDeleted);
        $this->assertNull($this->taskRepo->findById($taskId));
    }

    /**
     * Pengujian 4: Validasi Server menolak input kosong (empty) atau melebihi batas panjang karakter (oversized).
     */
    public function test_server_validation_rejects_malformed_and_oversized_inputs(): void
    {
        $this->get('/switch-user/1');

        // Judul task kosong (empty)
        $emptyRes = $this->post('/tasks', [
            'title' => '',
            'priority' => 'Tinggi',
            'list_id' => 1,
        ]);
        $emptyRes->assertSessionHasErrors('title');

        // Judul task melebihi 255 karakter
        $oversizedTitle = str_repeat('A', 256);
        $oversizedRes = $this->post('/tasks', [
            'title' => $oversizedTitle,
            'priority' => 'Tinggi',
            'list_id' => 1,
        ]);
        $oversizedRes->assertSessionHasErrors('title');

        // Prioritas tidak valid
        $invalidPriorityRes = $this->post('/tasks', [
            'title' => 'Judul Valid',
            'priority' => 'InvalidPriorityHacked',
            'list_id' => 1,
        ]);
        $invalidPriorityRes->assertSessionHasErrors('priority');

        // Catatan progres melebihi 1000 karakter
        $oversizedNote = str_repeat('N', 1001);
        $noteRes = $this->post('/tasks/1/progress-notes', [
            'note' => $oversizedNote,
        ]);
        $noteRes->assertSessionHasErrors('note');
    }
}
