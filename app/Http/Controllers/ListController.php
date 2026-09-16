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

    public function destroy(int $id)
    {
        $result = $this->mockService->deleteList($id);

        if ($result['success']) {
            return redirect()->route('tasks.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}

