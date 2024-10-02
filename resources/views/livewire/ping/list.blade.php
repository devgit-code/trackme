<?php

use App\Models\Tag;
use App\Models\Ping;
use App\Enums\TagType;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

new class extends Component {
    use WithPagination;
    use Actions;

    protected $uid;

    public Tag $tag;
    public $pings;

    public $name = '';
    public $message = '';

    public function pingsPaginated()
    {
        return $this->tag->pings()->paginate(10);
    }

    #[On('ping-created')]
    public function refreshPage(): void
    {
        $this->dispatch('refreshComponent');
    }
}; ?>

<div class="flex flex-col gap-3">
    @foreach ($this->pingsPaginated() as $ping)
        {{-- <div class="flex flex-row gap-3">
            <div>
                <x-avatar class="mt-0.5 hidden shadow-md sm:flex" lg squared :src="$ping->user->gravatar" />
            </div>
            @if ($ping->is($editing))
                @php $padding = 'p-1'; @endphp
            @else
                @php $padding = 'p-4'; @endphp
            @endif
            <x-card color="border border-gray-200" :padding="$padding" :wire:key="$ping->id">
                <x-slot name="title">
                    <div class="flex gap-1.5">
                        <span>{{ $ping->user->name }}</span>
                        <span
                              class="inline-flex items-center text-gray-400">{{ $ping->created_at->diffForHumans() }}@unless ($ping->created_at->eq($ping->updated_at))
                            <x-icon name="pencil" class="h-4 w-4" />
                        @endunless
                    </span>
                </div>
            </x-slot>
            @if ($ping->is($editing))
                <livewire:ping.edit :wire:key="$ping->id" :ping="$ping" />
            @else
                <p>{{ $ping->comment }}</p>
            @endif
            <x-slot name="action">
                <div class="flex gap-1.5">
                    @if ($ping->hasLocation)
                        <x-badge sm icon="globe" outline zinc label="{{ $ping->loc_name }}" />
                    @endif
                    @can('delete', $ping)
                        <x-button sm alt="Delete Comment" icon="trash" outline negative
                                  wire:click="delete({{ $ping->id }})" />
                    @endcan
                    @if ($ping->is($editing))
                        <x-button wire:click="disableEditing" sm alt="Cancel" icon="x" primary />
                    @else
                        @can('update', $ping)
                            <x-button wire:click="edit({{ $ping->id }})" sm alt="Edit Comment" icon="pencil"
                                      primary />
                        @endcan
                    @endif
                </div>
            </x-slot>
        </x-card>
    </div> --}}
        <livewire:ping :ping="$ping">
    @endforeach
    {{ $this->pingsPaginated()->links() }}
</div>
