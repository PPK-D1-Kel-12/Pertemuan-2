<?php

namespace App\Http\Middleware;

use App\Services\MockDataService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeListOwner
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
        $listId = $request->route('list') ?? $request->route('id') ?? $request->input('list_id');

        if ($listId !== null) {
            $list = $this->mockService->getList((int) $listId);

            if ($list) {
                $currentUser = $this->mockService->getCurrentUser();
                $isOwner = ($list['owner_id'] ?? null) === ($currentUser['id'] ?? null);
                $isAdmin = ($currentUser['role'] ?? '') === 'admin';

                if (!$isOwner && !$isAdmin) {
                    abort(403, 'Akses Ditolak: Anda bukan pemilik sah dari list ini.');
                }
            }
        }

        return $next($request);
    }
}
