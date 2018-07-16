<?php

namespace App\Console\Commands;

use App\Bid;
use App\History;
use App\Lead;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;

class BidReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bids:remind {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders about any bids requests.';

    /**
     * @var History
     */
    protected $history;
    /**
     * @var Bid
     */
    protected $bids;
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * Create a new command instance.
     *
     * @param History $history
     * @param Bid     $bids
     * @param Mailer  $mailer
     */
    public function __construct(History $history, Bid $bids, Mailer $mailer)
    {
        parent::__construct();

        $this->history = $history;
        $this->bids = $bids;
        $this->mailer = $mailer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $referenceDate = $this->option('date') ?? date('Y-m-d');
        $referenceDate = strtotime($referenceDate);

        $dates = [
            3 => date('Y-m-d', strtotime('-3 days', $referenceDate)),
            5 => date('Y-m-d', strtotime('-5 days', $referenceDate)),
            7 => date('Y-m-d', strtotime('-7 days', $referenceDate)),
        ];

        // Get the leads with potential haulers to email
        // Only want Leads with status of 3 (Bids Requested).
        $history = $this->history
            ->whereIn(\DB::raw('DATE(created_at)'), $dates)
            ->where('type', 'bid_request')
            ->with('hauler')
            ->get();

        if ($history === null)
        {
            return;
        }

        // Loop through the history, checking for any
        // haulers that haven't bid yet.
        foreach ($history as $record)
        {
            // Don't want to email people that have already bid.
            $hasBid = $this->bids->where([
                'hauler_id' => $record->hauler_id,
                'lead_id' => $record->lead_id
            ])->count();

            if ($hasBid)
            {
                continue;
            }

            // Determine if this is day 7 or not (will set the email subject)
            $isFinalReminder = $record->created_at->format('Y-m-d') == $dates[7];

            $data = [
                'lead'   => $record->lead,
                'hauler' => $record->hauler,
                'url'    => route('bids::externalForm', ['id' => base64_encode($record->lead->id .'::'. $record->hauler->id)])
            ];

            $this->mailer->send('emails.bid_reminder', $data, function($m) use($isFinalReminder, $record) {
                $m->subject($isFinalReminder ? trans('messages.emailFinalReminder') : trans('messages.emailReminder'));
                $m->to($record->hauler->email_array);
            });
        }
    }
}
