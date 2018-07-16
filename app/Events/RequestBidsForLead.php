<?php

namespace App\Events;

use App\Events\Event;
use App\Lead;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestBidsForLead extends Event
{
    use SerializesModels;

    /**
     * @var Lead
     */
    public $lead;

    public $haulers;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Lead $lead, $haulers)
    {
        $this->lead    = $lead;
        $this->haulers = $haulers;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
