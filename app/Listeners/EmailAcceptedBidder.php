<?php

namespace App\Listeners;

use App\Events\AcceptedBid;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use WasteMaster\v1\History\HistoryManager;

class EmailAcceptedBidder
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
     * @param \Illuminate\Contracts\Mail\Mailer      $mailer
     * @param \WasteMaster\v1\History\HistoryManager $history
     */
    public function __construct(Mailer $mailer, HistoryManager $history)
    {
        $this->mailer = $mailer;
        $this->history = $history;
    }

    /**
     * Handle the event.
     *
     * @param  AcceptedBid  $event
     * @return void
     */
    public function handle(AcceptedBid $event)
    {
        $hauler = $event->bid->hauler;
        $lead   = $event->bid->lead;

        $data = [
            'hauler' => $hauler,
            'lead'   => $lead,
            'url'    => route('bids::externalForm', ['id' =>base64_encode($lead->id .'::'. $hauler->id)])
        ];

        $this->mailer->send('emails.bid_accepted', $data, function ($m) use($hauler) {
            $m->subject('Your Wastemaster bid has been accepted')
              ->to(unserialize($hauler->emails));
        });

        // Log it into history
        $this->history
            ->setLeadID($lead->id)
            ->setHaulerID($hauler->id)
            ->setType('bid_accepted')
            ->create();
    }
}
