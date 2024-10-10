<?php

use App\Models\Tag;
use App\Enums\TagType;
use Livewire\Attributes\Rule;
use WireUi\Traits\Actions;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Request;
use Livewire\WithFileUploads;
use Carbon\Carbon;

new class extends Component {
    use Actions;
    use WithFileUploads;

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

    protected $loc_name;
    protected $ip_address;

    public $visible = false;
    public $trackLocation = false;

    #[Rule('nullable|file|mimes:png,jpg,jpeg,webp|max:12288')]
    public $image;

    public function requestLogin(): void
    {
        $this->notification()->warning($title = 'For comment, please Login!');
    }

    public function save(): void
    {
        $this->authorize('create', $this->tag);

        $validated = $this->validate();

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
            dd("Error occurred");
        }

        $data = json_decode($response, true);
        if (!empty($data[0]['Errors'])) {
            $this->notification()->error($title = 'Wrong Zip code!');
            // $this->dispatch('ping-created');
        }

        $validated['lat'] = $data[0]['Coords']['Lat'];
        $validated['lon'] = $data[0]['Coords']['Lon'];
        $validated['accuracy'] = 10000;
        //** end of get code */

        if (auth()->user()) {
            if($this->image){
                $timestamp = Carbon::now()->format('Ymd_His');
                $customFileName = auth()->id() . '_' . $timestamp . '.' . $this->image->getClientOriginalExtension();
                $path = $this->image->storeAs('uploads/pings', $customFileName, 'public');
                
                $validated['img_url'] = '/storage/' . $path;
            }else
                $validated['img_url'] = '';

            $this->tag->pings()->create(
                array_merge($validated, [
                    'user_id' => auth()->id(),
                    'ip_address' => Request::ip(),
                    'loc_name' => 'Unknown',
                    'is_approved' => $this->tag->auto_approve,
                ]),
            );

            $this->reset('image');

            $this->visible = false;
            $this->notification()->success($title = 'Comment saved!');
            $this->dispatch('ping-created');
        } else {
            // Create unverified account for commenter
        }
        $this->comment = '';
    }
}; ?>
<form wire:submit="save" x-on:ping-created="open = false" x-data="{ open: false }">
    <div x-cloak x-show="open" class="flex flex-row gap-2 rounded border p-2">
        <div>
            @auth
            <x-avatar class="mt-6 hidden shadow-md sm:flex" lg squared :src="auth()->user()->gravatar" />
            @else
            <x-avatar class="mt-6 hidden shadow-md sm:flex" lg squared />
            @endauth
        </div>
        <div class="flex flex-grow flex-col gap-2">
            @guest
            <x-input wire:model="name" :label="__('ping.name')" required autofocus placeholder="{{ __('Jane Doe') }}"
                autocomplete="name" />
            <x-input wire:model="email" :label="__('ping.email')" required placeholder="{{ __('jane.doe@gmail.com') }}"
                autocomplete="email" />
            @endguest

            <x-textarea wire:model="comment" required autofocus :label="__('ping.comment')"
                placeholder="{{ __('I found this at the park!!') }}" />

            <x-input wire:model="code" required :label="__('ping.code')" />
            <x-file-uploader wire:model="image" :file="$image" rules="mimes:jpeg,png,webp"/>

            <div class="flex flex-row flex-wrap content-center justify-between gap-4">
                <div class="grid content-center">
                    <x-toggle md :label="__('ping.new-location')" x-data="{ lat: $wire.entangle('lat', true), lon: $wire.entangle('lon', true), accuracy: $wire.entangle('accuracy', true) }" wire:model="trackLocation"
                        x-on:change="requestLocation($el, $data)" />
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

                </div>

                <div class="flex flex-wrap items-end gap-2">
                    <x-button flat label="Cancel" spinner="visible" x-on:click="open = false" />
                    <x-button type="submit" primary spinner="save" :disabled="$accuracy > 10000" icon="chat-alt"
                        :label="__('ping.create')" />
                </div>
            </div>
        </div>
    </div>

    @if(!auth()->user())
    <x-button class="w-full" outline primary icon="chat-alt" label="{{ __('ping.create') }}" wire:click="requestLogin"/>
    @else
    <x-button class="w-full" x-on:click="open = true" x-show="!open" outline primary icon="chat-alt"
        label="{{ __('ping.create') }}" />
    @endif
</form>