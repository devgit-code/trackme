<?php

use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new class extends Component {
    
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 py-5">
            {{ __('Statistics') }}
        </h2>
    </header>

    <div class="flex justify-between gap-x-6 px-5">
        <div class="min-w-0 gap-x-4">
            <p class="mt-1 text-md text-gray-600">Created Tags : {{ auth()->user()->tags()->count() }}</p>
            <p class="mt-1 text-md text-gray-600">Followed Tags : {{ auth()->user()->follows()->count() }}</p>
            <p class="mt-1 text-md text-gray-600">Add Pings : {{ auth()->user()->pings()->count() }} </p>
            <p class="mt-1 text-md text-gray-600">Report Tags : {{ auth()->user()->reports()->count() }}</p>
            <!-- <p class="mt-1 text-md text-gray-600">{{ __('Total Hits: 376') }}</p> -->
        </div>
        <!-- <div class="min-w-0 gap-x-4">
            <p class="mt-1 text-md text-gray-600">{{ __('George Score: 0.00') }}</p>
            <p class="mt-1 text-md text-gray-600">{{ __('Days of Inactivity: 2558') }}</p>
        </div> -->
    </div>
</section>
