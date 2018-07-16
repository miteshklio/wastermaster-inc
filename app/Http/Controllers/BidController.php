<?php

namespace App\Http\Controllers;

use App\Bid;
use Illuminate\Http\Request;

use App\Http\Requests;
use WasteMaster\v1\Bids\BidExists;
use WasteMaster\v1\Bids\BidManager;
use WasteMaster\v1\Haulers\HaulerManager;
use WasteMaster\v1\Leads\LeadManager;

/**
 * Class BidController
 *
 * Used for the external bid form only.
 *
 * @package App\Http\Controllers
 */
class BidController extends Controller
{
    /**
     * @var LeadManager
     */
    protected $leads;

    /**
     * @var HaulerManager
     */
    protected $haulers;

    /**
     * @var \WasteMaster\v1\Bids\BidManager
     */
    protected $bids;

    public function __construct(LeadManager $leads, HaulerManager $haulers, BidManager $bids)
    {
        $this->leads = $leads;
        $this->haulers = $haulers;
        $this->bids = $bids;
    }

    /**
     * Shows the Bid form for the Hauler to bid out the job.
     *
     * @param string $code
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm(string $code)
    {
        $code = base64_decode($code);
        list($leadID, $haulerID) = explode('::', $code);

        $lead   = $this->leads->find($leadID);
        $hauler = $this->haulers->find($haulerID);
        $bid    = $this->bids->findExisting($leadID, $haulerID);

        $displayNumber = function($number)
        {
            $number = number_format($number, 2);

            list($left, $right) = explode('.', $number);

            return $right == 0
                ? number_format($number, 0)
                : number_format($number, 1);
        };

        return view('app.bids.form', [
            'code' => base64_encode($code),
            'lead' => $lead,
            'hauler' => $hauler,
            'bid' => $bid,
            'acceptedBid' => $lead->acceptedBid(),
            'displayNumber' => $displayNumber
        ]);
    }

    public function submitBid(Request $request, string $code)
    {
        $this->validate($request, [
            'hauler_email' => 'required|email',
            'rec_offset' => 'numeric',
            'fuel_surcharge' => 'numeric',
            'env_surcharge' => 'numeric',
            'recovery_fee' => 'numeric',
            'admin_fee' => 'numeric',
            'other_fees' => 'numeric',
            'net_monthly' => 'required|numeric',
        ]);

        $code = base64_decode($code);
        list($leadID, $haulerID) = explode('::', $code);

        try
        {
            $this->bids
                ->setLeadID($leadID)
                ->setHaulerID($haulerID)
                ->setHaulerEmail($request->input('hauler_email'))
                ->setNotes($request->input('notes'))
                ->setWastePrice($request->input('msw_price'))
                ->setRecyclePrice($request->input('rec_price'))
                ->setRecycleOffset($request->input('rec_offset'))
                ->setFuelSurcharge($request->input('fuel_surcharge'))
                ->setEnvironmentalSurcharge($request->input('env_surcharge'))
                ->setRecoveryFee($request->input('recovery_fee'))
                ->setAdminFee($request->input('admin_fee'))
                ->setOtherFees($request->input('other_fees'))
                ->setNet($request->input('net_monthly'))
                ->setStatus(Bid::STATUS_LIVE)
                ->setNoBid($request->has('no-bid'))
                ->create();

            return redirect()->route('bids::thanks', ['code' => base64_encode($code)]);
        } catch(\Exception $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Displays the Thanks for Bidding! screen.
     *
     * @param string $code
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function thanks(string $code)
    {
        $code = base64_decode($code);
        list($leadID, $haulerID) = explode('::', $code);

        $hauler = $this->haulers->find($haulerID);
        $lead   = $this->leads->find($leadID);

        return view('app.bids.thanks', [
            'lead' => $lead,
            'hauler' => $hauler,
            'code' => base64_encode($code)
        ]);
    }
}
