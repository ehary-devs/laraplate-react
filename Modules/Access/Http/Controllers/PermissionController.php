<?php

namespace Modules\Access\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Access\Http\Resources\PermissionResource;
use Modules\Access\Services\AccessService;

class PermissionController extends Controller
{
    public function __construct(private readonly AccessService $access) {}

    public function index(): Response
    {
        return Inertia::render('access/permissions/index', [
            'permissions' => PermissionResource::collection($this->access->listPermissions())
                ->response()
                ->getData(true),
            'filters' => request()->only(['search', 'sort', 'dir', 'per_page']),
        ]);
    }
}
