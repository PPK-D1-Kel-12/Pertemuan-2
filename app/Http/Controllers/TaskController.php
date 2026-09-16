<?php

namespace App\Http\Controllers;

use App\Services\MockDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected MockDataService $mockService;

    public function __construct(MockDataService $mockService)
    {
        $this->mockService = $mockService;
        $this->mockService->ensureInitialized();
    }

    public function index(Request $request)
    {
        $listId = $request->query('list_id') ? (int) $request->query('list_id') : 1;
        $filter = $request->query('filter', 'all');
        $openTaskId = $request->query('open_task') ? (int) $request->query('open_task') : null;

        $currentUser = $this->mockService->getCurrentUser();
        $users = $this->mockService->getUsers();
        $lists = ListController::getLists();

        $tasks = $this->mockService->getTasks($listId, $filter);
        $notifications = $this->mockService->getNotifications();

        $activeTask = null;
        if ($openTaskId) {
            $activeTask = $this->mockService->getTask($openTaskId);
        }

        // Kelompokkan task berdasarkan Status ala Kanban/List Todoist
        $tasksByStatus = [
            'To Do' => array_values(array_filter($tasks, fn($t) => $t['status'] === 'To Do')),
            'In Progress' => array_values(array_filter($tasks, fn($t) => $t['status'] === 'In Progress')),
            'Selesai' => array_values(array_filter($tasks, fn($t) => $t['status'] === 'Selesai')),
        ];

        return view('tasks.index', [
            'currentUser' => $currentUser,
            'users' => $users,
            'lists' => $lists,
            'currentListId' => $listId,
            'currentFilter' => $filter,
            'tasks' => $tasks,
            'tasksByStatus' => $tasksByStatus,
            'activeTask' => $activeTask,
            'notifications' => $notifications,
        ]);
    }

    public function switchUser(int $id)
    {
        $this->mockService->switchCurrentUser($id);
        $currentUser = $this->mockService->getCurrentUser();

        return redirect()->back()->with('success', "Beralih akun ke: {$currentUser['name']} ({$currentUser['email']})");
    }

    public function resetData()
    {
        $this->mockService->resetToDefault();
        return redirect()->route('tasks.index')->with('success', 'Data mock berhasil direset ke kondisi awal.');
    }

    public function addCollaborator(Request $request, int $taskId)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'role' => 'required|in:editor,viewer',
        ]);

        $result = $this->mockService->addCollaborator(
            $taskId,
            (int) $request->input('user_id'),
            $request->input('role')
        );

        if ($result['success']) {
            return redirect()->route('tasks.index', ['open_task' => $taskId])->with('success', $result['message']);
        }

        return redirect()->route('tasks.index', ['open_task' => $taskId])->with('error', $result['message']);
    }

    public function removeCollaborator(int $taskId, int $userId)
    {
        $result = $this->mockService->removeCollaborator($taskId, $userId);

        if ($result['success']) {
            return redirect()->route('tasks.index', ['open_task' => $taskId])->with('success', $result['message']);
        }

        return redirect()->route('tasks.index', ['open_task' => $taskId])->with('error', $result['message']);
    }

    public function updateStatus(Request $request, int $taskId)
    {
        $request->validate([
            'status' => 'required|in:To Do,In Progress,Selesai',
        ]);

        $result = $this->mockService->updateStatus($taskId, $request->input('status'));

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function addProgressNote(Request $request, int $taskId)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $result = $this->mockService->addProgressNote($taskId, $request->input('note'));

        if ($result['success']) {
            return redirect()->route('tasks.index', ['open_task' => $taskId])->with('success', $result['message']);
        }

        return redirect()->route('tasks.index', ['open_task' => $taskId])->with('error', $result['message']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:Tinggi,Sedang,Rendah',
            'list_id' => 'required|integer',
        ]);

        $result = $this->mockService->createTask($request->all());

        return redirect()->route('tasks.index', ['list_id' => $request->input('list_id'), 'open_task' => $result['task_id']])
            ->with('success', 'Task baru berhasil dibuat!');
    }

    public function update(Request $request, int $taskId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:Tinggi,Sedang,Rendah',
            'deadline' => 'required|date',
            'list_id' => 'required|integer',
        ]);

        $result = $this->mockService->updateTask($taskId, $request->all());

        return redirect()->route('tasks.index', ['list_id' => $request->input('list_id'), 'open_task' => $taskId])
            ->with('success', $result['message']);
    }

    public function destroy(int $taskId)
    {
        $result = $this->mockService->deleteTask($taskId);

        if ($result['success']) {
            return redirect()->route('tasks.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function moveList(Request $request, int $taskId)
    {
        $request->validate([
            'list_id' => 'required|integer',
        ]);

        $result = $this->mockService->moveTask($taskId, (int) $request->input('list_id'));

        return redirect()->route('tasks.index', ['list_id' => $request->input('list_id'), 'open_task' => $taskId])
            ->with('success', $result['message']);
    }
}

