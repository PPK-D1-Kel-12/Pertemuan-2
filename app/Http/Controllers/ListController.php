<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    protected MockDataService $mockService;

    public function __construct(MockDataService $mockService)
    {
        $this->mockService = $mockService;
        $this->mockService->ensureInitialized();
    }

    /**
     * Ambil list langsung dari database MySQL jara_db dengan status kepemilikan.
     */
    public static function getLists(): array
    {
        $currentUser = DB::table('users')->where('id', session('jara_current_user_id', 1))->first();

        return DB::table('lists')
            ->select('lists.*', 'users.name as owner_name')
            ->leftJoin('users', 'lists.owner_id', '=', 'users.id')
            ->get()
            ->map(function ($list) use ($currentUser) {
                $list->is_owner = ($currentUser && $list->owner_id == $currentUser->id);
                return (array) $list;
            })
            ->keyBy('id')
            ->toArray();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $currentUserId = session('jara_current_user_id', 1);

        // Kueri berparameter untuk menyimpan list baru dengan auto-ownership (SRS-F-17)
        $listId = DB::table('lists')->insertGetId([
            'name' => trim($request->input('name')),
            'owner_id' => $currentUserId,
            'team_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('tasks.index', ['list_id' => $listId])
            ->with('success', "List '{$request->input('name')}' berhasil dibuat sebagai milik Anda!");
    }

    public function destroy(Request $request, int $id)
    {
        $currentUserId = session('jara_current_user_id', 1);

        // 1. Cek keberadaan list menggunakan kueri berparameter
        $list = DB::table('lists')->where('id', $id)->first();
        if (!$list) {
            return redirect()->route('tasks.index')->with('error', 'List tidak ditemukan.');
        }

        // 2. Eksekusi transaksi atomik (SRS-F-18)
        DB::beginTransaction();
        try {
            // Ambil seluruh ID task yang ada di dalam list ini
            $taskIds = DB::table('tasks')->where('list_id', $id)->pluck('id')->toArray();

            if (!empty($taskIds)) {
                // A. Hapus seluruh catatan aktivitas / progres task terkait
                DB::table('task_activities')->whereIn('task_id', $taskIds)->delete();

                // B. Hapus seluruh relasi kolaborator pada task terkait
                DB::table('task_collaborators')->whereIn('task_id', $taskIds)->delete();

                // C. Hapus notifikasi yang terhubung ke task terkait
                DB::table('notifications')->whereIn('task_id', $taskIds)->delete();

                // D. Hapus seluruh task di dalam list
                DB::table('tasks')->where('list_id', $id)->delete();
            }

            // Simulasi error rollback jika parameter query ?simulate_fail=1 disertakan (untuk demonstrasi praktikum)
            if ($request->query('simulate_fail') == '1') {
                throw new \Exception('Simulasi kegagalan server: Transaksi penghapusan list dibatalkan (Rollback).');
            }

            // E. Hapus entitas list utama
            DB::table('lists')->where('id', $id)->delete();

            // Seluruh langkah sukses -> simpan permanen
            DB::commit();

            // Cari ID list pertama yang tersisa untuk redirect
            $nextList = DB::table('lists')->first();
            $redirectParams = $nextList ? ['list_id' => $nextList->id] : [];

            return redirect()->route('tasks.index', $redirectParams)
                ->with('success', "List '{$list->name}' beserta seluruh task dan relasinya berhasil dihapus secara atomik!");

        } catch (\Throwable $e) {
            // Terjadi kegagalan di salah satu tahap -> BATALKAN SEMUANYA
            DB::rollBack();

            return redirect()->back()->with('error', 'Penghapusan GAGAL dan di-ROLLBACK: ' . $e->getMessage());
        }
    }
}

