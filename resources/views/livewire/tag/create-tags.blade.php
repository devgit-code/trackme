<?php

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $tags;

    public function mount()
    {
        $this->tags = auth()->user()->tags;
    }
}; ?>

<div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
    @if ($tags->count() == 0)
        <p>{{ __('You have no tags!') }}<br><br>
            <x-button href="{{ route('create-tag') }}" icon="pencil" primary label="{{ __('Create Tag') }}"
                      wire:navigate />
        </p>
    @else
        @foreach ($tags as $tag)
            <a href="{{ route('view-tag', ['uid' => $tag->id]) }}" wire:key="{{ $tag->id }}" wire:navigate>
                <x-card>
                    <span class="break-words capitalize">
                        {{ $tag->name }}
                    </span>
                    <x-map class="h-[150px]" small="true" :locations="json_encode($tag->getLocations())" />
                    @if ($tag->pings()->first())
                        @if ($tag->pings()->firstWhere('loc_name', '!=', null))
                            <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                                <x-icon name="globe" class="h-6 w-6" />
                                <span>{{ $tag->pings()->firstWhere('loc_name', '!=', null)->loc_name }}</span>
                            </div>
                        @endif
                        <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                            <x-icon name="map" class="h-6 w-6" />
                            <span>
                                {{ $tag->pings()->first()->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endif
                    <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                        <x-icon name="calendar" class="h-6 w-6" />
                        <span>
                            {{ $tag->created_at->diffForHumans() }}
                        </span>
                    </div>
                </x-card>
            </a>
        @endforeach
    @endif
</div>
