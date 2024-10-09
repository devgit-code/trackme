<?php

use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

new class extends Component {
    use WithFileUploads;
    public $uid;

    // #[Rule('required|string|max:255')]
    // public string $email = '';

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('string|max:4096')]
    public string $description = '';

    #[Rule('required')]
    public TagType $type = TagType::Traveller;

    #[Rule('boolean')]
    public bool $auto_approve = false;

    #[Rule('nullable|file|mimes:png,jpg,jpeg,webp|max:12288')]
    public $image;

    public function save(): void
    {
        $validated = $this->validate();
 
        if($this->image){
            $timestamp = Carbon::now()->format('Ymd_His');
            $customFileName = 'file_' . $timestamp . '.' . $this->image->getClientOriginalExtension();
            $path = $this->image->storeAs('uploads/tags', $customFileName, 'public');
            
            $validated['img_url'] = '/storage/' . $path;
        }else
            $validated['img_url'] = '';

        $validated['id'] = $this->uid;

        $this->reset('image');

        $new_tag = auth()
            ->user()
            ->tags()
            ->create($validated);

        $this->redirect(route('view-tag', ['uid' => $new_tag->id]), navigate: true);
    }
}; ?>

<form wire:submit="save">
    <div class="grid gap-4">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tag Type:</span>
        <div class="grid gap-1">
            <x-radio id="tagtype-{{ TagType::Traveller }}" :label="__('tag.traveller')" value="{{ TagType::Traveller }}"
                wire:model.defer="type" />
            <x-radio id="tagtype-{{ TagType::LostAndFound }}" :label="__('tag.lost-and-found')" value="{{ TagType::LostAndFound }}"
                wire:model.defer="type" />
        </div>

        <x-input wire:model="name" :label="__('tag.name')" required autofocus placeholder="{{ __('Isaac\'s $2 Bill') }}"
            autocomplete="name" />

        <x-textarea wire:model="description" :label="__('tag.description')"
            placeholder="{{ __('This is my prized $2 bill, it has been handed down between many generations.') }}" />

        <x-toggle label="{{ __('tag.auto-approve') }}" wire:model="auto_approve" />

        <x-file-uploader wire:model="image" :file="$image" rules="mimes:jpeg,png,webp"/>


        @csrf
        @if(!auth()->user())
        <x-button wire:click="save" spinner="save" primary :label="__('tag.create')" disabled />
        <span style="color:red">For register new Item, please login!</span><br>
        @else
        <x-button wire:click="save" spinner="save" primary :label="__('tag.create')" />
        @endif

    </div>
</form>