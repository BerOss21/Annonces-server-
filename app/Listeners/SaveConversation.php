<?php

namespace App\Listeners;

use App\Events\ConversationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Conversation;

class SaveConversation
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
     * @param  ConversationEvent  $event
     * @return void
     */
    public function handle(ConversationEvent $event)
    {
        $conversation=Conversation::create([
            "annoucement_id"=>$event->data->annoucement_id,
            "user_id"=>$event->data->user_id
        ]);
        $conversation->messages()->save([
            "user_id"=>$event->data->author_id,
            "content"=>$event->data->content
        ]);
    }
}
