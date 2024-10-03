<?php

use App\Models\Ping;
use App\Enums\TagType;
use Livewire\Attributes\Rule;
use WireUi\Traits\Actions;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Request;

new class extends Component {
    use Actions;

    public Ping $ping;

    #[Rule('required|max:4096')]
    public $comment;

    #[Rule('nullable|numeric')]
    public $lat;

    #[Rule('nullable|numeric')]
    public $lon;

    #[Rule('nullable|numeric|max:10000')]
    public $accuracy;

    protected $loc_name;

    public $addCommentModal;
    public $trackLocation = false;

    public function mount(): void
    {
        $this->comment = $this->ping->comment;
        $this->lat = $this->ping->lat;
        $this->lon = $this->ping->lon;
    }

    public function update(): void
    {
        dd('here');
        $this->authorize('update', $this->ping);

        $validated = $this->validate();

        $validated['ip_address'] = Request::ip();

        if (auth()->user()) {
            $this->ping->update($validated);
            $this->dispatch('ping-created');
        } else {
            // Create unverified account for commenter
        }
        $this->comment = '';
    }
}; ?>
<form wire:submit="update">
    <div class="flex flex-col gap-2">
        <x-textarea wire:model="comment" placeholder="{{ __('I found this at the park!!') }}" />
        <x-toggle md :label="__('ping.edit-location')" x-data="{ lat: $wire.entangle('lat', true), lon: $wire.entangle('lon', true), accuracy: $wire.entangle('accuracy', true) }" wire:model="trackLocation"
                  x-on:change="requestLocation($el, $data, $wire)" />
        <span class="block text-sm text-gray-400">
            @if ($trackLocation)
                @if ($accuracy > 10000)
                    <span class="block text-sm text-red-400">Insufficient Accuracy:
                        {{ round($accuracy / 1000, 2) }}km (need &lt;10km) </span>
                @else
                    <span class="block text-sm text-gray-400">
                        Current Location: {{ round($lat, 4) }}, {{ round($lon, 4) }}
                        ({{ round($accuracy / 1000, 2) }}km acc.)
                    </span>
                @endif
            @endif
        </span>
        <x-button spinner="update" wire:click="update" sm label="Save" :disabled="$accuracy > 10000" icon="pencil" primary />
    </div>

</form>
