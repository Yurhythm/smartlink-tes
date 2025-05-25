<?php

namespace App\Livewire\Filament\Pages\Dashboard;

use App\Models\Project;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserReportTable extends Component implements HasTable, HasForms
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }


    public static function table(Table $table): Table
    {

        return $table
            ->query(function ($livewire) {


                $query = User::query();
                $filter = $livewire->getTableFilterState('project')['value'] ?? null;
                if ($filter != null) {
                    $query->withCount([
                        'tasks as to_do_count' => fn($q) => $q->where('status', 'to_do')->when($filter, fn($q) => $q->where('project_id', $filter)),
                        'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress')->when($filter, fn($q) => $q->where('project_id', $filter)),
                        'tasks as done_count' => fn($q) => $q->where('status', 'done')->when($filter, fn($q) => $q->where('project_id', $filter)),
                    ]);
                } else {
                    $query->withCount([
                        'tasks as to_do_count' => fn($q) => $q->where('status', 'to_do'),
                        'tasks as in_progress_count' => fn($q) => $q->where('status', 'in_progress'),
                        'tasks as done_count' => fn($q) => $q->where('status', 'done'),
                    ]);
                }

                return $query;
            })
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->label('User')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('to_do_count')->label('To Do')->formatStateUsing(fn($state) => $state ?? 0),
                \Filament\Tables\Columns\TextColumn::make('in_progress_count')->label('In Progress')->formatStateUsing(fn($state) => $state ?? 0),
                \Filament\Tables\Columns\TextColumn::make('done_count')->label('Done')->formatStateUsing(fn($state) => $state ?? 0),
            ])
            ->filters([
                SelectFilter::make('project')
                    ->label('Project')
                    ->relationship('tasks.project', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([])
            ->bulkActions([]);
    }
}
