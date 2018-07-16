<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Client;
use Illuminate\Http\Request;
use WasteMaster\v1\Clients\ClientManager;
use WasteMaster\v1\Haulers\HaulerManager;
use WasteMaster\v1\Clients\ClientExists;
use WasteMaster\v1\Clients\ClientNotFound;
use WasteMaster\v1\Helpers\DataTable;
use WasteMaster\v1\ServiceAreas\ServiceAreaManager;

class ClientController extends Controller
{
    protected $clients;

    public function __construct(ClientManager $clients)
    {
        $this->clients = $clients;
    }

    /**
     * Displays sortable table of leads in the system.
     *
     * @param Client $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Client $model)
    {
        $datatable = new DataTable($model);

        $datatable->showColumns([
            'company' => 'Name',
            'service_area_id' => 'Service Area',
            'created_at' => 'Created At',
            'prior_total' => 'Prior $',
            'net_monthly' => 'Net Monthly',
            'gross_profit' => 'Gross Profit',
            'total' => 'Total Monthly'
        ])
            ->searchColumns(['company'])
            ->setDefaultSort('company', 'asc')
            ->setAlwaysSort('archived', 'asc')
            ->hideOnMobile(['created_at', 'prior_total'])
            ->eagerLoad('serviceArea')
            ->prepare(20);

        return view('app.admin.clients.index')->with([
            'datatable' => $datatable
        ]);
    }

    /**
     * Displays the create Lead form.
     */
    public function newClient(HaulerManager $haulers, ServiceAreaManager $areas)
    {
        return view('app.admin.clients.form', [
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
            'gross_profit' => 'numeric',
            'total' => 'numeric',
        ]);

        try
        {
            $this->clients
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
                ->setPriorTotal($request->input('prior_total'))
                ->setWastePrice($request->input('msw_price'))
                ->setRecyclePrice($request->input('rec_price'))
                ->setRecycleOffset($request->input('rec_offset'))
                ->setFuelSurcharge($request->input('fuel_surcharge'))
                ->setEnvironmentalSurcharge($request->input('env_surcharge'))
                ->setRecoveryFee($request->input('recovery_fee'))
                ->setAdminFee($request->input('admin_fee'))
                ->setOtherFees($request->input('other_fees'))
                ->setNet($request->input('net_monthly'))
                ->setGross($request->input('gross_profit'))
                ->setTotal($request->input('total'))
                ->create();

            return redirect()->route('clients::home')->with(['message' => trans('messages.clientCreated')]);
        } catch(ClientExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Displays the edit a Lead form.
     *
     * @param HaulerManager      $haulers
     * @param ServiceAreaManager $areas
     * @param int                $clientID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(HaulerManager $haulers, ServiceAreaManager $areas, int $clientID)
    {
        $client = $this->clients->find($clientID);

        if ($client === null)
        {
            return redirect()->back()->with(['message' => trans('messages.clientNotFound')]);
        }

        $cityHaulers = $haulers->inCity($client->city_id);

        return view('app.admin.clients.form', [
            'client' => $client,
            'editMode' => true,
            'haulers' => $haulers->all(),
            'cityHaulers' => $cityHaulers,
            'serviceAreas' => $areas->all(),
        ]);
    }

    /**
     * Handles actually saving the updated record.
     *
     * @param Request $request
     * @param int     $clientID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $clientID)
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
            'gross_profit' => 'numeric',
            'total' => 'numeric',
        ]);

        try
        {
            $this->clients
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
                ->setPriorTotal($request->input('prior_total'))
                ->setWastePrice($request->input('msw_price'))
                ->setRecyclePrice($request->input('rec_price'))
                ->setRecycleOffset($request->input('rec_offset'))
                ->setFuelSurcharge($request->input('fuel_surcharge'))
                ->setEnvironmentalSurcharge($request->input('env_surcharge'))
                ->setRecoveryFee($request->input('recovery_fee'))
                ->setAdminFee($request->input('admin_fee'))
                ->setOtherFees($request->input('other_fees'))
                ->setNet($request->input('net_monthly'))
                ->setGross($request->input('gross_profit'))
                ->setTotal($request->input('total'))
                ->update($clientID);

            return redirect()->route('clients::show', ['id' => $clientID])->with(['message' => trans('messages.clientUpdated')]);
        } catch(ClientExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Lead.
     *
     * @param int $clientID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $clientID)
    {
        try {
            $this->clients->delete($clientID);

            return redirect()->route('clients::home')->with(['message' => trans('messages.clientDeleted')]);
        }
        catch (ClientNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Sets the archive flag on a lead.
     *
     * @param int $clientID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(int $clientID)
    {
        try {
            $this->clients->archive($clientID);

            return redirect()->route('clients::home')->with(['message' => trans('messages.clientArchived')]);
        }
        catch (ClientNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Unarchives a Lead
     *
     * @param int $clientID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unarchive(int $clientID)
    {
        try {
            $this->clients->archive($clientID, false);

            return redirect()->route('clients::home')->with(['message' => trans('messages.clientUnArchived')]);
        }
        catch (ClientNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Rebids a client. Used when the client
     *
     * @param int $clientID
     *
     * @return string
     */
    public function rebid(int $clientID)
    {
        try {
            $this->clients->rebidClient($clientID);

            return redirect()->back()->with(['message' => trans('messages.leadRebid')]);
        }
        catch (\Exception $e)
        {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }


}
