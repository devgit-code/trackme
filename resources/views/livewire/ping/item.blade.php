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
    public bool $is_owned;

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

    #[Rule('nullable|file|mimes:png,jpg,jpeg,webp|max:12288')]
    public $image;
    
    public $img_url = '';

    public function mount(): void
    {
        if (!$this->creating) {
            $this->comment = $this->ping->comment;
            $this->lat = $this->ping->lat;
            $this->lon = $this->ping->lon;
            $this->img_url = $this->ping->img_url;
        }
    }

    public function approve(): void
    {
        $this->authorize('update', $this->ping);
        $validated = $this->validate();

        $validated['is_approved'] = 1;
        $validated['accuracy'] = 10000;

        if (auth()->user()) {
            $this->ping->update($validated);
            // $this->dispatch('ping-created');
        } else {
            // Create unverified account for commenter
        }
        $this->notification()->success($title = 'Comment approved!');
        $this->dispatch('ping-created');
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
            // $this->dispatch('ping-created');
        } else {
            // Create unverified account for commenter
        }
        $this->comment = '';
    }

    public function report(): void
    {
        //
        // $exists = Report::where('user_id', auth()->user()->id)->where('ping_id', $this->ping->id)->exists();
        $exists = Report::where('user_id', auth()->user()->id)
                        ->where('ping_id', $this->ping->id)
                        ->exists();

        if($exists){
            $this->notification()->warning($title = 'Already reported!');
            // $this->dispatch('ping-created');
        }else{
            $count = Report::where('ping_id', $this->ping->id)->count();

            $new = Report::create([
                'user_id' => auth()->user()->id,
                'ping_id' => $this->ping->id,
                // 'reason' => 'spam'
            ]);
            $this->notification()->success($title = 'Reported successfully!');

            if($count == 2)
            {
                $this->ping->delete();
                // $this->notification()->warning($title = 'This comment removed!');
                $this->dispatch('ping-created');
            }
        }
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->ping);
        $this->ping->delete();
        $this->notification()->success($title = 'Comment deleted!');
        $this->dispatch('ping-created');
    }
}; ?>
<div class="flex flex-row gap-3" x-data="{ editing: false }">
    <div>
        <x-avatar id="openstatisticsModal" class="mt-0.5 hidden shadow-md sm:flex" lg squared :src="$ping->user->gravatar" />
        <!-- Modal dialog -->
        <div id="statisticsModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-1/2 p-6">
                <livewire:profile.show-statistics-form  />
                <div class="mt-4 flex justify-end">
                    <!-- Cancel Button -->
                    <button id="closestatisticsModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2">Close</button>
                </div>
            </div>
        </div>
    </div>
    <x-card color="border border-gray-200" :wire:key="$ping->id">
        <x-slot name="title">
            <div class="flex gap-1.5">
                <!-- Modal dialog -->
                <div id="msgModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
                        <h2 class="text-xl font-semibold mb-4">Send a Message</h2>

                        <input type="text" id="messageTitle" class="w-full p-2 mb-4 border border-gray-300 rounded-md" placeholder="Subject">
                        <!-- Textarea -->
                        <textarea id="messageText" rows="4" class="w-full p-2 border border-gray-300 rounded-md" placeholder="Enter your message"></textarea>

                        <!-- Modal Buttons -->
                        <div class="mt-4 flex justify-end">
                            <!-- Cancel Button -->
                            <button id="closemsgModal" class="bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2">Cancel</button>

                            <!-- Confirm Button -->
                            <button id="confirmMessage" class="bg-blue-500 text-white px-4 py-2 rounded">Send</button>
                        </div>
                    </div>
                </div>
                <!-- Optional script for modal toggle -->
                <script>
                    // Open modal
                    document.getElementById('openmsgModal').addEventListener('click', function() {
                        document.getElementById('msgModal').classList.remove('hidden');
                    });

                    document.getElementById('openstatisticsModal').addEventListener('click', function() {
                        document.getElementById('statisticsModal').classList.remove('hidden');
                    });

                    // Close modal
                    document.getElementById('closemsgModal').addEventListener('click', function() {
                        document.getElementById('msgModal').classList.add('hidden');
                    });
                    document.getElementById('closestatisticsModal').addEventListener('click', function() {
                        document.getElementById('statisticsModal').classList.add('hidden');
                    });

                    // Confirm action
                    document.getElementById('confirmMessage').addEventListener('click', function() {
                        let title = document.getElementById('messageTitle').value;
                        let message = document.getElementById('messageText').value;
                        if (title && message) {
                            // Here you can send the message to the server using an AJAX request or PHP form submission
                            console.log("Title: " + title);
                            console.log("Message sent: " + message);
                            document.getElementById('msgModal').classList.add('hidden'); // Hide modal after confirmation
                        } else {
                            alert("Please enter a message before confirming.");
                        }
                    });
                </script>

                @if($is_owned)
                    @if($ping->is_approved == 0)
                        <x-button wire:click="approve" sm alt="Allow Comment" icon="check-circle" emerald />
                    @else
                        <x-badge sm icon="check" outline zinc label="Allowed" />
                    @endif
                @endif

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
    <div x-show="!editing" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <p class="sm:col-span-2">{{ $ping->comment }}</p>
        @if($img_url != '')
            <div class="sm:col-span-1 flex justify-center">
                <img src="{{ $img_url }}" class="w-16 object-cover" alt="Ping image" />
            </div>
        @endif

    </div>
    <x-slot name="action">
        <div class="flex gap-1.5">
            @if ($ping->hasLocation)
                <x-badge sm icon="globe" outline zinc label="{{ $ping->loc_name }}" />
            @endif
            <x-button x-cloak x-show="editing" x-on:click="editing = false" sm alt="Cancel"
                    class="ring-2 ring-offset-2" icon="x" primary />

            @if (auth()->id() != $ping->user_id)
                <x-button wire:click="report" sm alt="Report this" icon="thumb-down" warning />
            @else
                <x-button sm alt="Delete Comment" icon="trash" outline negative wire:click="delete" />
                <x-button x-show="!editing" x-on:click="editing = true" sm alt="Edit Comment" icon="pencil" primary />
            @endif
        </div>
    </x-slot>
</x-card>
</div>
