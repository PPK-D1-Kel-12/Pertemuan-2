<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

/**
 * Repository untuk modul List menggunakan Prepared Statement murni (SRS-F-20).
 * Parameter binding (? atau :param) menjamin kekebalan mutlak dari serangan SQL Injection.
 */
class DatabaseListRepository
{
    /**
     * Ambil seluruh list menggunakan parameterized SELECT query.
     */
    public function getAll(): array
    {
        $rows = DB::select('SELECT id, name, team_id, owner_id, created_at, updated_at FROM lists ORDER BY id ASC');

        return array_map(fn($row) => (array) $row, $rows);
    }

    /**
     * Cari list berdasarkan ID menggunakan Prepared Statement dengan parameter binding (?).
     */
    public function findById(int $id): ?array
    {
        $row = DB::selectOne('SELECT id, name, team_id, owner_id, created_at, updated_at FROM lists WHERE id = ?', [$id]);

        return $row ? (array) $row : null;
    }

    /**
     * Tambah list baru ke database menggunakan Prepared Statement INSERT.
     */
    public function create(string $name, ?int $teamId, int $ownerId): int
    {
        $now = date('Y-m-d H:i:s');

        DB::insert(
            'INSERT INTO lists (name, team_id, owner_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
            [trim($name), $teamId, $ownerId, $now, $now]
        );

        return (int) DB::getPdo()->lastInsertId();
    }

    /**
     * Perbarui nama list menggunakan Prepared Statement UPDATE.
     */
    public function update(int $id, string $name): bool
    {
        $affected = DB::update(
            'UPDATE lists SET name = ?, updated_at = ? WHERE id = ?',
            [trim($name), date('Y-m-d H:i:s'), $id]
        );

        return $affected > 0;
    }

    /**
     * Hapus list berdasarkan ID menggunakan Prepared Statement DELETE.
     */
    public function delete(int $id): bool
    {
        $affected = DB::delete('DELETE FROM lists WHERE id = ?', [$id]);

        return $affected > 0;
    }

    /**
     * Hitung total list yang ada di sistem menggunakan Prepared Statement.
     */
    public function count(): int
    {
        $res = DB::selectOne('SELECT COUNT(*) as total FROM lists');
        return (int) ($res->total ?? 0);
    }
}
