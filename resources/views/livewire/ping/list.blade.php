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
        if($mode == 1)
        {
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
    @can('update', $tag)
        @foreach ($this->pingsPaginated(1) as $ping)
            <livewire:ping.item :ping="$ping" :is_owned="$tag->user_id == auth()->id()">
        @endforeach
        {{ $this->pingsPaginated()->links() }}
    @else
        @foreach ($this->pingsPaginated() as $ping)
            <livewire:ping.item :ping="$ping" :is_owned="$tag->user_id == auth()->id()">
        @endforeach
        {{ $this->pingsPaginated()->links() }}
    @endcan
</div>
