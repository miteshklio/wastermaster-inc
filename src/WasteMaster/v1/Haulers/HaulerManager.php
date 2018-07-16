<?php namespace WasteMaster\v1\Haulers;

use App\City;
use App\Hauler;
use App\Lead;

class HaulerManager
{
    /**
     * @var Hauler
     */
    protected $haulers;

    protected $cities;

    protected $name;
    protected $city;
    protected $service_area_id;
    protected $doesRecycling;
    protected $doesWaste;
    protected $emails = [];

    public function __construct(Hauler $hauler, City $cities)
    {
        $this->haulers = $hauler;
        $this->cities  = $cities;
    }

    /**
     * Sets the name to use when creating/updating a Hauler.
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
     * Sets the city_id to use when creating/updating a Hauler.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setCityID(int $id)
    {
        $this->city = $id;

        return $this;
    }

    /**
     * Sets the service_area_id to use when creating/updating a Service Area.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setServiceAreaID(int $id)
    {
        $this->service_area_id = $id;

        return $this;
    }


    /**
     * Looks up the appropriate city based on the city name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setCity(string $name)
    {
        $city = $this->cities->where('name', $name)->first();

        if ($city === null)
        {
            throw new CityNotFound(trans('messages.cityNotFound'));
        }

        $this->city = $city->id;

        return $this;
    }

    /**
     * Whether this Hauler does recycling.
     *
     * @param bool $recycles
     *
     * @return $this
     */
    public function setRecycling(bool $recycles=false)
    {
        $this->doesRecycling = (int)$recycles;

        return $this;
    }

    /**
     * Whether this Hauler does waste collection.
     *
     * @param bool $waste
     *
     * @return $this
     */
    public function setWaste(bool $waste = false)
    {
        $this->doesWaste = (int)$waste;

        return $this;
    }

    /**
     * Add one or more
     *
     * @param $emails
     *
     * @return $this
     * @internal param string $email
     */
    public function setEmails($emails)
    {
        $this->emails = $this->parseEmails($emails);

        return $this;
    }

    /**
     * Creates a new Hauler from data
     * provided by the fluent interface.
     */
    public function create()
    {
        $this->checkRequired();

        // Does a Hauler with this name/city
        // already exist?
        if ($this->haulers->where(['name' => $this->name, 'city_id' => $this->city])->count())
        {
            throw new HaulerExists(trans('haulerExists'));
        }

        $hauler = $this->haulers->create([
            'name'            => $this->name,
            'service_area_id' => $this->service_area_id,
            'svc_recycle'     => (int)$this->doesRecycling,
            'svc_waste'       => (int)$this->doesWaste,
            'emails'          => serialize($this->emails)
        ]);

        return $hauler;
    }

    /**
     * Updates an existing hauler, filling in
     * properties with key/value pairs in $fields array.
     *
     * @param int   $id
     *
     * @return
     * @throws HaulerNotFound
     */
    public function update(int $id)
    {
        $hauler = $this->haulers->find($id);

        if ($hauler === null)
        {
            throw new HaulerNotFound(trans('messages.haulerNotFound', ['id' => $id]));
        }

        $fields = [
            'svc_recycle' => $this->doesRecycling,
            'svc_waste'   => $this->doesWaste,
        ];

        if ($this->name !== null) $fields['name'] = $this->name;
        if ($this->service_area_id !== null) $fields['service_area_id'] = $this->service_area_id;
        if (count($this->emails)) $fields['emails'] = serialize($this->parseEmails($this->emails));

        $hauler->fill($fields);
        $hauler->save();

        return $hauler;
    }

    /**
     * Permanently deletes an existing hauler.
     *
     * @param int $id
     *
     * @return bool|null
     */
    public function delete(int $id)
    {
        $hauler = $this->find($id);

        return $hauler->delete();
    }

    /**
     * Archives a Hauler.
     *
     * @param int  $id
     * @param bool $archived
     *
     * @return mixed
     */
    public function archive(int $id, bool $archived=true)
    {
        $hauler = $this->find($id);

        $hauler->archived = $archived;
        $hauler->save();

        return $hauler;
    }


    /**
     * Returns a single hauler from the database.
     *
     * @param int $id
     *
     * @return mixed
     * @throws HaulerNotFound
     */
    public function find(int $id)
    {
        $hauler = $this->haulers->with('serviceArea')->find($id);

        if ($hauler === null)
        {
            throw new HaulerNotFound(trans('messages.haulerNotFound', ['id' => $id]));
        }

        return $hauler;
    }

    /**
     * Finds multiple haulers with ids matching those passed in the only parameter.
     *
     * @param array $ids
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     * @throws HaulerNotFound
     */
    public function findIn(array $ids)
    {
        $haulers = $this->haulers->with('city')->find($ids);

        if ($haulers === null)
        {
            throw new HaulerNotFound(trans('messages.haulerNoneFound'));
        }

        return $haulers;
    }

    /**
     * Returns all haulers in the system.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->haulers->with('serviceArea')
                ->orderBy('name', 'asc')
                ->get();
    }

    /**
     * Returns a collection of haulers within the specified city.
     *
     * @param int $cityID
     * @param int $excludeID Ignore this hauler...
     *
     * @return mixed
     */
    public function inCity(int $cityID, int $excludeID = null)
    {
        if (is_numeric($excludeID))
        {
            return $this->haulers
                ->where('city_id', $cityID)
                ->where('id', '!=', $excludeID)
                ->get();
        }

        return $this->haulers
            ->where('city_id', $cityID)
            ->get();
    }

    public function applicableForLead(Lead $lead)
    {
        $needsWaste = (bool)$lead->msw_qty;
        $needsRecycling = (bool)$lead->rec_qty;

        $haulers = $this->haulers;

        if ($needsWaste)
        {
            $haulers = $haulers->where('svc_waste', 1);
        }

        if ($needsRecycling)
        {
            $haulers = $haulers->where('svc_recycle', 1);
        }

        return $haulers
                ->where('service_area_id', $lead->service_area_id)
                ->where('id', '!=', $lead->hauler_id)
                ->orderBy('name', 'asc')
                ->get();
    }


    /**
     * Converts a comma-separated string of emails
     * into a usable array.
     *
     * @param $emails
     *
     * @return array
     */
    public function parseEmails($emails)
    {
        if (is_string($emails)) {
            $emails = explode(',', $emails);

            array_walk($emails, function (&$value) {
                $value = trim($value);
            });
        }
        else if (! is_array($emails))
        {
            throw new InvalidEmails(trans('messages.haulerInvalidEmail'));
        }

        return $emails;
    }

    /**
     * Checks that we have the fields we need to create
     * a new Hauler, or throws a validation error.
     */
    protected function checkRequired()
    {
        // doesWaste and doesRecycling will return
        // false alarm when a '0'.
        $requiredFields = [
            'name', 'service_area_id', 'emails'
        ];

        $errorFields = [];

        foreach ($requiredFields as $field)
        {
            if (empty($this->$field))
            {
                $errorFields[] = $field;
            }
        }

        if (count($errorFields))
        {
            throw new MissingRequiredFields(trans('messages.haulerValidationErrors', ['fields' => implode(', ', $errorFields)]));
        }
    }
}
