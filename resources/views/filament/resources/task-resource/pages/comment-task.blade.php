<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Project and Task Info -->
        <div class="p-4 bg-white rounded shadow-sm border space-y-1">
            <div class="text-sm text-gray-500 uppercase font-semibold tracking-wide">Project</div>
            <div class="text-lg font-bold text-primary-600">{{ $this->record->project->name ?? 'N/A' }}</div>

            <div class="text-sm text-gray-500 uppercase font-semibold tracking-wide mt-4">Task</div>
            <div class="text-lg font-medium text-gray-800">{{ $this->record->title?? 'N/A' }}</div>
        </div>

        <!-- Comments Section -->
        <div class="space-y-4">
            <div class="text-lg font-semibold text-gray-700 border-b pb-2">Comments</div>

            @forelse ($this->comments as $comment)
                <div class="p-4 bg-gray-100 rounded space-y-1">
                    <div class="text-sm text-gray-700 font-semibold">{{ $comment->user->name }}</div>
                    <div class="text-gray-800">{{ $comment->content }}</div>
                    <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="text-gray-500 italic">No comments yet.</div>
            @endforelse
        </div>

        <!-- Comment Form -->
        <form wire:submit.prevent="submit" class="space-y-4">
            {{ $this->form }}

            <x-filament::button type="submit" wire:target="submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">Add Comment</span>
                <span wire:loading wire:target="submit">Submitting...</span>
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
