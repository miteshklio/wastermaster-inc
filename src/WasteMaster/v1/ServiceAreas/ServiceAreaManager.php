<?php namespace WasteMaster\v1\ServiceAreas;

use App\ServiceArea;

class ServiceAreaManager {

    protected $areas;

    protected $name;

    public function __construct(ServiceArea $areas)
    {
        $this->areas = $areas;
    }

    /**
     * Sets the name of the service area.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Creates a new ServiceArea from already set Name() and returns the instance.
     *
     * @return static
     * @throws \WasteMaster\v1\ServiceAreas\ServiceAreaExists
     */
    public function create()
    {
        if (empty($this->name))
        {
            throw new MissingRequiredFields(trans('messages.serviceAreaValidationErrors', ['fields' => 'name']));
        }

        // Does an Area with this address
        // already exist?
        if ($this->areas->where(\DB::raw('LOWER(name)'), strtolower($this->name))->count())
        {
            throw new ServiceAreaExists(trans('messages.serviceAreaExists'));
        }

        $area = $this->areas->create([
            'name' => $this->name
        ]);

        $this->name = null;

        return $area;
    }

    /**
     * Updates an existing ServiceArea.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function update(int $id)
    {
        $area = $this->find($id);

        $area->name = $this->name;
        $area->save();

        return $area;
    }

    /**
     * Locates an existing Service Area by ID, or throws an exception.
     *
     * @param int $id
     *
     * @return mixed
     * @throws \WasteMaster\v1\ServiceAreas\ServiceAreaNotFound
     */
    public function find(int $id)
    {
        $area = $this->areas->find($id);

        if ($area === null)
        {
            throw new ServiceAreaNotFound(trans('messages.serviceAreaNotFound', ['id' => $id]));
        }

        return $area;
    }

    public function all()
    {
        return $this->areas->orderBy('name', 'asc')->get();
    }


    public function delete(int $id)
    {
        $area = $this->find($id);

        return $area->delete();
    }

}
