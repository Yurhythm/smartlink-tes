<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'view_all',
            'create',
            'update',
            'delete',
            'delete_any',
            'change_status',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('created_by')
                    ->default(fn() => Auth::id()),
                Forms\Components\Select::make('team_id')
                    ->label('Team Filter')
                    ->placeholder('All Teams')
                    ->options(Team::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set) => $set('user_id', null)),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(function (callable $get) {
                        $teamId = $get('team_id');
                        if ($teamId) {
                            return User::where('team_id', $teamId)->pluck('name', 'id');
                        }
                        // If no team selected, show all users
                        return User::pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                // Forms\Components\Select::make('user')
                //     ->relationship('user', 'name')
                //     ->multiple()
                //     ->preload()
                //     ->searchable(),
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(191),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('deadline'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.team.name')
                    ->label('Team')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state) => match ($state) {
                        'to_do' => 'warning',
                        'in_progress' => 'info',
                        'done' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'to_do' => 'To Do',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Project')
                    ->options(Project::all()->pluck('name', 'id'))
                    ->searchable(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\Action::make('comments')
                    ->label('Comment')
                    ->url(fn(Task $record) => route('filament.admin.resources.tasks.comment', ['record' => $record]))
                    ->visible(fn($record) =>  Auth::user()?->can('create_task') || Auth::user()?->can('update_task') || Auth::id() === $record->user_id)
                    // ->icon('heroicon-o-chat-bubble-left')
                    ->color('info'),
                Tables\Actions\Action::make('changeStatus')
                    ->label('Change Status')
                    // ->icon('heroicon-o-pencil')
                    ->visible(fn($record) => Auth::user()?->can('update_task') || (Auth::user()?->can('change_status_task') && Auth::id() === $record->user_id))
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'to_do' => 'To Do',
                                'in_progress' => 'In Progress',
                                'done' => 'Done',
                            ])
                            ->default(fn($record) => $record->status)
                            ->required(),
                    ])
                    ->action(function (Task $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                    })
                    ->modalHeading('Change Task Status')
                    ->modalSubmitActionLabel('Update Status')
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!Auth::user()?->can('view_all_task')) {
            $query->where('user_id', Auth::user()->id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'comment' => Pages\CommentTask::route('/{record}/comments'),
        ];
    }

    public function changeStatus(User $user)
    {
        return $user->can('change_status');
    }

    public function viewAll(User $user)
    {
        return $user->can('view_all');
    }
}
