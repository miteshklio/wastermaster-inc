<?php namespace App\Http\Controllers\Admin;

use Event;
use App\Events\PostBidMatchRequest;
use App\Http\Controllers\Controller;
use App\Bid;
use Illuminate\Http\Request;
use WasteMaster\v1\Bids\BidManager;
use WasteMaster\v1\Haulers\HaulerManager;
use WasteMaster\v1\Bids\BidExists;
use WasteMaster\v1\Bids\BidNotFound;
use WasteMaster\v1\Helpers\DataTable;

class BidController extends Controller
{
    protected $bids;

    public function __construct(BidManager $bids)
    {
        $this->bids = $bids;
    }

    /**
     * Displays sortable table of leads in the system.
     *
     * @param Bid $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Bid $model)
    {
        // Update the last_bids_view for this user
        // so that we can track new bids.
        $user = \Auth::user();
        $user->last_bids_view = date('Y-m-d H:i:s');
        $user->save();

        $datatable = new DataTable($model);

        $datatable->showColumns([
            'lead_name' => 'Lead Name',
            'status' => 'Status',
            'hauler_name' => 'Bidder',
            'created_at' => 'Submitted At',
            'current_total' => 'Current $',
            'net_monthly' => 'Bid $'
        ])
            ->searchColumns(['leads.id', 'leads.company', 'haulers.name'])
            ->setDefaultSort('net_monthly', 'asc')
            ->where('bids.archived', 0)
            ->join('leads', 'leads.id', '=', 'bids.lead_id')
            ->join('haulers', 'haulers.id', '=', 'bids.hauler_id')
            ->select('bids.*', 'leads.company as lead_name', 'leads.monthly_price as current_total', 'haulers.name as hauler_name')
            ->hideOnMobile(['created_at', 'current_total'])
            ->eagerLoad('lead.hauler')
            ->prepare(20);

        return view('app.admin.bids.index')->with([
            'datatable' => $datatable,
            'recentDate' => strtotime(\Auth::user()->last_bids_view),
        ]);
    }

    /**
     * Displays the edit a Lead form.
     *
     * @param HaulerManager $haulers
     * @param int           $bidID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(HaulerManager $haulers, int $bidID)
    {
        $bid = $this->bids->find($bidID);

        if ($bid === null)
        {
            return redirect()->back()->with(['message' => trans('messages.bidNotFound')]);
        }

        return view('app.admin.bids.form', [
            'bid' => $bid,
        ]);
    }

    /**
     * Handles actually saving the updated record.
     *
     * @param Request $request
     * @param int     $bidID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $bidID)
    {
        $this->validate($request, [
            'msw_qty' => 'integer',
            'msw_yards' => 'numeric',
            'msw_per_week' => 'numeric',
            'rec_qty' => 'integer',
            'rec_yards' => 'numeric',
            'rec_per_week' => 'numeric',
            'prior_total' => 'numeric',
            'msw_price' => 'numeric',
            'rec_price' => 'numeric',
            'rec_offset' => 'numeric',
            'fuel_surcharge' => 'numeric',
            'env_surcharge' => 'numeric',
            'recovery_fee' => 'numeric',
            'admin_fee' => 'numeric',
            'other_fees' => 'numeric',
            'net_monthly' => 'numeric',
        ]);

        try
        {
            $this->bids
                ->setWastePrice($request->input('msw_price'))
                ->setRecyclePrice($request->input('rec_price'))
                ->setRecycleOffset($request->input('rec_offset'))
                ->setFuelSurcharge($request->input('fuel_surcharge'))
                ->setEnvironmentalSurcharge($request->input('env_surcharge'))
                ->setRecoveryFee($request->input('recovery_fee'))
                ->setAdminFee($request->input('admin_fee'))
                ->setOtherFees($request->input('other_fees'))
                ->setNet($request->input('net_monthly'))
                ->update($bidID);

            return redirect()->route('bids::show', ['id' => $bidID])->with(['message' => trans('messages.bidUpdated')]);
        } catch(BidExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Lead.
     *
     * @param int $bidID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $bidID)
    {
        try {
            $this->bids->delete($bidID);

            return redirect()->route('bids::home')->with(['message' => trans('messages.bidDeleted')]);
        }
        catch (BidNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Rescind a bid and make all bids for this lead Live again.
     *
     * @param int $bidID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rescind(int $bidID)
    {
        try {
            $this->bids->rescindBid($bidID);

            return redirect()->route('bids::home')->with(['message' => trans('messages.bidRescinded')]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    public function postMatchRequest(int $bidID)
    {
        try
        {
            Event::fire(new PostBidMatchRequest($this->bids->find($bidID)));
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->back()->with(['message' => trans('messages.emailSent')]);
    }

    public function acceptModal(int $bidID)
    {
        $bid = $this->bids->find($bidID);
        $lead = $bid->lead;

        $initialGross = round(($lead->monthly_price - $bid->net_monthly) / 2, 2);
        $initalTotal  = $bid->net_monthly + $initialGross;

        return view('app.admin.bids._accept_modal', [
            'bid' => $bid,
            'lead' => $lead,
            'hauler' => $bid->hauler,
            'gross' => $initialGross,
            'total' => $initalTotal
        ]);
    }

    /**
     * Sets the archive flag on a lead.
     *
     * @param int $bidID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, int $bidID)
    {
        try {
            $this->bids->acceptBid($bidID, $request->input('gross'));

            return redirect()->route('bids::show', ['id' => $bidID])->with(['message' => trans('messages.bidAccepted')]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }
}
