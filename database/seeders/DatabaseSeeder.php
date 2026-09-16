<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $users = [
            [
                'id' => 1,
                'name' => 'Menza (Anda)',
                'email' => 'menza@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'MZ',
                'color' => '#4f46e5',
                'role' => 'user',
                'status' => 'active',
                'created_at' => '2026-09-01 08:00:00',
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Budi Pratama',
                'email' => 'budi@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'BP',
                'color' => '#0ea5e9',
                'role' => 'user',
                'status' => 'active',
                'created_at' => '2026-09-02 09:30:00',
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Siti Rahma',
                'email' => 'siti@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'SR',
                'color' => '#10b981',
                'role' => 'user',
                'status' => 'active',
                'created_at' => '2026-09-02 11:15:00',
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Joshua',
                'email' => 'joshua@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'JS',
                'color' => '#f59e0b',
                'role' => 'user',
                'status' => 'active',
                'created_at' => '2026-09-03 14:00:00',
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Novelya',
                'email' => 'novelya@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'NV',
                'color' => '#ec4899',
                'role' => 'user',
                'status' => 'active',
                'created_at' => '2026-09-03 14:15:00',
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Iza (Admin)',
                'email' => 'iza.admin@jara.test',
                'password' => Hash::make('password'),
                'avatar' => 'IZ',
                'color' => '#64748b',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => '2026-08-30 10:00:00',
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(['id' => $user['id']], $user);
        }

        // 2. Teams
        $teams = [
            ['id' => 1, 'name' => 'Tim Praktikum PPK', 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Tim Proyek Jara Core', 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($teams as $team) {
            DB::table('teams')->updateOrInsert(['id' => $team['id']], $team);
        }

        // 3. Lists
        $lists = [
            ['id' => 1, 'name' => 'Sprint 1 - Kolaborasi', 'team_id' => 1, 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Tugas Frontend PPK', 'team_id' => 1, 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Pribadi & Persiapan', 'team_id' => null, 'owner_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($lists as $list) {
            DB::table('lists')->updateOrInsert(['id' => $list['id']], $list);
        }

        // 4. Tasks
        $tasks = [
            [
                'id' => 1,
                'title' => 'Implementasi Fitur Add Collaborator & Permission',
                'description' => 'Membuat antarmuka penambahan kolaborator dengan role Viewer vs Editor, avatar stack, dan kontrol hak akses pemilik task.',
                'priority' => 'Tinggi',
                'deadline' => date('Y-m-d', strtotime('+2 days')),
                'status' => 'In Progress',
                'list_id' => 1,
                'owner_id' => 1,
                'created_at' => '2026-09-08 09:00:00',
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Pemantauan Linimasa Kronologis (Activity Log)',
                'description' => 'Menyediakan linimasa visual aktivitas pengerjaan task agar owner bisa memantau siapa yang mengubah status dan waktu perubahannya.',
                'priority' => 'Tinggi',
                'deadline' => date('Y-m-d', strtotime('+3 days')),
                'status' => 'To Do',
                'list_id' => 1,
                'owner_id' => 1,
                'created_at' => '2026-09-08 11:00:00',
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'Review Desain Komponen UI Todoist Style',
                'description' => 'Mengecek konsistensi style Tailwind/Bootstrap pada baris task, offcanvas drawer, modal popup, dan badge peran.',
                'priority' => 'Sedang',
                'deadline' => date('Y-m-d', strtotime('+1 day')),
                'status' => 'In Progress',
                'list_id' => 1,
                'owner_id' => 5,
                'created_at' => '2026-09-07 13:00:00',
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'title' => 'Setup Layout Dasar Blade & Persona Switcher',
                'description' => 'Menyiapkan layout dasar Laravel Blade dengan navbar, sidebar, dan switcher persona untuk mempermudah demonstrasi.',
                'priority' => 'Rendah',
                'deadline' => date('Y-m-d', strtotime('-1 day')),
                'status' => 'Selesai',
                'list_id' => 1,
                'owner_id' => 1,
                'created_at' => '2026-09-06 08:00:00',
                'updated_at' => now(),
            ],
        ];
        foreach ($tasks as $task) {
            DB::table('tasks')->updateOrInsert(['id' => $task['id']], $task);
        }

        // 5. Collaborators
        $collaborators = [
            ['task_id' => 1, 'user_id' => 2, 'role' => 'editor', 'added_by' => 1, 'created_at' => '2026-09-08 10:15:00', 'updated_at' => now()],
            ['task_id' => 1, 'user_id' => 3, 'role' => 'viewer', 'added_by' => 1, 'created_at' => '2026-09-08 10:30:00', 'updated_at' => now()],
            ['task_id' => 2, 'user_id' => 4, 'role' => 'editor', 'added_by' => 1, 'created_at' => '2026-09-08 11:20:00', 'updated_at' => now()],
            ['task_id' => 3, 'user_id' => 1, 'role' => 'editor', 'added_by' => 5, 'created_at' => '2026-09-07 14:00:00', 'updated_at' => now()],
        ];
        foreach ($collaborators as $collab) {
            DB::table('task_collaborators')->updateOrInsert(
                ['task_id' => $collab['task_id'], 'user_id' => $collab['user_id']],
                $collab
            );
        }

        // 6. Task Activities
        $activities = [
            ['task_id' => 1, 'user_id' => 1, 'type' => 'created', 'description' => 'Menza membuat task ini.', 'created_at' => '2026-09-08 09:00:00', 'updated_at' => now()],
            ['task_id' => 1, 'user_id' => 1, 'type' => 'collab_add', 'description' => 'Menza menambahkan Budi Pratama sebagai Editor.', 'created_at' => '2026-09-08 10:15:00', 'updated_at' => now()],
            ['task_id' => 1, 'user_id' => 1, 'type' => 'collab_add', 'description' => 'Menza menambahkan Siti Rahma sebagai Viewer.', 'created_at' => '2026-09-08 10:30:00', 'updated_at' => now()],
            ['task_id' => 1, 'user_id' => 2, 'type' => 'status_change', 'description' => 'Budi Pratama mengubah status dari To Do ke In Progress.', 'created_at' => '2026-09-08 14:10:00', 'updated_at' => now()],
            ['task_id' => 1, 'user_id' => 2, 'type' => 'progress_note', 'description' => 'Desain komponen modal dan dropdown pencarian kolaborator sudah diuji secara visual.', 'created_at' => '2026-09-08 15:45:00', 'updated_at' => now()],
            ['task_id' => 2, 'user_id' => 1, 'type' => 'created', 'description' => 'Menza membuat task ini.', 'created_at' => '2026-09-08 11:00:00', 'updated_at' => now()],
            ['task_id' => 2, 'user_id' => 1, 'type' => 'collab_add', 'description' => 'Menza menambahkan Joshua sebagai Editor.', 'created_at' => '2026-09-08 11:20:00', 'updated_at' => now()],
            ['task_id' => 3, 'user_id' => 5, 'type' => 'created', 'description' => 'Novelya membuat task ini.', 'created_at' => '2026-09-07 13:00:00', 'updated_at' => now()],
            ['task_id' => 3, 'user_id' => 5, 'type' => 'collab_add', 'description' => 'Novelya menambahkan Menza sebagai Editor.', 'created_at' => '2026-09-07 14:00:00', 'updated_at' => now()],
            ['task_id' => 4, 'user_id' => 1, 'type' => 'created', 'description' => 'Menza membuat task ini.', 'created_at' => '2026-09-06 08:00:00', 'updated_at' => now()],
            ['task_id' => 4, 'user_id' => 1, 'type' => 'status_change', 'description' => 'Menza menandai task ini Selesai.', 'created_at' => '2026-09-06 17:00:00', 'updated_at' => now()],
        ];
        foreach ($activities as $act) {
            DB::table('task_activities')->insert($act);
        }

        // 7. Admin Logs
        $adminLogs = [
            ['admin_name' => 'Iza (Admin)', 'action' => 'CREATE_USER', 'description' => 'Admin membuat akun pengguna baru: Novelya (novelya@jara.test)', 'created_at' => '2026-09-03 14:15:00', 'updated_at' => now()],
            ['admin_name' => 'Iza (Admin)', 'action' => 'CREATE_USER', 'description' => 'Admin membuat akun pengguna baru: Joshua (joshua@jara.test)', 'created_at' => '2026-09-03 14:00:00', 'updated_at' => now()],
            ['admin_name' => 'Iza (Admin)', 'action' => 'ACTIVATE_USER', 'description' => 'Admin mengaktifkan status akun: Siti Rahma', 'created_at' => '2026-09-04 10:20:00', 'updated_at' => now()],
        ];
        foreach ($adminLogs as $log) {
            DB::table('admin_logs')->insert($log);
        }

        // 8. Notifications
        $notifications = [
            ['user_id' => 1, 'task_id' => 3, 'message' => 'Novelya menambahkan Anda sebagai kolaborator pada task "Review Desain Komponen UI Todoist Style"', 'read' => false, 'created_at' => '2026-09-07 14:00:00', 'updated_at' => now()],
        ];
        foreach ($notifications as $notif) {
            DB::table('notifications')->insert($notif);
        }
    }
}
