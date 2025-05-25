<?php

namespace App\Livewire\Filament\Pages\Dashboard;

use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProjectReportTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    protected function getTableQuery(): Builder
    {
        return Project::query()->withCount([
            'tasks as to_do_count' => fn($q) => $q->where('status', 'to_do'),
            'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
            'tasks as done_count' => fn($q) => $q->where('status', 'done'),
        ]);
    }

    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('name')->label('Project')->searchable(),
            \Filament\Tables\Columns\TextColumn::make('to_do_count')->label('To Do'),
            \Filament\Tables\Columns\TextColumn::make('in_progress_count')->label('In Progress'),
            \Filament\Tables\Columns\TextColumn::make('done_count')->label('Done'),
        ];
    }
}

