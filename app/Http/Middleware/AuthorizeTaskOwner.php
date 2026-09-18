<?php

namespace App\Http\Middleware;

use App\Services\MockDataService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTaskOwner
{
    public function __construct(
        protected MockDataService $mockService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $taskId = $request->route('task') ?? $request->route('id') ?? $request->input('task_id');

        if ($taskId !== null) {
            $task = $this->mockService->getTask((int) $taskId);

            if ($task) {
                $currentUser = $this->mockService->getCurrentUser();
                $isOwner = ($task['owner_id'] ?? null) === ($currentUser['id'] ?? null);
                $isAdmin = ($currentUser['role'] ?? '') === 'admin';

                if (!$isOwner && !$isAdmin) {
                    abort(403, 'Akses Ditolak: Hanya pemilik task yang berhak melakukan aksi ini.');
                }
            }
        }

        return $next($request);
    }
}
