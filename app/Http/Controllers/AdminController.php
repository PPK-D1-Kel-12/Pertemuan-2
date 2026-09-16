<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected MockDataService $mockService;

    public function __construct(MockDataService $mockService)
    {
        $this->mockService = $mockService;
        $this->mockService->ensureInitialized();
    }

    public function users()
    {
        $currentUser = $this->mockService->getCurrentUser();
        $users = $this->mockService->getUsers();
        $lists = ListController::getLists();
        $notifications = $this->mockService->getNotifications();

        return view('admin.users', [
            'currentUser' => $currentUser,
            'users' => $users,
            'lists' => $lists,
            'notifications' => $notifications,
        ]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'role' => 'required|in:admin,user',
        ]);

        $result = $this->mockService->adminCreateUser($request->all());

        return redirect()->route('admin.users')->with('success', $result['message']);
    }

    public function toggleStatus(int $id)
    {
        $result = $this->mockService->adminToggleUserStatus($id);

        return redirect()->route('admin.users')->with('success', $result['message']);
    }

    public function destroyUser(int $id)
    {
        $result = $this->mockService->adminDeleteUser($id);

        return redirect()->route('admin.users')->with('success', $result['message']);
    }

    public function logs()
    {
        $currentUser = $this->mockService->getCurrentUser();
        $users = $this->mockService->getUsers();
        $lists = ListController::getLists();
        $logs = $this->mockService->getAdminLogs();
        $notifications = $this->mockService->getNotifications();

        return view('admin.logs', [
            'currentUser' => $currentUser,
            'users' => $users,
            'lists' => $lists,
            'logs' => $logs,
            'notifications' => $notifications,
        ]);
    }
}

