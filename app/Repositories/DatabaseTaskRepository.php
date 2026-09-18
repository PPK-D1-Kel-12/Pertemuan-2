<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

/**
 * Repository untuk modul Task, Kolaborator, dan Linimasa Aktivitas
 * menggunakan Prepared Statement murni (SRS-F-20).
 * Parameter binding (? atau :param) menjamin kekebalan mutlak dari serangan SQL Injection.
 */
class DatabaseTaskRepository
{
    /**
     * Ambil seluruh task berdasarkan list dan filter dengan Prepared Statement.
     */
    public function getAll(?int $listId = null, ?string $filter = null, ?int $currentUserId = null): array
    {
        $query = 'SELECT t.*, u.name as owner_name, u.email as owner_email, u.avatar as owner_avatar, u.color as owner_color 
                  FROM tasks t 
                  LEFT JOIN users u ON t.owner_id = u.id 
                  WHERE 1=1';
        $bindings = [];

        if ($listId !== null) {
            $query .= ' AND t.list_id = ?';
            $bindings[] = $listId;
        }

        if ($filter === 'my_tasks' && $currentUserId !== null) {
            $query .= ' AND t.owner_id = ?';
            $bindings[] = $currentUserId;
        } elseif ($filter === 'high_priority') {
            $query .= ' AND t.priority = ?';
            $bindings[] = 'Tinggi';
        } elseif ($filter === 'assigned_to_me' && $currentUserId !== null) {
            $query .= ' AND EXISTS (SELECT 1 FROM task_collaborators tc WHERE tc.task_id = t.id AND tc.user_id = ?)';
            $bindings[] = $currentUserId;
        }

        $query .= ' ORDER BY t.id ASC';

        $rows = DB::select($query, $bindings);

        $tasks = [];
        foreach ($rows as $row) {
            $taskArray = (array) $row;
            $taskArray['owner'] = [
                'id' => $taskArray['owner_id'],
                'name' => $taskArray['owner_name'],
                'email' => $taskArray['owner_email'],
                'avatar' => $taskArray['owner_avatar'],
                'color' => $taskArray['owner_color'],
            ];
            $taskArray['collaborators'] = $this->getCollaborators((int) $taskArray['id']);
            $taskArray['activities'] = $this->getActivities((int) $taskArray['id']);
            $tasks[] = $taskArray;
        }

        return $tasks;
    }

    /**
     * Cari task berdasarkan ID menggunakan Prepared Statement.
     */
    public function findById(int $id): ?array
    {
        $row = DB::selectOne(
            'SELECT t.*, u.name as owner_name, u.email as owner_email, u.avatar as owner_avatar, u.color as owner_color 
             FROM tasks t 
             LEFT JOIN users u ON t.owner_id = u.id 
             WHERE t.id = ?',
            [$id]
        );

        if (!$row) {
            return null;
        }

        $task = (array) $row;
        $task['owner'] = [
            'id' => $task['owner_id'],
            'name' => $task['owner_name'],
            'email' => $task['owner_email'],
            'avatar' => $task['owner_avatar'],
            'color' => $task['owner_color'],
        ];
        $task['collaborators'] = $this->getCollaborators($id);
        $task['activities'] = $this->getActivities($id);

        return $task;
    }

    /**
     * Buat task baru menggunakan Prepared Statement INSERT.
     */
    public function create(array $data, int $ownerId): int
    {
        $now = date('Y-m-d H:i:s');
        $priority = in_array($data['priority'] ?? '', ['Tinggi', 'Sedang', 'Rendah']) ? $data['priority'] : 'Sedang';
        $status = in_array($data['status'] ?? '', ['To Do', 'In Progress', 'Selesai']) ? $data['status'] : 'To Do';

        DB::insert(
            'INSERT INTO tasks (title, description, priority, deadline, status, list_id, owner_id, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                trim($data['title']),
                trim($data['description'] ?? ''),
                $priority,
                $data['deadline'] ?? date('Y-m-d', strtotime('+3 days')),
                $status,
                (int) $data['list_id'],
                $ownerId,
                $now,
                $now,
            ]
        );

        $newTaskId = (int) DB::getPdo()->lastInsertId();

        // Catat aktivitas awal pembuatan task
        $this->addActivity($newTaskId, $ownerId, 'created', 'Task dibuat.');

