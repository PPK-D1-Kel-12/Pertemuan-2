<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class ListController extends Controller
{
    protected MockDataService $mockService;

    public function __construct(MockDataService $mockService)
    {
        $this->mockService = $mockService;
        $this->mockService->ensureInitialized();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:100',
        ]);

        $result = $this->mockService->createList($request->input('name'));

        return redirect()->route('tasks.index', ['list_id' => $result['list_id']])
            ->with('success', $result['message']);
    }

    public function destroy(int $id)
    {
        $list = $this->mockService->getList($id);
        if ($list) {
            $currentUser = $this->mockService->getCurrentUser();
            if (($list['owner_id'] ?? null) !== $currentUser['id'] && ($currentUser['role'] ?? '') !== 'admin') {
                abort(403, 'Akses Ditolak: Anda bukan pemilik sah dari list ini.');
            }
        }

        $result = $this->mockService->deleteList($id);

        if (!empty($result['unauthorized'])) {
            abort(403, $result['message']);
        }

        if ($result['success']) {
            return redirect()->route('tasks.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
