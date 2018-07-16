<?php namespace App\Http\Controllers\Admin;

use App\Events\Event;
use App\Events\RequestBidsForLead;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use WasteMaster\v1\Bids\BidManager;
use WasteMaster\v1\Bids\PreBidMatcher;
use WasteMaster\v1\Clients\ClientManager;
use WasteMaster\v1\Haulers\HaulerManager;
use WasteMaster\v1\History\HistoryManager;
use WasteMaster\v1\Leads\LeadExists;
use WasteMaster\v1\Leads\LeadManager;
use WasteMaster\v1\Leads\LeadNotFound;
use WasteMaster\v1\Helpers\DataTable;
use WasteMaster\v1\ServiceAreas\ServiceAreaManager;

class LeadsController extends Controller
{
    protected $leads;

    public function __construct(LeadManager $leads)
    {
        $this->leads = $leads;
    }

    /**
     * Displays sortable table of leads in the system.
     *
     * @param Lead $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Lead $model, BidManager $bids)
    {
        $datatable = new DataTable($model);

        $datatable->showColumns([
            'company'    => 'Name',
            'bid_count'  => '# of Bids',
            'contact_name' => 'Contact',
            'status'     => 'Status',
            'hauler_id' => 'Current Hauler',
            'service_area_id' => 'Service Area',
            'created_at'  => 'Created At',
            'leads.monthly_price' => 'Current $',
            'low_bid'     => 'Cheapest Bid',
        ])
            ->searchColumns(['company', 'status'])
            ->setAlwaysSort('archived', 'asc')
            ->setDefaultSort('created_at', 'desc')
            ->hideOnMobile(['created_at', 'Current $'])
            ->eagerLoad('city')
            ->select('leads.*',
                \DB::raw('(SELECT net_monthly FROM bids WHERE bids.lead_id = leads.id AND bids.archived=0 AND bids.net_monthly > 0 ORDER BY net_monthly ASC LIMIT 0,1) as low_bid')
            )
            ->prepare(20);

        return view('app.admin.leads.index')->with([
            'datatable' => $datatable,
            'bids' => $bids,
            'recentDate' => \Auth::user()->last_bids_view,
        ]);
    }

    /**
     * Displays the create Lead form.
     */
    public function newLead(HaulerManager $haulers, ServiceAreaManager $areas)
    {
        return view('app.admin.leads.form', [
            'editMode' => false,
            'haulers' => $haulers->all(),
            'serviceAreas' => $areas->all()
        ]);
    }

