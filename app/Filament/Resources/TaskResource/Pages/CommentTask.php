<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Comment;
use App\Models\Task;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CommentTask extends Page
{
    public Task $record;

    public ?string $content = null;

    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.task-resource.pages.comment-task';

    public function mount(Task $record): void
    {
        $this->record = $record->load('project');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('content')
                ->label('Add a Comment')
                ->required()
                ->rows(4),
        ]);
    }

    public function submit(): void
    {
        Comment::create([
            'task_id' => $this->record->id,
            'user_id' => Auth::id(),
            'content' => $this->content,
        ]);

        // $this->task = $this->task->fresh(['project', 'comments', 'comments.user']);

        $this->reset('content');

        Notification::make()
            ->title('Comment added')
            ->success()
            ->send();
    }

    public function getCommentsProperty()
    {
        return $this->record->comments()->latest()->with('user')->get();
    }
}
