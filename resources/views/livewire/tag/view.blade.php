<?php

use App\Models\Tag;
use App\Models\Follow;
use App\Enums\TagType;
use App\Providers\RouteServiceProvider;

use WireUi\Traits\Actions;
use Livewire\Attributes\Rule;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Carbon\Carbon;

new class extends Component {
    use WithPagination;
    use Actions;
    use WithFileUploads;
    
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
    
    #[Rule('nullable|file|mimes:png,jpg,jpeg,webp|max:12288')]
    public $image;

    public $img_url = '';
    public $old_img_url = '';

    #[Rule('boolean')]
    public bool $auto_approve = false;

    public bool $is_followed = false;

    public float $distance = 0;


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

        //calculate distance function
        $this->distance = $this->tag->getDistance();

        //check followed item?
        if (auth()->id() != $this->tag->user_id && Follow::where('user_id', auth()->id())->where('tag_id', $this->tag->id)->first()) {
            $this->is_followed = true;
        }

        $this->name = $this->tag->name;
        $this->description = $this->tag->description;
        $this->type = $this->tag->type;
        $this->img_url = $this->tag->img_url;
        $this->auto_approve = $this->tag->auto_approve;
    }

    public function update(): void
    {
        $this->authorize('update', $this->tag);

        $validated = $this->validate();

        if($this->image){
            $timestamp = Carbon::now()->format('Ymd_His');
            $customFileName = auth()->id() . '_' . $timestamp . '.' . $this->image->getClientOriginalExtension();
            $path = $this->image->storeAs('uploads/tags', $customFileName, 'public');
            
            $validated['img_url'] = '/storage/' . $path;
        }else
            $validated['img_url'] = '';

        $this->reset('image');

        $this->tag->update($validated);
        $this->dispatch('ping-created');

    }

    public function delete(): void
    {
        $this->authorize('delete', $this->tag);
        $this->tag->delete();
        $this->notification()->success($title = 'Tag Deleted!');
        $this->redirect(route('my-tags'), navigate: true);
    }

    public function follow(): void
    {
        if(!auth()->user())
        {
            $this->notification()->warning($title = 'Login First!');
            return;
        }

        //valdiated check
        $validated = [
            'tag_id' => $this->tag->id,
            'user_id' => auth()->id(),
        ];

        $new_follow = Follow::create($validated);
        $this->notification()->success($title = 'You followed item!');
        $this->dispatch('ping-created');


//         $validated = [
//             'auto_approve' => !$this->auto_approve,
//         ];

//         $ret = $this->tag->update($validated);
// // dd($this->tag);
//         $this->auto_approve = !$this->auto_approve;
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

<div x-data="{ 
    editing: false, 
    isImgCanceled : false, 
    fnShareCode(str){
        navigator.clipboard.writeText(str)
            .then(() => {
                alert('Share Link Copied to clipboard: ');
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    } 
    }">
    <div class="">
        <div class="grid sm:grid-cols-5 gap-2">
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
            <div x-show="!editing" class="flex justify-between">
                <h1 class="break-words pb-2 text-2xl font-semibold text-gray-800">
                    {{ $name }}
                </h1>

                <div>
                    <x-button sm :href="route('print-tag', ['uid' => $tag->id])" icon="printer" sky wire:navigate></x-button>
                    <x-button sm @click="fnShareCode('{{env('APP_URL') . '/t/'. $tag->share_code}}')" green icon="link"/>
                </div>
            </div>

            @if ($tag->user)
            <div class="mt-3 flex flex-row items-center gap-1 text-gray-500">
                <span class="font-medium text-gray-700 dark:text-gray-400">Created by:</span>
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
            <div class="mt-5 flex flex-row items-center gap-1 text-gray-500">
                <span class="font-medium text-gray-700 dark:text-gray-400">Last found:</span>
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
            <p x-show="!editing" class="mt-5">
                <span class="font-medium text-gray-700 dark:text-gray-400 mr-1">Distance traveled : </span>{{ round($this->distance, 2)  }}km
            </p>

            <div class="my-2">
                @can('update', $tag)
                <x-button x-cloak x-show="editing" x-on:click="editing = !editing" dark icon="arrow-left">Leave Edit
                    Mode</x-button>
                <x-button x-show="!editing" x-on:click="editing = !editing" dark icon="pencil">Edit</x-button>
                <x-button x-show="!editing" wire:click="delete" dark icon="trash">Delete</x-button>
                @else
                @if(!$this->is_followed)
                <x-button wire:click="follow" primary>
                    <span class="mr-2">
                        <x-icon name="heart" class="w-5 h-5" outlined />
                    </span>
                    {{ __('Follow Item') }}
                </x-button>
                @else
                <x-button wire:click="follow" disabled primary>
                    <span class="mr-2">
                        <x-icon name="heart" class="w-5 h-5" solid />
                    </span>
                    {{ __('Followed') }}
                </x-button>
                @endif
                @endcan
            </div>
            <hr class="my-2">
            <x-textarea x-cloak x-show="editing" wire:model.blur="description" wire:blur="update" />
            <div x-show="editing" class="my-2">
                <x-toggle label="{{ __('tag.auto-approve') }}" wire:model.blur="auto_approve" wire:blur="update" />
            </div>
            <p x-show="!editing">{{ $description }}</p>

            @if($img_url != '')
            <div x-show="!editing" class="my-1">
                <img src="{{ asset($img_url) }}" class="inset-0 w-64 object-cover" alt="Tag image" />
            </div>
            <div x-show="editing" class="my-1">
                <img src="{{ asset($img_url) }}" class="inset-0 w-16 object-cover" alt="Tag image" />
            </div>
            @endif
            <div x-show="editing" class="my-2">
                <x-file-uploader  wire:model.blur="image" :file="$image" rules="mimes:jpeg,png,webp" wire:blur="update" />
            </div>
        </div>
        <div class="">
            @can('update', $tag)
                <x-map class="h-[350px] mb-2" :locations="json_encode($this->tag->getLocations(1))" />
            @else
                <x-map class="h-[350px] mb-2" :locations="json_encode($this->tag->getLocations())" />
            @endif
            <div class="sm:col-span-4 sm:col-start-2 lg:col-span-4 lg:col-start-3">
                <livewire:ping.create :tag="$tag" />
                <livewire:ping.list :tag="$tag" />
            </div>
        </div>
    </div>
</div>