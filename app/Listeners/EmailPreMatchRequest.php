<?php

namespace App\Listeners;

use App\Events\PreBidMatchRequest;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use WasteMaster\v1\History\HistoryManager;

class EmailPreMatchRequest
{
    /**
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * @var \WasteMaster\v1\History\HistoryManager
     */
    protected $history;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer, HistoryManager $history)
    {
        $this->mailer = $mailer;
        $this->history = $history;
    }

    /**
     * Handle the event.
     *
     * @param  PreBidMatchRequest  $event
     * @return void
     */
    public function handle(PreBidMatchRequest $event)
    {
        $lead = $event->lead;
        $hauler = $event->hauler;

        // Log it into history
        $this->history
            ->setLeadID($lead->id)
            ->setHaulerID($hauler->id)
            ->setType('pre_match_request')
            ->create();
    }
}
