<?php namespace WasteMaster\v1\Bids;

use App\Bid;
use App\Lead;

/**
 * Class PreBidMatcher
 *
 * Matches the lead against past bids
 * from other clients to find the lowest
 * bid that matches the requirements
 * (dumpsters, size, frequency)
 *
 * @package WasteMaster\v1\Bids
 */
class PreBidMatcher
{
    /**
     * @var Bid
     */
    protected $bids;

    public function __construct(Bid $bids)
    {
        $this->bids = $bids;
    }

    /**
     * Matches the best price on a waste service match.
     *
     * @param Lead $lead
     */
    public function matchWaste(Lead $lead)
    {
        $bids = $this->bids
            ->join('leads', 'leads.id', '=', 'bids.lead_id')
            ->where([
                'msw_qty' => $lead->msw_qty,
                'msw_yards' => $lead->msw_yards,
                'msw_per_week' => $lead->msw_per_week
            ]);

        if (! empty($lead->msw2_qty))
        {
            $bids = $bids
                ->orWhere([
                    'msw2_qty' => $lead->msw2_qty,
                    'msw2_yards' => $lead->msw2_yards,
                    'msw2_per_week' => $lead->msw2_per_week
                ]);
        }

        return $bids
            ->select('bids.*')
            ->orderBy('net_monthly', 'asc')
            ->orderBy('bids.created_at', 'desc')
            ->where('net_monthly', '>', 0)
            ->with('hauler', 'lead')
            ->first();
    }

    /**
     * Matches the best price on a recycling match.
     *
     * @param Lead $lead
     */
    public function matchRecycle(Lead $lead)
    {
        $bids = $this->bids
            ->join('leads', 'leads.id', '=', 'bids.lead_id')
            ->where([
                'rec_qty' => $lead->rec_qty,
                'rec_yards' => $lead->rec_yards,
                'rec_per_week' => $lead->rec_per_week
            ]);

        if (! empty($lead->rec2_qty))
        {
            $bids = $bids
                ->orWhere([
                    'rec2_qty' => $lead->rec2_qty,
                    'rec2_yards' => $lead->rec2_yards,
                    'rec2_per_week' => $lead->rec2_per_week
                ]);
        }

        return $bids
            ->select('bids.*')
            ->orderBy('net_monthly', 'asc')
            ->orderBy('bids.created_at', 'desc')
            ->where('net_monthly', '>', 0)
            ->with('hauler', 'lead')
            ->first();
    }
}
