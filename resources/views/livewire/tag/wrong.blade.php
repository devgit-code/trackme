<?php

use App\Enums\TagType;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component {
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('string|max:4096')]
    public string $description = '';

    #[Rule('required')]
    public TagType $type = TagType::Traveller;

    public function save(): void
    {
        $validated = $this->validate();
        $validated['id'] = Str::random(8);

        $new_tag = auth()
            ->user()
            ->tags()
            ->create($validated);

        $this->redirect(route('view-tag', ['uid' => $new_tag->id]), navigate: true);
    }
}; ?>

<form wire:submit="save">
    <div class="grid gap-4">
    Your Item is not recognized
    </div>
</form>
