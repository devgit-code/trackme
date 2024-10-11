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

    public Tag $tag;
    public $pings;

    public $name = '';
    public $message = '';

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

<div class="flex flex-col gap-3 mt-5">
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <p><span class="text-sm font-medium text-gray-700 dark:text-gray-400">Total track Items : </span>{{Tag::count()}}</p>

        </div>
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
            ->get();
        $latestPing = Ping::latest('created_at')->first();
        if($latestPing)
        {
            $tag = Tag::find($latestPing->tag_id);
            $sample_data = json_encode($tag->getLocations());
        }else
        {
            $sample_data = '[[35.306389,-78.323889,"Isaac Alich\n4 days ago"], [35.53965,-82.55095,"John Smith\n2 weeks ago"], [36.085336,-80.241745,"Jane Doe\n1 month ago"]]';
        }
        @endphp
        <x-map class="h-[350px]" :locations="$sample_data" />

    </div>
</div>