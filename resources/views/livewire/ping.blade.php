<?php

use App\Models\Tag;
use App\Models\Ping;
use App\Models\Report;
use App\Enums\TagType;
use Livewire\Attributes\Rule;
use WireUi\Traits\Actions;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Request;

new class extends Component {
    use Actions;

    public Ping $ping;
    public Tag $tag;

    #[Rule('max:255')]
    public $name;

    #[Rule('required|max:4096')]
    public $comment;

    #[Rule('nullable|numeric|max:1000000')]
    public $code;

    #[Rule('nullable|numeric')]
    public $lat;

    #[Rule('nullable|numeric')]
    public $lon;

    #[Rule('nullable|numeric|max:10000')]
    public $accuracy;

    public bool $creating = false;

    protected $loc_name;
    protected $ip_address;

    public $visible = false;
    public $trackLocation = false;

    public function mount(): void
    {
        if (!$this->creating) {
            $this->comment = $this->ping->comment;
            $this->lat = $this->ping->lat;
            $this->lon = $this->ping->lon;
        }
    }

    public function update(): void
    {
        $this->authorize('update', $this->ping);

        $validated = $this->validate();

        $validated['ip_address'] = Request::ip();

        if($validated['code'] == "") //miss zip code validation
            dd('validation error');
 //** */ start of get lat,lon from zipcode
        $url = "https://pcmiler.alk.com/apis/rest/v1.0/Service.svc/locations?country=US&postcode=" . $validated['code'];

        $options = [
            'http' => [
                'header' => "Authorization: 2D3C139AAA563B4495A0A1C786664F0B\r\n",
                'method' => 'GET'
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // Check for errors
        if ($response === FALSE) {
            dd( "Error occurred");
        }

        $data = json_decode($response, true);
        if(!empty($data[0]['Errors'])){
            dd( "Wrong Zip code");
        }

        $validated['lat'] = $data[0]['Coords']['Lat'];
        $validated['lon'] = $data[0]['Coords']['Lon'];
        $validated['accuracy'] = 10000;
        //** end of get code */

        if (auth()->user()) {
            $this->ping->update($validated);
            $this->dispatch('ping-created');
        } else {
            // Create unverified account for commenter
        }
        $this->comment = '';
    }

    public function report(Ping $ping): void
    {
        //
        $report = Report::where(['user_id', 'ping_id'], [auth()->user()->id, $this->ping->id]);
        dd($report);
    }

    public function delete(Ping $ping): void
    {
        $this->authorize('delete', $ping);
        $ping->delete();
        $this->notification()->success($title = 'Comment deleted!');
        $this->dispatch('ping-created');
    }
}; ?>
<div class="flex flex-row gap-3" x-data="{ editing: false }">
    <div>
        <x-avatar class="mt-0.5 hidden shadow-md sm:flex" lg squared :src="$ping->user->gravatar" />
    </div>
    <x-card color="border border-gray-200" :wire:key="$ping->id">
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
    <form x-cloak x-show="editing" wire:submit="update" x-data="{ open: false }">
        <div class="flex flex-col gap-2">
            <x-textarea wire:model="comment" placeholder="{{ __('I found this at the park!!') }}" />
            <x-input wire:model="code" required :label="__('ping.code')"/>
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
            <x-button spinner="update" wire:click="update" sm label="Save" :disabled="$accuracy > 10000" icon="pencil"
                      primary />
        </div>
    </form>
    <p x-show="!editing">{{ $ping->comment }}</p>
    <x-slot name="action">
        <div class="flex gap-1.5">
            @if ($ping->hasLocation)
                <x-badge sm icon="globe" outline zinc label="{{ $ping->loc_name }}" />
            @endif
            @can('delete', $ping)
                <x-button sm alt="Delete Comment" icon="trash" outline negative wire:click="delete" />
            @endcan
            <x-button x-cloak x-show="editing" x-on:click="editing = false" sm alt="Cancel"
                      class="ring-2 ring-offset-2" icon="x" primary />
            @can('update', $ping)
                <x-button x-show="!editing" x-on:click="editing = true" sm alt="Edit Comment" icon="pencil" primary />
            @endcan
            @cannot('update', $ping)
                <x-button wire:click="report" sm alt="Report this" icon="thumb-down" warning />
            @endcannot
        </div>
    </x-slot>
</x-card>
</div>
