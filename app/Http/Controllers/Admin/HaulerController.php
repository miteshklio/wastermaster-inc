<?php namespace App\Http\Controllers\Admin;

use App\Hauler;
use App\Http\Controllers\Controller;
use App\ServiceArea;
use Illuminate\Http\Request;
use WasteMaster\v1\Haulers\HaulerExists;
use WasteMaster\v1\Haulers\HaulerManager;
use WasteMaster\v1\Haulers\HaulerNotFound;
use WasteMaster\v1\Helpers\DataTable;
use WasteMaster\v1\ServiceAreas\ServiceAreaManager;

class HaulerController extends Controller
{
    protected $haulers;

    public function __construct(HaulerManager $haulers)
    {
        $this->haulers     = $haulers;
    }

    /**
     * Displays sortable table of haulers in the system.
     *
     * @param Hauler $model
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Hauler $model)
    {
        $datatable = new DataTable($model);

        $datatable->showColumns([
            'name'       => 'Name',
            'city_id'    => 'Operating Area',
            'svc_waste'  => 'Waste Types',
            'emails'     => 'Associated Emails',
        ])
            ->searchColumns(['name', 'emails', 'city_id'])
            ->setDefaultSort('name', 'asc')
            ->setAlwaysSort('archived', 'asc')
            ->eagerLoad('serviceArea')
            ->hideOnMobile(['emails'])
            ->prepare(20);

        return view('app.admin.haulers.index')->with([
            'datatable' => $datatable
        ]);
    }

    /**
     * Displays the create Hauler form.
     */
    public function newHauler(ServiceAreaManager $areas)
    {
        return view('app.admin.haulers.form')->with([
            'serviceAreas' => $areas->all()
        ]);
    }

    /**
     * Actual Hauler creation.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'service_area_id' => 'required',
            'emails' => 'required|max:255'
        ]);

        try
        {
            $this->haulers
                ->setName($request->input('name'))
                ->setServiceAreaID($request->input('service_area_id'))
                ->setRecycling((bool)$request->input('recycle'))
                ->setWaste((bool)$request->input('waste'))
                ->setEmails($request->input('emails'))
                ->create();

            return redirect()->route('haulers::home')->with(['message' => trans('messages.haulerCreated')]);
        } catch(HaulerExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Displays the edit a Hauler form.
     *
     * @param int $haulerID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(ServiceArea $areas, int $haulerID)
    {
        $hauler = $this->haulers->find($haulerID);

        if ($hauler === null)
        {
            return redirect()->back()->with(['message' => trans('messages.haulerNotFound')]);
        }

        return view('app.admin.haulers.form', [
            'hauler' => $hauler,
            'serviceAreas' => $areas->all()
        ]);
    }

    /**
     * Handles actually saving the udpated record.
     *
     * @param Request $request
     * @param int     $haulerID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $haulerID)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'service_area_id' => 'required',
            'emails' => 'required|max:255'
        ]);

        try
        {
            $this->haulers
                ->setName($request->input('name'))
                ->setServiceAreaID($request->input('service_area_id'))
                ->setRecycling((bool)$request->input('recycle'))
                ->setWaste((bool)$request->input('waste'))
                ->setEmails($request->input('emails'))
                ->update($haulerID);

            return redirect()->route('haulers::home')->with(['message' => trans('messages.haulerUpdated')]);
        } catch(\Exception $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes a Hauler.
     *
     * @param Request $request
     * @param int     $haulerID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, int $haulerID)
    {
        try {
            $this->haulers->delete($haulerID);

            return redirect()->route('haulers::home')->with(['message' => trans('messages.haulerDeleted')]);
        }
        catch (HaulerNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Sets the archive flag on a project.
     *
     * @param int                      $haulerID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function archive(int $haulerID)
    {
        try {
            $this->haulers->archive($haulerID);

            return redirect()->route('haulers::home')->with(['message' => trans('messages.haulerArchived')]);
        }
        catch (HaulerNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Unarchives a Hauler
     *
     * @param int                      $haulerID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unarchive(int $haulerID)
    {
        try {
            $this->haulers->archive($haulerID, false);

            return redirect()->route('haulers::home')->with(['message' => trans('messages.haulerUnArchived')]);
        }
        catch (HaulerNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

}