    /**
     * Actual Lead creation.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'company' => 'required|max:255',
            'address' => 'required',
            'service_area_id' => 'required',
            'contact_name' => 'required|max:255',
            'contact_email' => 'required|email|max:255',
            'account_num' => 'required|max:255',
            'hauler_id' => 'required|integer',
            'msw_qty' => 'integer',
            'msw_yards' => 'numeric',
            'msw_per_week' => 'numeric',
            'rec_qty' => 'integer',
            'rec_yards' => 'numeric',
            'rec_per_week' => 'numeric',
            'msw2_qty' => 'integer',
            'msw2_yards' => 'numeric',
            'msw2_per_week' => 'numeric',
            'rec2_qty' => 'integer',
            'rec2_yards' => 'numeric',
            'rec2_per_week' => 'numeric',
            'monthly_price' => 'numeric',
        ]);

        try
        {
            $lead = $this->leads
                ->setCompany($request->input('company'))
                ->setAddress($request->input('address'))
                ->setServiceAreaID($request->input('service_area_id'))
                ->setContactName($request->input('contact_name'))
                ->setContactEmail($request->input('contact_email'))
                ->setAccountNum($request->input('account_num'))
                ->setHaulerID($request->input('hauler_id'))
                ->setWaste(
                    $request->input('msw_qty'),
                    $request->input('msw_yards'),
                    $request->input('msw_per_week')
                )
                ->setRecycling(
                    $request->input('rec_qty'),
                    $request->input('rec_yards'),
                    $request->input('rec_per_week')
                )
                ->setWaste2(
                    $request->input('msw2_qty'),
                    $request->input('msw2_yards'),
                    $request->input('msw2_per_week')
                )
                ->setRecycling2(
                    $request->input('rec2_qty'),
                    $request->input('rec2_yards'),
                    $request->input('rec2_per_week')
                )
                ->setMonthlyPrice($request->input('monthly_price'))
                ->setNotes($request->input('notes'))
                ->create();

            return redirect()->route('leads::show', ['id' => $lead->id])->with(['message' => trans('messages.leadCreated')]);
        } catch(LeadExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Displays the edit a Lead form.
     *
     * @param HaulerManager                                   $haulers
     * @param \WasteMaster\v1\History\HistoryManager          $history
     * @param \WasteMaster\v1\ServiceAreas\ServiceAreaManager $areas
     * @param PreBidMatcher                                   $matcher
     * @param int                                             $leadID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws LeadNotFound
     */
    public function show(HaulerManager $haulers, HistoryManager $history, ServiceAreaManager $areas, PreBidMatcher $matcher, int $leadID)
    {
        $lead = $this->leads->find($leadID);

        if ($lead === null)
        {
            return redirect()->back()->with(['message' => trans('messages.leadNotFound')]);
        }

        $cityHaulers = $haulers->applicableForLead($lead);

        // Show Post Bid Matching? Only show if lowest bid is lower than
        // current monthly total - unless rebidding, than ignore gross_profit
        // and work off of net_monthly here.
        $lowBid = $lead->cheapestBidObject();
        $showPostBid = $this->leads->shouldShowPostMatchBid($lead, $lowBid);

        $bidRequestHistory = $history->findForLead($leadID, 'bid_request');
        $postMatchHistory = $history->findForLead($leadID, 'post_match_request');
        $preMatchHistory = $history->findForLead($leadID, 'pre_match_request');

        return view('app.admin.leads.form', [
            'lead' => $lead,
            'editMode' => true,
            'haulers' => $haulers->all(),
            'cityHaulers' => $cityHaulers,
            'lowBid' => $lowBid,
            'bidRequestDate' => $bidRequestHistory[0]->created_at ?? '',
            'preMatchDate' => $preMatchHistory[0]->created_at ?? '',
            'postMatchDate' => $postMatchHistory[0]->created_at ?? '',
            'bidRequestHaulers' => $history->listNames($bidRequestHistory),
            'preMatchHaulers' => $history->listNames($preMatchHistory),
            'postMatchHistory' => $history->listNames($postMatchHistory),
            'isCurrentMatching' => $this->leads->doesCurrentHaulerMatch($lead),
            'showPostMatchBid' => $showPostBid,
            'preWasteMatch' => $matcher->matchWaste($lead),
            'preRecycleMatch' => $matcher->matchRecycle($lead),
            'serviceAreas' => $areas->all()
        ]);
    }

