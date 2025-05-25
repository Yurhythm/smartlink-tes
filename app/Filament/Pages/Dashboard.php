<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
// use App\Models\Project;
// use App\Models\Task;
// use App\Models\User;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.dashboard';

    // public function getProjects(): \Illuminate\Support\Collection
    // {
    //     return Project::withCount([
    //         'tasks as to_do_count' => fn($q) => $q->where('status', 'to_do'),
    //         'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
    //         'tasks as done_count' => fn($q) => $q->where('status', 'done'),
    //     ])->get();
    // }

    // public function getUserTasks(): \Illuminate\Support\Collection
    // {
    //     return User::withCount([
    //         'tasks as to_do_count' => fn ($q) => $q->where('status', 'to_do'),
    //         'tasks as in_progress_count' => fn ($q) => $q->where('status', 'in_progress'),
    //         'tasks as done_count' => fn ($q) => $q->where('status', 'done'),
    //     ])->get();
    // }

}
