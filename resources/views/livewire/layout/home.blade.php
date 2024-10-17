<?php

use App\Models\Tag;
use App\Models\Ping;
use App\Enums\TagType;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;

new class extends Component {
    use WithPagination;
    use Actions;

    protected $uid;
    protected $listeners = ['refreshComponent' => '$refresh'];

    public Tag $tag;
    public $pings;

    public $name = '';
    public $message = '';

    public Tag $selectedTag;
    public $sample_data;
    public $totalDistance = 0;

    public $is_tag_null = false;

    public function mount(){
        $latestPing = Ping::latest('created_at')->first();
        if($latestPing)
        {
            $this->selectedTag = $latestPing->tag;
            $this->sample_data = json_encode($this->selectedTag->getLocations());

        }else{
            // $this->selectedTag = Tag();
            $this->is_tag_null = true;
            $this->sample_data = '[[35.306389,-78.323889,"Isaac Alich\n4 days ago"], [35.53965,-82.55095,"John Smith\n2 weeks ago"], [36.085336,-80.241745,"Jane Doe\n1 month ago"]]';
        }

        $tags = Tag::all();
        foreach($tags as $tag)
        {
            $this->totalDistance += $tag->getDistance();
        }
    }

    public function selectTag($tag_id)
    {
        $this->selectedTag = Tag::find($tag_id); // Update the selected user based on the ID
        $this->sample_data = json_encode($this->selectedTag->getLocations());
        $this->dispatch('refreshComponent');
    }

    public function pingsPaginated($mode = 0)
    {
        if ($mode == 1) {
            return $this->tag->pings()->paginate(10);
        }
        return $this->tag->pings()->where('is_approved', 1)->paginate(10);
    }

    #[On('ping-created')]
    public function refreshPage(): void
    {
        $this->dispatch('refreshComponent');
    }
}; ?>

<div class="flex flex-col gap-3 my-2">
    <div class="">
        <div class="mb-4 grid sm:grid-cols-2 gap-2 text-orange-400/90">
            <div class="text-center">
                <p class="text-4xl font-bold mb-2">{{Tag::count()}}</p>
                <span class="text-md font-medium text-gray-400">Created Tag </span>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold mb-2">{{round($this->totalDistance, 2)}}<span class="text-lg font-medium"> km</span></p>
                <span class="text-md font-medium text-gray-400">Travel distance </span>
            </div>
        </div>
        
        <x-map class="mt-2 h-[350px]" :locations="$this->sample_data" />

        @php
        $latestPings = Ping::where('is_approved', 1)
                            ->select('pings.*')
                            ->whereIn('id', function($query){
                                                $query->selectRaw('MAX(id)')
                                                    ->from('pings')
                                                    ->where('is_approved', 1)
                                                    ->groupBy('tag_id');
                                                })
                            ->with('tag')
                            ->orderBy('created_at', 'desc')
                            ->limit(3)
                            ->get();
        @endphp

        <div>
            @if($this->is_tag_null)
                <p class="py-6 text-center">{{__('No tracked tags. You will be the first person!')}}</p>
            @endif

            @if($selectedTag)
            <div class="overflow-x-auto mt-5 ">
                @foreach($latestPings as $ping)
                <div x-data wire:click="selectTag('{{ $ping->tag->id }}')" wire:key="tag-{{ $ping->tag->id }}" class="block max-w p-4 mb-2 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <div class="flex">
                        <a href="{{env('app_url') . '/t/' . $ping->tag->id}}" class="inline text-lg uppercase font-bold mr-2 hover:text-blue-400">{{$ping->tag->name}}</a>
                        <span class="text-md font-medium text-gray-400 ">({{$ping->created_at->diffForHumans()}})</span>
                    </div>
                    <div class="flex justify-between">
                        <p class="inline text-md font-medium text-gray-500">{{$ping->tag->description}}</p> 
                        <span>{{round($ping->tag->getDistance(), 2) }} km</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>