        return $newTaskId;
    }

    /**
     * Update rincian task menggunakan Prepared Statement UPDATE.
     */
    public function update(int $id, array $data): bool
    {
        $now = date('Y-m-d H:i:s');
        $priority = in_array($data['priority'] ?? '', ['Tinggi', 'Sedang', 'Rendah']) ? $data['priority'] : 'Sedang';

        $affected = DB::update(
            'UPDATE tasks 
             SET title = ?, description = ?, priority = ?, deadline = ?, list_id = ?, updated_at = ? 
             WHERE id = ?',
            [
                trim($data['title']),
                trim($data['description'] ?? ''),
                $priority,
                $data['deadline'],
                (int) $data['list_id'],
                $now,
                $id,
            ]
        );

        return $affected > 0;
    }

    /**
     * Update status task menggunakan Prepared Statement UPDATE.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $affected = DB::update(
            'UPDATE tasks SET status = ?, updated_at = ? WHERE id = ?',
            [$status, date('Y-m-d H:i:s'), $id]
        );

        return $affected > 0;
    }

    /**
     * Pindahkan task ke list lain menggunakan Prepared Statement UPDATE.
     */
    public function moveList(int $id, int $targetListId): bool
    {
        $affected = DB::update(
            'UPDATE tasks SET list_id = ?, updated_at = ? WHERE id = ?',
            [$targetListId, date('Y-m-d H:i:s'), $id]
        );

        return $affected > 0;
    }

    /**
     * Hapus task menggunakan Prepared Statement DELETE.
     */
    public function delete(int $id): bool
    {
        $affected = DB::delete('DELETE FROM tasks WHERE id = ?', [$id]);

        return $affected > 0;
    }

    /**
     * Ambil kolaborator task menggunakan Prepared Statement.
     */
    public function getCollaborators(int $taskId): array
    {
        $rows = DB::select(
            'SELECT tc.*, u.name, u.email, u.avatar, u.color, u.role as user_role 
             FROM task_collaborators tc 
             JOIN users u ON tc.user_id = u.id 
             WHERE tc.task_id = ? 
             ORDER BY tc.id ASC',
            [$taskId]
        );

        return array_map(function ($row) {
            $collab = (array) $row;
            $collab['user'] = [
                'id' => $collab['user_id'],
                'name' => $collab['name'],
                'email' => $collab['email'],
                'avatar' => $collab['avatar'],
                'color' => $collab['color'],
                'role' => $collab['user_role'],
            ];
            return $collab;
        }, $rows);
    }

    /**
     * Tambah kolaborator baru menggunakan Prepared Statement INSERT.
     */
    public function addCollaborator(int $taskId, int $userId, string $role, int $addedBy): bool
    {
        $now = date('Y-m-d H:i:s');
        $validRole = in_array($role, ['editor', 'viewer']) ? $role : 'editor';

        // Prepared Statement: INSERT OR IGNORE jika sudah terdaftar
        DB::insert(
            'INSERT OR REPLACE INTO task_collaborators (task_id, user_id, role, added_by, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?)',
            [$taskId, $userId, $validRole, $addedBy, $now, $now]
        );

        return true;
    }

    /**
     * Hapus kolaborator menggunakan Prepared Statement DELETE.
     */
    public function removeCollaborator(int $taskId, int $userId): bool
    {
        $affected = DB::delete(
            'DELETE FROM task_collaborators WHERE task_id = ? AND user_id = ?',
            [$taskId, $userId]
        );

        return $affected > 0;
    }

    /**
     * Ambil linimasa aktivitas task menggunakan Prepared Statement.
     */
    public function getActivities(int $taskId): array
    {
        $rows = DB::select(
            'SELECT ta.*, u.name, u.email, u.avatar, u.color 
             FROM task_activities ta 
             LEFT JOIN users u ON ta.user_id = u.id 
             WHERE ta.task_id = ? 
             ORDER BY ta.id ASC',
            [$taskId]
        );

        return array_map(function ($row) {
            $act = (array) $row;
            $act['user'] = [
                'id' => $act['user_id'],
                'name' => $act['name'],
                'email' => $act['email'],
                'avatar' => $act['avatar'],
                'color' => $act['color'],
            ];
            $act['timestamp'] = $act['created_at'] ?? date('Y-m-d H:i:s');
            return $act;
        }, $rows);
    }

    /**
     * Tambah aktivitas baru menggunakan Prepared Statement INSERT.
     */
    public function addActivity(int $taskId, int $userId, string $type, string $description): int
    {
        $now = date('Y-m-d H:i:s');

        DB::insert(
            'INSERT INTO task_activities (task_id, user_id, type, description, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?)',
            [$taskId, $userId, $type, trim($description), $now, $now]
        );

        return (int) DB::getPdo()->lastInsertId();
    }
}
