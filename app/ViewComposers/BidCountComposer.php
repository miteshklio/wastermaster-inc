<?php namespace App\ViewComposers;

use Illuminate\View\View;
use WasteMaster\v1\Bids\BidManager;

class BidCountComposer
{
    /**
     * @var \WasteMaster\v1\Bids\BidManager
     */
    protected $bids;

    public function __construct(BidManager $bids)
    {
        $this->bids = $bids;
    }

    public function compose(View $view)
    {
        // Not in admin? Get outta' here!
        if (! request()->is('admin/*')) {
            $count = 0;
        }
        else
        {
            $count = $this->bids->recentBidCount(\Auth::user()->last_bids_view);
        }

        $view->with('newBidCount', $count);
    }

}
