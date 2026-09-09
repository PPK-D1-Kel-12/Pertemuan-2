<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class MockDataService
{
    private const SESSION_KEY = 'jara_mock_database';
    private const CURRENT_USER_KEY = 'jara_current_user_id';

    /**
     * Inisialisasi data awal jika session masih kosong.
     */
    public function ensureInitialized(): void
    {
        if (!Session::has(self::SESSION_KEY)) {
            $this->resetToDefault();
        }

        if (!Session::has(self::CURRENT_USER_KEY)) {
            // Default login sebagai Menza (ID 1 - Pemilik Task / Project Lead)
            Session::put(self::CURRENT_USER_KEY, 1);
        }
    }

    /**
     * Kembalikan data default mock.
     */
    public function resetToDefault(): void
    {
        $data = [
            'users' => [
                1 => [
                    'id' => 1,
                    'name' => 'Menza (Anda)',
                    'email' => 'menza@jara.test',
                    'avatar' => 'MZ',
                    'color' => '#4f46e5',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2026-09-01 08:00:00',
                ],
                2 => [
                    'id' => 2,
                    'name' => 'Budi Pratama',
                    'email' => 'budi@jara.test',
                    'avatar' => 'BP',
                    'color' => '#0ea5e9',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2026-09-02 09:30:00',
                ],
                3 => [
                    'id' => 3,
                    'name' => 'Siti Rahma',
                    'email' => 'siti@jara.test',
                    'avatar' => 'SR',
                    'color' => '#10b981',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2026-09-02 11:15:00',
                ],
                4 => [
                    'id' => 4,
                    'name' => 'Joshua',
                    'email' => 'joshua@jara.test',
                    'avatar' => 'JS',
                    'color' => '#f59e0b',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2026-09-03 14:00:00',
                ],
                5 => [
                    'id' => 5,
                    'name' => 'Novelya',
                    'email' => 'novelya@jara.test',
                    'avatar' => 'NV',
                    'color' => '#ec4899',
                    'role' => 'user',
                    'status' => 'active',
                    'created_at' => '2026-09-03 14:15:00',
                ],
                6 => [
                    'id' => 6,
                    'name' => 'Iza (Admin)',
                    'email' => 'iza.admin@jara.test',
                    'avatar' => 'IZ',
                    'color' => '#64748b',
                    'role' => 'admin',
                    'status' => 'active',
                    'created_at' => '2026-08-30 10:00:00',
                ],
            ],
            'teams' => [
                1 => ['id' => 1, 'name' => 'Tim Praktikum PPK', 'owner_id' => 1],
                2 => ['id' => 2, 'name' => 'Tim Proyek Jara Core', 'owner_id' => 1],
            ],
            'lists' => [
                1 => ['id' => 1, 'name' => 'Sprint 1 - Kolaborasi', 'team_id' => 1, 'owner_id' => 1],
                2 => ['id' => 2, 'name' => 'Tugas Frontend PPK', 'team_id' => 1, 'owner_id' => 1],
                3 => ['id' => 3, 'name' => 'Pribadi & Persiapan', 'team_id' => null, 'owner_id' => 1],
            ],
            'tasks' => [
                1 => [
                    'id' => 1,
                    'title' => 'Implementasi Fitur Add Collaborator & Permission',
                    'description' => 'Membuat antarmuka penambahan kolaborator dengan role Viewer vs Editor, avatar stack, dan kontrol hak akses pemilik task.',
                    'priority' => 'Tinggi',
                    'deadline' => date('Y-m-d', strtotime('+2 days')),
                    'status' => 'In Progress',
                    'list_id' => 1,
                    'owner_id' => 1, // Menza
                    'created_at' => '2026-09-08 09:00:00',
                    'collaborators' => [
                        [
                            'user_id' => 2, // Budi
                            'role' => 'editor',
                            'added_at' => '2026-09-08 10:15:00',
                            'added_by' => 1,
                        ],
                        [
                            'user_id' => 3, // Siti
                            'role' => 'viewer',
                            'added_at' => '2026-09-08 10:30:00',
                            'added_by' => 1,
                        ],
                    ],
                    'activities' => [
                        [
                            'id' => 101,
                            'user_id' => 1,
                            'type' => 'created',
                            'description' => 'Menza membuat task ini.',
                            'timestamp' => '2026-09-08 09:00:00',
                        ],
                        [
                            'id' => 102,
                            'user_id' => 1,
                            'type' => 'collab_add',
                            'description' => 'Menza menambahkan Budi Pratama sebagai Editor.',
                            'timestamp' => '2026-09-08 10:15:00',
                        ],
                        [
                            'id' => 103,
                            'user_id' => 1,
                            'type' => 'collab_add',
                            'description' => 'Menza menambahkan Siti Rahma sebagai Viewer.',
                            'timestamp' => '2026-09-08 10:30:00',
                        ],
                        [
                            'id' => 104,
                            'user_id' => 2,
                            'type' => 'status_change',
                            'description' => 'Budi Pratama mengubah status dari To Do ke In Progress.',
                            'timestamp' => '2026-09-08 14:10:00',
                        ],
                        [
                            'id' => 105,
                            'user_id' => 2,
                            'type' => 'progress_note',
                            'description' => 'Desain komponen modal dan dropdown pencarian kolaborator sudah diuji secara visual.',
                            'timestamp' => '2026-09-08 15:45:00',
                        ],
                    ],
                ],
                2 => [
                    'id' => 2,
                    'title' => 'Pemantauan Linimasa Kronologis (Activity Log)',
                    'description' => 'Menyediakan linimasa visual aktivitas pengerjaan task agar owner bisa memantau siapa yang mengubah status dan waktu perubahannya.',
                    'priority' => 'Tinggi',
                    'deadline' => date('Y-m-d', strtotime('+3 days')),
                    'status' => 'To Do',
                    'list_id' => 1,
                    'owner_id' => 1, // Menza
                    'created_at' => '2026-09-08 11:00:00',
                    'collaborators' => [
                        [
                            'user_id' => 4, // Joshua
                            'role' => 'editor',
                            'added_at' => '2026-09-08 11:20:00',
                            'added_by' => 1,
                        ],
                    ],
                    'activities' => [
                        [
                            'id' => 201,
                            'user_id' => 1,
                            'type' => 'created',
                            'description' => 'Menza membuat task ini.',
                            'timestamp' => '2026-09-08 11:00:00',
                        ],
                        [
                            'id' => 202,
                            'user_id' => 1,
                            'type' => 'collab_add',
                            'description' => 'Menza menambahkan Joshua sebagai Editor.',
                            'timestamp' => '2026-09-08 11:20:00',
                        ],
                    ],
                ],
                3 => [
                    'id' => 3,
                    'title' => 'Review Desain Komponen UI Todoist Style',
                    'description' => 'Evaluasi layout List View, offcanvas drawer, dan palet warna Bootstrap untuk presentasi praktikum.',
                    'priority' => 'Sedang',
                    'deadline' => date('Y-m-d', strtotime('+1 days')),
                    'status' => 'In Progress',
                    'list_id' => 1,
                    'owner_id' => 5, // Novelya
                    'created_at' => '2026-09-07 13:00:00',
                    'collaborators' => [
                        [
                            'user_id' => 1, // Menza
                            'role' => 'editor',
                            'added_at' => '2026-09-07 14:00:00',
                            'added_by' => 5,
                        ],
                    ],
                    'activities' => [
                        [
                            'id' => 301,
                            'user_id' => 5,
                            'type' => 'created',
                            'description' => 'Novelya membuat task ini.',
                            'timestamp' => '2026-09-07 13:00:00',
                        ],
                        [
                            'id' => 302,
                            'user_id' => 5,
                            'type' => 'collab_add',
                            'description' => 'Novelya menambahkan Menza sebagai Editor.',
                            'timestamp' => '2026-09-07 14:00:00',
                        ],
                        [
                            'id' => 303,
                            'user_id' => 1,
                            'type' => 'progress_note',
                            'description' => 'Layout sidebar dan offcanvas detail sudah disesuaikan dengan standar praktikum.',
                            'timestamp' => '2026-09-07 16:30:00',
                        ],
                    ],
                ],
                4 => [
                    'id' => 4,
                    'title' => 'Setup Baseline Project Repository',
                    'description' => 'Inisialisasi repo Git dan struktur folder awal untuk pembagian modul tim.',
                    'priority' => 'Rendah',
                    'deadline' => date('Y-m-d', strtotime('-1 days')),
                    'status' => 'Selesai',
                    'list_id' => 1,
                    'owner_id' => 1, // Menza
                    'created_at' => '2026-09-06 08:00:00',
                    'collaborators' => [],
                    'activities' => [
                        [
                            'id' => 401,
                            'user_id' => 1,
                            'type' => 'created',
                            'description' => 'Menza membuat task ini.',
                            'timestamp' => '2026-09-06 08:00:00',
                        ],
                        [
                            'id' => 402,
                            'user_id' => 1,
                            'type' => 'status_change',
                            'description' => 'Menza menandai task ini Selesai.',
                            'timestamp' => '2026-09-06 17:00:00',
                        ],
                    ],
                ],
            ],
            'notifications' => [
                [
                    'id' => 1,
                    'user_id' => 1,
                    'message' => 'Novelya menambahkan Anda sebagai kolaborator pada task "Review Desain Komponen UI Todoist Style"',
                    'task_id' => 3,
                    'timestamp' => '2026-09-07 14:00:00',
                    'read' => false,
                ],
            ],
            'admin_logs' => [
                [
                    'id' => 1,
                    'admin_name' => 'Iza (Admin)',
                    'action' => 'CREATE_USER',
                    'description' => 'Admin membuat akun pengguna baru: Novelya (novelya@jara.test)',
                    'timestamp' => '2026-09-03 14:15:00',
                ],
                [
                    'id' => 2,
                    'admin_name' => 'Iza (Admin)',
                    'action' => 'CREATE_USER',
                    'description' => 'Admin membuat akun pengguna baru: Joshua (joshua@jara.test)',
                    'timestamp' => '2026-09-03 14:00:00',
                ],
                [
                    'id' => 3,
                    'admin_name' => 'Iza (Admin)',
                    'action' => 'ACTIVATE_USER',
                    'description' => 'Admin mengaktifkan status akun: Siti Rahma',
                    'timestamp' => '2026-09-04 10:20:00',
                ],
            ],
        ];

        Session::put(self::SESSION_KEY, $data);
    }

    private function getDatabase(): array
    {
        $this->ensureInitialized();
        return Session::get(self::SESSION_KEY, []);
    }

    private function saveDatabase(array $data): void
    {
        Session::put(self::SESSION_KEY, $data);
    }

    public function getCurrentUser(): array
    {
        $this->ensureInitialized();
        $currentId = Session::get(self::CURRENT_USER_KEY, 1);
        $users = $this->getUsers();
        return $users[$currentId] ?? $users[1];
    }

    public function switchCurrentUser(int $userId): void
    {
        $this->ensureInitialized();
        Session::put(self::CURRENT_USER_KEY, $userId);
    }

    public function getUsers(): array
    {
        $db = $this->getDatabase();
        return $db['users'] ?? [];
    }

    public function getLists(): array
    {
        $db = $this->getDatabase();
        return $db['lists'] ?? [];
    }

    public function createList(string $name): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();
        $newId = count($db['lists']) ? max(array_keys($db['lists'])) + 1 : 1;

        $db['lists'][$newId] = [
            'id' => $newId,
            'name' => trim($name),
            'team_id' => 1,
            'owner_id' => $currentUser['id'],
        ];

        $this->saveDatabase($db);
        return ['success' => true, 'list_id' => $newId, 'message' => "List '{$name}' berhasil dibuat!"];
    }

    public function deleteList(int $listId): array
    {
        $db = $this->getDatabase();
        if (count($db['lists']) <= 1) {
            return ['success' => false, 'message' => 'Minimal harus ada 1 list dalam sistem.'];
        }

        if (isset($db['lists'][$listId])) {
            $name = $db['lists'][$listId]['name'];
            unset($db['lists'][$listId]);

            // Pindahkan task dari list ini ke list pertama yang ada
            $firstListId = array_key_first($db['lists']);
            foreach ($db['tasks'] as &$task) {
                if ($task['list_id'] === $listId) {
                    $task['list_id'] = $firstListId;
                }
            }

            $this->saveDatabase($db);
            return ['success' => true, 'message' => "List '{$name}' berhasil dihapus."];
        }

        return ['success' => false, 'message' => 'List tidak ditemukan.'];
    }

    public function getTasks(?int $listId = null, ?string $filter = null): array
    {
        $db = $this->getDatabase();
        $tasks = $db['tasks'] ?? [];
        $currentUser = $this->getCurrentUser();

        if ($listId) {
            $tasks = array_filter($tasks, fn($t) => $t['list_id'] === $listId);
        }

        if ($filter === 'my_tasks') {
            $tasks = array_filter($tasks, fn($t) => $t['owner_id'] === $currentUser['id']);
        } elseif ($filter === 'assigned_to_me') {
            $tasks = array_filter($tasks, function ($t) use ($currentUser) {
                foreach ($t['collaborators'] as $c) {
                    if ($c['user_id'] === $currentUser['id']) {
                        return true;
                    }
                }
                return false;
            });
        } elseif ($filter === 'high_priority') {
            $tasks = array_filter($tasks, fn($t) => $t['priority'] === 'Tinggi');
        }

        $users = $this->getUsers();
        foreach ($tasks as &$task) {
            $task['owner'] = $users[$task['owner_id']] ?? null;
            foreach ($task['collaborators'] as &$collab) {
                $collab['user'] = $users[$collab['user_id']] ?? null;
            }
            foreach ($task['activities'] as &$act) {
                $act['user'] = $users[$act['user_id']] ?? null;
            }
        }

        return array_values($tasks);
    }

    public function getTask(int $taskId): ?array
    {
        $tasks = $this->getTasks();
        foreach ($tasks as $task) {
            if ($task['id'] === $taskId) {
                return $task;
            }
        }
        return null;
    }

    public function updateTask(int $taskId, array $data): array
    {
        $db = $this->getDatabase();
        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        $task = &$db['tasks'][$taskId];
        $currentUser = $this->getCurrentUser();

        $task['title'] = $data['title'] ?? $task['title'];
        $task['description'] = $data['description'] ?? $task['description'];
        $task['priority'] = $data['priority'] ?? $task['priority'];
        $task['deadline'] = $data['deadline'] ?? $task['deadline'];

        if (isset($data['list_id']) && (int)$data['list_id'] !== $task['list_id']) {
            $oldList = $db['lists'][$task['list_id']]['name'] ?? 'List Lama';
            $newList = $db['lists'][$data['list_id']]['name'] ?? 'List Baru';
            $task['list_id'] = (int)$data['list_id'];

            $task['activities'][] = [
                'id' => time() + rand(1, 999),
                'user_id' => $currentUser['id'],
                'type' => 'status_change',
                'description' => "{$currentUser['name']} memindahkan task dari '{$oldList}' ke '{$newList}'.",
                'timestamp' => date('Y-m-d H:i:s'),
            ];
        }

        $this->saveDatabase($db);
        return ['success' => true, 'message' => 'Task berhasil diperbarui.'];
    }

    public function deleteTask(int $taskId): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        // Hanya Owner atau Admin yang boleh menghapus task
        if ($db['tasks'][$taskId]['owner_id'] !== $currentUser['id'] && $currentUser['role'] !== 'admin') {
            return ['success' => false, 'message' => 'Hanya Pemilik Task yang berhak menghapus task ini.'];
        }

        unset($db['tasks'][$taskId]);
        $this->saveDatabase($db);

        return ['success' => true, 'message' => 'Task berhasil dihapus.'];
    }

    public function moveTask(int $taskId, int $targetListId): array
    {
        return $this->updateTask($taskId, ['list_id' => $targetListId]);
    }

    /**
     * Tambah Kolaborator ke Task (SRS-F-08)
     */
    public function addCollaborator(int $taskId, int $targetUserId, string $role = 'editor'): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();
        $users = $db['users'] ?? [];

        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        $task = &$db['tasks'][$taskId];

        if ($task['owner_id'] !== $currentUser['id']) {
            return ['success' => false, 'message' => 'Hanya Pemilik Task yang dapat menambahkan kolaborator.'];
        }

        foreach ($task['collaborators'] as $collab) {
            if ($collab['user_id'] === $targetUserId) {
                return ['success' => false, 'message' => 'Pengguna ini sudah menjadi kolaborator.'];
            }
        }

        $task['collaborators'][] = [
            'user_id' => $targetUserId,
            'role' => in_array($role, ['editor', 'viewer']) ? $role : 'editor',
            'added_at' => date('Y-m-d H:i:s'),
            'added_by' => $currentUser['id'],
        ];

        $targetUser = $users[$targetUserId] ?? ['name' => 'User'];
        $roleLabel = $role === 'viewer' ? 'Viewer (Hanya Lihat)' : 'Editor (Bisa Update Status)';

        $task['activities'][] = [
            'id' => time() + rand(1, 999),
            'user_id' => $currentUser['id'],
            'type' => 'collab_add',
            'description' => "{$currentUser['name']} menambahkan {$targetUser['name']} sebagai {$roleLabel}.",
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $db['notifications'][] = [
            'id' => time() + rand(1000, 9999),
            'user_id' => $targetUserId,
            'message' => "{$currentUser['name']} menambahkan Anda sebagai kolaborator ({$roleLabel}) pada task \"{$task['title']}\"",
            'task_id' => $taskId,
            'timestamp' => date('Y-m-d H:i:s'),
            'read' => false,
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Berhasil menambahkan {$targetUser['name']} sebagai kolaborator."];
    }

    /**
     * Hapus Kolaborator dari Task (SRS-F-09)
     */
    public function removeCollaborator(int $taskId, int $targetUserId): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();
        $users = $db['users'] ?? [];

        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        $task = &$db['tasks'][$taskId];

        $isOwner = $task['owner_id'] === $currentUser['id'];
        $isSelf = $currentUser['id'] === $targetUserId;

        if (!$isOwner && !$isSelf) {
            return ['success' => false, 'message' => 'Anda tidak memiliki hak untuk menghapus kolaborator ini.'];
        }

        $targetUser = $users[$targetUserId] ?? ['name' => 'User'];
        $initialCount = count($task['collaborators']);

        $task['collaborators'] = array_values(array_filter(
            $task['collaborators'],
            fn($c) => $c['user_id'] !== $targetUserId
        ));

        if (count($task['collaborators']) === $initialCount) {
            return ['success' => false, 'message' => 'Kolaborator tidak ditemukan dalam task ini.'];
        }

        $actionText = $isSelf
            ? "{$targetUser['name']} keluar dari task ini."
            : "{$currentUser['name']} mengeluarkan {$targetUser['name']} dari task.";

        $task['activities'][] = [
            'id' => time() + rand(1, 999),
            'user_id' => $currentUser['id'],
            'type' => 'collab_remove',
            'description' => $actionText,
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Berhasil mengeluarkan {$targetUser['name']}."];
    }

    /**
     * Update Status Task (SRS-F-07 & SRS-F-11)
     */
    public function updateStatus(int $taskId, string $newStatus): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        $task = &$db['tasks'][$taskId];
        $oldStatus = $task['status'];

        $canEdit = ($task['owner_id'] === $currentUser['id']);
        if (!$canEdit) {
            foreach ($task['collaborators'] as $collab) {
                if ($collab['user_id'] === $currentUser['id'] && $collab['role'] === 'editor') {
                    $canEdit = true;
                    break;
                }
            }
        }

        if (!$canEdit) {
            return ['success' => false, 'message' => 'Anda hanya memiliki hak akses Viewer (Hanya Lihat).'];
        }

        if ($oldStatus === $newStatus) {
            return ['success' => true, 'message' => 'Status tidak berubah.'];
        }

        $task['status'] = $newStatus;

        $task['activities'][] = [
            'id' => time() + rand(1, 999),
            'user_id' => $currentUser['id'],
            'type' => 'status_change',
            'description' => "{$currentUser['name']} mengubah status dari '{$oldStatus}' ke '{$newStatus}'.",
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Status berhasil diperbarui menjadi {$newStatus}."];
    }

    /**
     * Tambah Catatan Progres / Komentar ke Linimasa (SRS-F-11)
     */
    public function addProgressNote(int $taskId, string $note): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        if (!isset($db['tasks'][$taskId])) {
            return ['success' => false, 'message' => 'Task tidak ditemukan.'];
        }

        $task = &$db['tasks'][$taskId];

        $canComment = ($task['owner_id'] === $currentUser['id']);
        if (!$canComment) {
            foreach ($task['collaborators'] as $collab) {
                if ($collab['user_id'] === $currentUser['id']) {
                    $canComment = true;
                    break;
                }
            }
        }

        if (!$canComment) {
            return ['success' => false, 'message' => 'Anda tidak memiliki akses ke task ini.'];
        }

        $task['activities'][] = [
            'id' => time() + rand(1, 999),
            'user_id' => $currentUser['id'],
            'type' => 'progress_note',
            'description' => trim($note),
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => 'Catatan progres berhasil ditambahkan ke linimasa.'];
    }

    /**
     * Tambah Task Baru (SRS-F-01)
     */
    public function createTask(array $data): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        $newId = count($db['tasks']) ? max(array_keys($db['tasks'])) + 1 : 1;

        $db['tasks'][$newId] = [
            'id' => $newId,
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'priority' => $data['priority'] ?? 'Sedang',
            'deadline' => $data['deadline'] ?? date('Y-m-d', strtotime('+3 days')),
            'status' => 'To Do',
            'list_id' => (int) ($data['list_id'] ?? 1),
            'owner_id' => $currentUser['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'collaborators' => [],
            'activities' => [
                [
                    'id' => time() + rand(1, 999),
                    'user_id' => $currentUser['id'],
                    'type' => 'created',
                    'description' => "{$currentUser['name']} membuat task ini.",
                    'timestamp' => date('Y-m-d H:i:s'),
                ],
            ],
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'task_id' => $newId, 'message' => 'Task berhasil dibuat.'];
    }

    public function getNotifications(): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();
        $notes = $db['notifications'] ?? [];

        return array_values(array_filter($notes, fn($n) => $n['user_id'] === $currentUser['id']));
    }

    // ==========================================
    // BAGIAN ADMIN (SRS-F-14, SRS-F-15, SRS-F-16)
    // TUGAS IZA
    // ==========================================

    public function getAdminLogs(): array
    {
        $db = $this->getDatabase();
        return array_reverse($db['admin_logs'] ?? []);
    }

    public function adminCreateUser(array $data): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        $newId = count($db['users']) ? max(array_keys($db['users'])) + 1 : 1;
        $name = trim($data['name']);
        $email = trim($data['email']);
        $role = in_array($data['role'] ?? 'user', ['admin', 'user']) ? $data['role'] : 'user';

        // Buat inisial avatar
        $words = explode(' ', $name);
        $avatar = count($words) >= 2 
            ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
            : strtoupper(substr($name, 0, 2));

        $colors = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6', '#06b6d4'];
        $color = $colors[array_rand($colors)];

        $db['users'][$newId] = [
            'id' => $newId,
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar,
            'color' => $color,
            'role' => $role,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Catat di log admin (SRS-F-16)
        $db['admin_logs'][] = [
            'id' => time() + rand(1, 999),
            'admin_name' => $currentUser['name'],
            'action' => 'CREATE_USER',
            'description' => "Admin menambahkan akun baru: {$name} ({$email}) dengan role {$role}.",
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Akun {$name} berhasil dibuat!"];
    }

    public function adminToggleUserStatus(int $userId): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        if (!isset($db['users'][$userId])) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan.'];
        }

        $user = &$db['users'][$userId];
        $newStatus = ($user['status'] ?? 'active') === 'active' ? 'inactive' : 'active';
        $user['status'] = $newStatus;

        $actionText = $newStatus === 'active' ? 'mengaktifkan kembali' : 'menonaktifkan';

        $db['admin_logs'][] = [
            'id' => time() + rand(1, 999),
            'admin_name' => $currentUser['name'],
            'action' => strtoupper($newStatus) . '_USER',
            'description' => "Admin {$actionText} akun: {$user['name']}.",
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Status {$user['name']} berhasil diubah menjadi {$newStatus}."];
    }

    public function adminDeleteUser(int $userId): array
    {
        $db = $this->getDatabase();
        $currentUser = $this->getCurrentUser();

        if (!isset($db['users'][$userId])) {
            return ['success' => false, 'message' => 'Pengguna tidak ditemukan.'];
        }

        $userName = $db['users'][$userId]['name'];
        unset($db['users'][$userId]);

        $db['admin_logs'][] = [
            'id' => time() + rand(1, 999),
            'admin_name' => $currentUser['name'],
            'action' => 'DELETE_USER',
            'description' => "Admin menghapus akun pengguna: {$userName}.",
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $this->saveDatabase($db);

        return ['success' => true, 'message' => "Akun {$userName} berhasil dihapus dari sistem."];
    }

    // ==========================================
    // AUTHENTICATION (LOGIN & REGISTRASI MANDIRI)
    // ==========================================

    public function registerUser(array $data): array
    {
        $data['role'] = 'user';
        return $this->adminCreateUser($data);
    }
}
