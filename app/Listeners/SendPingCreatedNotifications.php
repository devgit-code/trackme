<?php

namespace App\Listeners;

use App\Events\PingCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\NewPing;

class SendPingCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PingCreated $event): void
    {
        $event->ping->tag->user->notify(new NewPing($event->ping));
    }
}
