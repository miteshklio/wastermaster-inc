<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ServiceArea;
use WasteMaster\v1\Helpers\DataTable;
use WasteMaster\v1\ServiceAreas\ServiceAreaExists;
use WasteMaster\v1\ServiceAreas\ServiceAreaManager;
use WasteMaster\v1\ServiceAreas\ServiceAreaNotFound;

class AreasController extends Controller
{
    /**
     * @var \WasteMaster\v1\ServiceAreas\ServiceAreaManager
     */
    protected $areas;

    public function __construct(ServiceAreaManager $areas)
    {
        $this->areas = $areas;
    }

    /**
     * Displays the Service areas in a paginated manner,
     * and allows searching.
     *
     * @param \App\ServiceArea $model
     *
     * @return mixed
     */
    public function index(ServiceArea $model)
    {
        $datatable = new DataTable($model);

        $datatable->showColumns([
            'id' => 'ID',
            'name' => 'Name',
        ])
            ->searchColumns(['name'])
            ->setDefaultSort('name', 'asc')
            ->prepare(20);

        return view('app.admin.areas.index')->with([
            'datatable' => $datatable,
        ]);
    }

    /**
     * Shows the New Area form.
     *
     * @return mixed
     */
    public function newArea()
    {
        return view('app.admin.areas.form')->with([
            'editMode' => false
        ]);
    }

    /**
     * Creetes a new Service Area
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        try {
            $this->areas
                ->setName($request->input('name'))
                ->create();

            return redirect()->route('areas::home')->with(['message' => trans('messages.serviceAreaCreated')]);
        }
        catch (ServiceAreaExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Shows the edit form for an existing Service Area
     *
     * @param int $areaID
     *
     * @return mixed
     */
    public function show(int $areaID)
    {
        return view('app.admin.areas.form')->with([
            'editMode' => true,
            'area' => $this->areas->find($areaID)
        ]);
    }

    /**
     * Updates an existing Service Area
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $areaID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $areaID)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        try {
            $this->areas
                ->setName($request->input('name'))
                ->update($areaID);

            return redirect()->route('areas::home')->with(['message' => trans('messages.serviceAreaUpdated')]);
        }
        catch (ServiceAreaExists $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

    /**
     * Deletes an existing Service Area
     *
     * @param int $areaID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(int $areaID)
    {
        try {
            $this->areas->delete($areaID);

            return redirect()->route('areas::home')->with(['message' => trans('messages.serviceAreaDeleted')]);
        }
        catch (ServiceAreaNotFound $e)
        {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        }
    }

}
