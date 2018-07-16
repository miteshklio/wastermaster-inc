<?php

namespace App\Listeners;

use App\Events\RequestBidsForLead;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use WasteMaster\v1\History\HistoryManager;

class EmailBidRequests
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
     * @param  RequestBidsForLead  $event
     * @return void
     */
    public function handle(RequestBidsForLead $event)
    {
        foreach ($event->haulers as $hauler)
        {
            $lead = $event->lead;

            $data = [
                'lead'   => $lead,
                'hauler' => $hauler,
                'url'    => route('bids::externalForm', ['id' => base64_encode($lead->id .'::'. $hauler->id)])
            ];

            // Send the email
            $this->mailer->send('emails.general_bid', $data, function ($m) use($hauler) {
                $m->subject('A new bid request from Wastemaster')
                    ->to(unserialize($hauler->emails));
            });

            // Log it into history
            $this->history
                ->setLeadID($lead->id)
                ->setHaulerID($hauler->id)
                ->setType('bid_request')
                ->create();
        }
    }
}
