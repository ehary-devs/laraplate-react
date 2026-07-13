<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Dashboard\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index(): Response
    {
        return Inertia::render('dashboard', [
            'stats' => $this->dashboard->stats(),
            'recentNotifications' => $this->dashboard->recentNotifications(),
        ]);
    }
}
