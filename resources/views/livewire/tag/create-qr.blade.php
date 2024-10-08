<?php

use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public $uid;

    // #[Rule('required|string|max:255')]
    // public string $email = '';

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('string|max:4096')]
    public string $description = '';

    #[Rule('required')]
    public TagType $type = TagType::Traveller;

    public function save(): void
    {
        $validated = $this->validate();
        $validated['id'] = $this->uid;

        $new_tag = auth()
            ->user()
            ->tags()
            ->create($validated);

        $this->redirect(route('view-tag', ['uid' => $new_tag->id]), navigate: true);
    }
}; ?>

<form wire:submit="save">
    <div class="grid gap-4">
        <x-input wire:model="name" :label="__('tag.name')" required autofocus placeholder="{{ __('Isaac\'s $2 Bill') }}"
            autocomplete="name" />

        <x-textarea wire:model="description" :label="__('tag.description')"
                    placeholder="{{ __('This is my prized $2 bill, it has been handed down between many generations.') }}" />

        <div class="grid gap-1">
            <x-radio id="tagtype-{{ TagType::Traveller }}" :label="__('tag.traveller')" value="{{ TagType::Traveller }}"
                wire:model.defer="type" />
            <x-radio id="tagtype-{{ TagType::LostAndFound }}" :label="__('tag.lost-and-found')" value="{{ TagType::LostAndFound }}"
                wire:model.defer="type" />
        </div>

        @csrf
        @if(!auth()->user())
        <x-button wire:click="save" spinner="save" primary :label="__('tag.create')" disabled />
        <span style="color:red">For register new Item, please login!</span><br>
        @else
        <x-button wire:click="save" spinner="save" primary :label="__('tag.create')" />
        @endif

    </div>
</form>