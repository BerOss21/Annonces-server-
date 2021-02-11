<?php

namespace App\Listeners;

use App\Events\ViewsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncreaseViews
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ViewsEvent  $event
     * @return void
     */
    public function handle(ViewsEvent $event)
    {
        $annoucement=$event->annoucement;
        $annoucement->views=$annoucement->views+1;
        $annoucement->save();
    }
}
