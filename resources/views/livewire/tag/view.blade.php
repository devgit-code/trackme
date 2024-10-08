<?php

use App\Models\Tag;
use App\Enums\TagType;
use App\Providers\RouteServiceProvider;

use WireUi\Traits\Actions;
use Livewire\Attributes\Rule;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

new class extends Component {
    use WithPagination;
    use Actions;

    public $uid;
    public Tag $tag;
    public Collection $pings;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('string|max:4096')]
    public string $description = '';

    #[Rule('required')]
    public TagType $type = TagType::Traveller;

    public $pingLocations = [];

    public function mount()
    {
        if (!$this->isStrRandom($this->uid, 8)) { //uid validation
            $this->redirect(route('wrong-tag'), navigate: true);
            return;
        }

        // dd('here'); 
        if (Tag::where('share_code', $this->uid)->first()) {
            $this->tag = Tag::where('share_code', $this->uid)->first();
        } else {
            try {
                $this->tag = Tag::findOrFail($this->uid);
            } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                //register tag
                $this->redirect(route('create-qr-tag', ['uid' => $this->uid]), navigate: true);
                return;
                // if (auth()->user()) {
                // } else {
                //     $this->redirect(session('url.intended', RouteServiceProvider::HOME), navigate: true);
                //     return;
                //     // session()->flash('status', 'sFirst login');
                //     // return $this->redirect(route('login'), navigate: true);
                // }
            }
        }
        $this->name = $this->tag->name;
        $this->description = $this->tag->description;
        $this->type = $this->tag->type;
    }

    public function update(): void
    {
        $this->authorize('update', $this->tag);

        $validated = $this->validate();

        $this->tag->update($validated);
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->tag);
        $this->tag->delete();
        $this->notification()->success($title = 'Tag Deleted!');
        $this->redirect(route('my-tags'), navigate: true);
    }

    private function isStrRandom($string, $length)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $string) && strlen($string) === $length;
    }

    #[On('ping-created')]
    public function refreshPage(): void
    {
        $this->dispatch('refreshComponent');
    }
}; ?>

<div x-data="{ editing: false }">
    <div class="grid grid-cols-1 gap-4 pb-4 sm:grid-cols-6">
        <div class="sm:col-span-6 md:col-span-3 lg:col-span-2">
            @if (!$tag->user)
            <div
                class="flex items-center p-4 border rounded-lg gap-x-3 dark:border-0 shadow-soft bg-blue-50 dark:bg-secondary-700">
                <div class="text-lg text-center font-semibold text-blue-700 dark:text-blue-400 w-full">
                    <span>This tag is unclaimed!</span><br>
                    @auth
                    <x-button class="p-4" wire:click="claim-tag" dark
                        icon="view-grid-add">{{ __('tag.claim') }}</x-button>
                    @endauth
                    @guest
                    <span>Create an account to claim it:</span>
                    <livewire:pages.auth.register />
                    @endguest
                </div>
            </div>
            @endif
            <x-input x-cloak x-show="editing" class="mb-2" wire:model="name" wire:blur="update" />
            <h1 x-show="!editing" class="break-words pb-2 text-2xl font-semibold text-gray-800">
                {{ $name }}
            </h1>
            @if ($tag->user)
            <div class="flex flex-row items-center gap-1 text-gray-500">
                <x-avatar xs :src="$tag->user->gravatar" />
                <span>{{ $tag->user->name }}</span>
            </div>
            @endif
            <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                <x-icon name="calendar" class="h-6 w-6" />
                <span>{{ $tag->created_at->format('M. jS, Y') }}
                    ({{ $tag->created_at->diffForHumans() }})</span>
            </div>
            @if ($tag->pings->first())
            @if ($tag->pings->firstWhere('loc_name', '!=', null))
            <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                <x-icon name="globe" class="h-6 w-6" />
                <span>{{ $tag->pings->firstWhere('loc_name', '!=', null)->loc_name }}</span>
            </div>
            @endif
            <div class="mt-1 flex flex-row items-center gap-1 text-gray-500">
                <x-icon name="map" class="h-6 w-6" />
                <span>{{ $tag->pings->first()->created_at->format('M. jS, Y') }}
                    ({{ $tag->pings->first()->created_at->diffForHumans() }})</span>
            </div>
            @endif
            @can('update', $tag)
            <div class="my-2">
                <x-button x-cloak x-show="editing" x-on:click="editing = !editing" dark icon="arrow-left">Leave Edit
                    Mode</x-button>
                <x-button x-show="!editing" x-on:click="editing = !editing" dark icon="pencil">Edit</x-button>
                <x-button x-show="!editing" :href="route('print-tag', ['uid' => $tag->id])" dark icon="printer" wire:navigate>Print</x-button>
                <x-button x-show="!editing" wire:click="delete" dark icon="trash">Delete</x-button>
            </div>
            @endcan
            <hr class="my-2">
            <x-textarea x-cloak x-show="editing" wire:model.blur="description" wire:blur="update" />
            <p x-show="!editing">{{ $description }}</p>
        </div>
        <x-map class="h-[350px] sm:col-span-6 md:col-span-3 lg:col-span-4" :locations="json_encode($this->tag->getLocations())" />
        <div class="col-span-1 col-start-1 grid gap-3 sm:col-span-4 sm:col-start-2 lg:col-span-4 lg:col-start-3">
            <livewire:ping.create :tag="$tag" />
            <livewire:ping.list :tag="$tag" />
        </div>
    </div>
</div>