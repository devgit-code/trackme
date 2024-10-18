<?php

use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;
use WireUi\Traits\Actions;

new class extends Component {
    use Actions;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('string|max:4096')]
    public string $description = '';

    #[Rule('required')]
    public TagType $type = TagType::Traveller;

    public int $amount = 1;

    public function save(): void
    {
        $validated = $this->validate();
        $validated['img_url'] = '';

        for ($i = 0; $i < $this->amount; $i++) {
            $validated['id'] = Str::random(8);
            auth()->user()->tags()->create($validated);
        }

        $this->notification()->success($title = 'Bulk Tags Created!');
        // $this->redirect(route('view-tag', ['uid' => $new_tag->id]), navigate: true);
    }
}; ?>

<form wire:submit="save">
    <div class="grid gap-4">
        <div><x-inputs.number min="1" wire:model="amount" label="Amount" /></div>

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
        <x-button wire:click="save" spinner="save" primary :label="__('tag.create')" />
    </div>
</form>
