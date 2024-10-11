<?php

use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

$tags = Tag::all();

?>

<x-app-layout>
    <div class="py-12">
        <x-slot name="title">
            {{ __('Profile') }}
        </x-slot>
        <div class="mx-auto max-w-xl space-y-6 sm:px-6 lg:px-1">
            
            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <div class="max-w-lg">
                    <livewire:profile.statistic />
                </div>
            </div>

            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <div class="max-w-lg">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <div class="max-w-lg">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{--<div class="bg-white shadow sm:rounded-lg sm:p-8">
                <div class="max-w-lg">
                
                    <div class="mt-5">
                        <button id="openModalBtn" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                            Show Statistics
                        </button>
                        <!-- Modal dialog -->
                        <div id="myModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
                            <div class="bg-white rounded-lg w-1/2">
                                <livewire:profile.show-statistics-form  />
                                <div class="flex justify-end p-4">
                                    <button id="closeModalBtn" class="items-center bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">
                                        OK
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Optional script for modal toggle -->
                        <script>
                            const openModalBtn = document.getElementById('openModalBtn');
                            const closeModalBtn = document.getElementById('closeModalBtn');
                            const modal = document.getElementById('myModal');
                            // Show modal
                            openModalBtn.addEventListener('click', () => {
                                modal.classList.remove('hidden');
                            });
                            // Hide modal
                            closeModalBtn.addEventListener('click', () => {
                                modal.classList.add('hidden');
                            });
                        </script>
                    </div>
                </div>
            </div>--}}

            <div class="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                <div class="max-w-lg">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
