<?php

use App\Models\Tag;
use App\Enums\TagType;
use WireUi\Traits\Actions;
use Livewire\Attributes\Rule;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

new class extends Component {
    public $uid;
    public Tag $tag;

    public TagType $type = TagType::Traveller;

    public function mount()
    {
        $this->tag = Tag::findOrFail($this->uid);
    }
}; ?>
<div x-data="{ layout: 'vertical', text: 'I\'m travelling! Scan me to track my journey!', show_name: true, show_author: true, show_created: true, scale: 100, amount: 5 }">
    <div class="flex flex-col gap-4">
        <div class="flex flex-row gap-4">
            <div class="flex flex-grow flex-col gap-1">
                <x-textarea label="Description Text" x-model="text" />
            </div>
            <div class="flex flex-col gap-1">
                <x-checkbox label="Show Name" name="show_name" x-model="show_name" />
                <x-checkbox label="Show Author" name="show_author" x-model="show_author" />
                <x-checkbox label="Show Created Date" name="show_created" x-model="show_created" />
                <x-radio label="Horizontal Layout" value="horizontal" x-model="layout" />
                <x-radio label="Vertical Layout" value="vertical" x-model="layout" />
            </div>
        </div>
        <div class="flex flex-row gap-2">
            <div><x-inputs.number min="10" max="1000" x-model="scale" label="Scale" /></div>
            <div><x-inputs.number min="1" x-model="amount" label="Amount" /></div>
            <x-button md x-on:click="printElement($refs.printable)" class="w-1/3" icon="printer">Print</x-button>
        </div>
        <div class="columns-full" x-ref="printable">
            <template x-for="i in amount" :key="i">
                <div :style="{ transform: 'scale(' + scale + '%)' }"
                     class="m-1 inline-flex overflow-clip rounded border-2 border-dashed bg-white"
                     :class="layout == 'horizontal' ? 'flex-row max-w-96 w-96 max-h-40 h-40' :
                         'flex-col max-w-40 w-40 max-h-80 h-80'">
                    <img class="h-auto" src="data:image/png;base64, {!! base64_encode(
                        QrCode::format('png')->size(512)->style('round')->generate(route('view-tag', $this->tag->share_code)),
                    ) !!} ">
                    <div class="flex min-h-0 min-w-0 flex-col justify-center text-gray-700">
                        {{-- {{ route('view-tag', $this->tag->share_code) }} --}}
                        <h1 x-show="show_name" class="mx-2 break-words text-2xl font-semibold text-gray-800">
                            {{ $tag->name }}
                        </h1>
                        <span class="mx-2" x-show="show_author">{{ $tag->user->name }}</span>
                        <span class="mx-2" x-show="show_created">{{ $tag->created_at->format('M jS, Y') }}</span>
                        <span class="mx-2 leading-tight" x-text="text"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
<script>
    function printElement(e) {
        let cloned = e.cloneNode(true);
        document.body.appendChild(cloned);
        cloned.classList.add("printable");
        window.print();
        document.body.removeChild(cloned);
    }
</script>
