<?php

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {
    public Collection $followedTags;

    public function mount()
    {
        $this->followedTags = auth()->user()->follows()->with('tag')->get();
        // dd($this->tags->count());
        // auth()->user()->follows()->with('tag')->get()
    }
}; ?>

<div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
    @if ($followedTags->count() == 0)
        <p>{{ __('You have no tags!') }}<br><br>
        </p>
    @else
        @foreach ($followedTags as $follow)
            <a href="{{ route('view-tag', ['uid' => $follow->tag->id]) }}" wire:key="{{ $follow->tag->id }}" wire:navigate>
                <x-card>
                    <span class="break-words">
                        {{ $follow->tag->name }}
                    </span>
                    <x-map class="h-[150px]" small="true" :locations="json_encode($follow->tag->getLocations())" />
                    @if ($follow->tag->pings()->first())
                        @if ($follow->tag->pings()->firstWhere('loc_name', '!=', null))
                            <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                                <x-icon name="globe" class="h-6 w-6" />
                                <span>{{ $follow->tag->pings()->firstWhere('loc_name', '!=', null)->loc_name }}</span>
                            </div>
                        @endif
                        <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                            <x-icon name="map" class="h-6 w-6" />
                            <span>
                                {{ $follow->tag->pings()->first()->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @endif
                    <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                        <x-icon name="calendar" class="h-6 w-6" />
                        <span>
                            {{ $follow->tag->created_at->diffForHumans() }}
                        </span>
                    </div>
                </x-card>
            </a>
        @endforeach
    @endif
</div>