    /**
     * Handles actually saving the updated record.
     *
     * @param Request $request
     * @param int     $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $leadID)
    {
        $this->validate($request, [
            'company' => 'required|max:255',
            'address' => 'required',
            'service_area_id' => 'required',
            'contact_name' => 'required|max:255',
            'contact_email' => 'required|email|max:255',
            'account_num' => 'required|max:255',
            'hauler_id' => 'required|integer',
            'msw_qty' => 'integer',
            'msw_yards' => 'numeric',
            'msw_per_week' => 'numeric',
            'rec_qty' => 'integer',
            'rec_yards' => 'numeric',
            'rec_per_week' => 'numeric',
            'msw2_qty' => 'integer',
            'msw2_yards' => 'numeric',
            'msw2_per_week' => 'numeric',
            'rec2_qty' => 'integer',
            'rec2_yards' => 'numeric',
            'rec2_per_week' => 'numeric',
            'monthly_price' => 'numeric'
        ]);

        try
        {
            $this->leads
                ->setCompany($request->input('company'))
                ->setAddress($request->input('address'))
                ->setServiceAreaID($request->input('service_area_id'))
                ->setContactName($request->input('contact_name'))
                ->setContactEmail($request->input('contact_email'))
                ->setAccountNum($request->input('account_num'))
                ->setHaulerID($request->input('hauler_id'))
                ->setWaste(
                    $request->input('msw_qty'),
                    $request->input('msw_yards'),
                    $request->input('msw_per_week')
                )
                ->setWaste2(
                    $request->input('msw2_qty'),
                    $request->input('msw2_yards'),
                    $request->input('msw2_per_week')
                )
                ->setRecycling(
                    $request->input('rec_qty'),
                    $request->input('rec_yards'),
                    $request->input('rec_per_week')
                )
                ->setRecycling2(
                    $request->input('rec2_qty'),
                    $request->input('rec2_yards'),
                    $request->input('rec2_per_week')
                )
                ->setMonthlyPrice($request->input('monthly_price'))
                ->setNotes($request->input('notes'))
                ->update($leadID);

            return redirect()->route('leads::show', ['id' => $leadID])->with(['message' => trans('messages.leadUpdated')]);
        } catch(LeadExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Lead.
     *
     * @param int     $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $leadID)
    {
        try {
            $this->leads->delete($leadID);

            return redirect()->route('leads::home')->with(['message' => trans('messages.leadDeleted')]);
        }
        catch (LeadNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Sets the archive flag on a lead.
     *
     * @param int $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(int $leadID)
    {
        try {
            $this->leads->archive($leadID);

            return redirect()->route('leads::home')->with(['message' => trans('messages.leadArchived')]);
        }
        catch (LeadNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Unarchives a Lead
     *
     * @param int $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unarchive(int $leadID)
    {
        try {
            $this->leads->archive($leadID, false);

            return redirect()->route('leads::home')->with(['message' => trans('messages.leadUnArchived')]);
        }
        catch (LeadNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Sends the bid request emails to the selected haulers.
     *
     * @param int $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendBidRequest(Request $request, HaulerManager $haulerManager, int $leadID)
    {
        try {
            $lead = $this->leads->find($leadID);
        }
        catch (\Exception $e)
        {
            return redirect()->route('leads::show', ['id' => $leadID])->with(['message' => $e->getMessage()]);
        }

        $haulerIDs = ! empty($request->input('haulers'))
            ? array_keys($request->input('haulers'))
            : null;

        if ($haulerIDs === null)
        {
            return redirect()->route('leads::show', ['id' => $leadID])->with(['message' => trans('messages.leadNoHaulers')]);
        }

        // If it's a new lead, we're requesting bids,
        // but don't change if it's another send of the bids.
        if ($lead->status == Lead::NEW || empty($lead->status))
        {
            $lead->status = Lead::BIDS_REQUESTED;
            $lead->save();
        }

        $haulers = $haulerManager->findIn($haulerIDs);

        \Event::fire(new RequestBidsForLead($lead, $haulers));

        return redirect()->route('leads::show', ['id' => $leadID])->with(['message' => trans('messages.leadBidsSent')]);
    }

    /**
     * Converts a lead into a client, transferring the lowest bid as
     * the current client information. If the client already exists,
     * will update the current bid info to match the cheapest bid.
     *
     * @param \WasteMaster\v1\Clients\ClientManager $clients
     * @param int                                   $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function convertToClient(ClientManager $clients, int $leadID)
    {
        try {
            $client = $this->leads->convertToClient($leadID, $clients);

            return redirect()->route('clients::show', ['id' => $client->id])
                ->with(['message' => trans('messages.leadConverted')]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Rebid this lead and associated client.
     *
     * @param ClientManager $clients
     * @param LeadManager   $leads
     * @param int           $leadID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rebid(ClientManager $clients, LeadManager $leads, int $leadID)
    {
        $lead = $leads->find($leadID);

        try {
            $clients->rebidLead($lead);

            return redirect()->back()->with(['message' => trans('messages.leadRebid')]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

}
