<?php namespace WasteMaster\v1\Leads;

use App\Bid;
use App\City;
use App\Lead;
use WasteMaster\v1\Bids\BidManager;
use WasteMaster\v1\Bids\BidNotFound;
use WasteMaster\v1\Clients\ClientManager;

class LeadManager
{
    /**
     * @var \App\Lead
     */
    protected $leads;

    /**
     * @var \App\City
     */
    protected $cities;

    /**
     * DB Columns
     */
    protected $company;
    protected $address;
    protected $city_id;
    protected $contact_name;
    protected $contact_email;
    protected $account_num;
    protected $hauler_id;
    protected $msw_qty;
    protected $msw_yards;
    protected $msw_per_week;
    protected $rec_qty;
    protected $rec_yards;
    protected $rec_per_week;
    protected $msw2_qty;
    protected $msw2_yards;
    protected $msw2_per_week;
    protected $rec2_qty;
    protected $rec2_yards;
    protected $rec2_per_week;
    protected $monthly_price;
    protected $status;
    protected $archived;
    protected $bid_count;
    protected $notes;
    protected $service_area_id;

    public function __construct(Lead $leads, City $cities)
    {
        $this->leads = $leads;
        $this->cities = $cities;
    }

    public function setCompany(string $company)
    {
        $this->company = $company;

        return $this;
    }

    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Sets the city_id to use when creating/updating a Lead.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setCityID(int $id)
    {
        $this->city_id = $id;

        return $this;
    }

    /**
     * Sets the service_area_id to use when creating/updating a Lead.
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

        $this->city_id = $city->id;

        return $this;
    }

    public function setContactName(string $name)
    {
        $this->contact_name = $name;

        return $this;
    }

    public function setContactEmail(string $email)
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new InvalidEmail(trans('messages.invalidEmailAddress'));
        }

        $this->contact_email = $email;

        return $this;
    }

    public function setAccountNum(string $num)
    {
        $this->account_num = $num;

        return $this;
    }

    public function setHaulerID(int $id)
    {
        $this->hauler_id = $id;

        return $this;
    }

    public function setWaste(int $qty=null, $yards=null, $frequency=null)
    {
        $this->msw_qty      = $qty;
        $this->msw_yards    = $yards;
        $this->msw_per_week = $frequency;

        return $this;
    }

    public function setRecycling(int $qty=null, $yards=null, $frequency=null)
    {
        $this->rec_qty      = $qty;
        $this->rec_yards    = $yards;
        $this->rec_per_week = $frequency;

        return $this;
    }

    public function setWaste2(int $qty=null, $yards=null, $frequency=null)
    {
        $this->msw2_qty      = $qty;
        $this->msw2_yards    = $yards;
        $this->msw2_per_week = $frequency;

        return $this;
    }

    public function setRecycling2(int $qty=null, $yards=null, $frequency=null)
    {
        $this->rec2_qty      = $qty;
        $this->rec2_yards    = $yards;
        $this->rec2_per_week = $frequency;

        return $this;
    }

    public function setMonthlyPrice(float $price)
    {
        $this->monthly_price = $price;

        return $this;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    public function setArchived(bool $archived = true)
    {
        $this->archived = $archived;

        return $this;
    }

    public function setBidCount(int $count)
    {
        $this->bids = $count;

        return $this;
    }

    public function setNotes(string $notes)
    {
        $this->notes = $notes;

        return $this;
    }

    public function create()
    {
        $this->checkRequired();

        // Does a Lead with this address
        // already exist?
        if ($this->leads->where(['address' => $this->address, 'city_id' => $this->city_id])->count())
        {
            throw new LeadExists(trans('messages.leadExists'));
        }

        $lead = $this->leads->create([
            'company' => $this->company,
            'address' => $this->address,
            'city_id' => (int)$this->city_id,
            'service_area_id' => (int)$this->service_area_id,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'account_num' => $this->account_num,
            'hauler_id' => $this->hauler_id,
            'msw_qty' => $this->msw_qty,
            'msw_yards' => $this->msw_yards,
            'msw_per_week' => $this->msw_per_week,
            'rec_qty' => $this->rec_qty,
            'rec_yards' => $this->rec_yards,
            'rec_per_week' => $this->rec_per_week,
            'msw2_qty' => $this->msw2_qty,
            'msw2_yards' => $this->msw2_yards,
            'msw2_per_week' => $this->msw2_per_week,
            'rec2_qty' => $this->rec2_qty,
            'rec2_yards' => $this->rec2_yards,
            'rec2_per_week' => $this->rec2_per_week,
            'monthly_price' => $this->monthly_price,
            'status' => Lead::NEW,
            'archived' => 0,
            'bid_count' => 0,
            'notes' => $this->notes
        ]);

        $this->reset();

        return $lead;
    }

    public function update($id)
    {
        $lead = $this->leads->find($id);

        if ($lead === null)
        {
            throw new LeadNotFound(trans('messages.leadNotFound', ['id' => $id]));
        }

        $fields = [];

        if ($this->company !== null) $fields['company'] = $this->company;
        if ($this->address !== null) $fields['address'] = $this->address;
        if ($this->service_area_id !== null) $fields['service_area_id'] = $this->service_area_id;
        if ($this->contact_name !== null) $fields['contact_name'] = $this->contact_name;
        if ($this->contact_email !== null) $fields['contact_email'] = $this->contact_email;
        if ($this->account_num !== null) $fields['account_num'] = $this->account_num;
        if ($this->hauler_id !== null) $fields['hauler_id'] = $this->hauler_id;
        if ($this->msw_qty !== null) $fields['msw_qty'] = $this->msw_qty;
        if ($this->msw_yards !== null) $fields['msw_yards'] = $this->msw_yards;
        if ($this->msw_per_week !== null) $fields['msw_per_week'] = $this->msw_per_week;
        if ($this->rec_qty !== null) $fields['rec_qty'] = $this->rec_qty;
        if ($this->rec_yards !== null) $fields['rec_yards'] = $this->rec_yards;
        if ($this->rec_per_week !== null) $fields['rec_per_week'] = $this->rec_per_week;
        if ($this->msw2_qty !== null) $fields['msw2_qty'] = $this->msw2_qty;
        if ($this->msw2_yards !== null) $fields['msw2_yards'] = $this->msw2_yards;
        if ($this->msw2_per_week !== null) $fields['msw2_per_week'] = $this->msw2_per_week;
        if ($this->rec2_qty !== null) $fields['rec2_qty'] = $this->rec2_qty;
        if ($this->rec2_yards !== null) $fields['rec2_yards'] = $this->rec2_yards;
        if ($this->rec2_per_week !== null) $fields['rec2_per_week'] = $this->rec2_per_week;
        if ($this->monthly_price !== null) $fields['monthly_price'] = $this->monthly_price;
        if ($this->status !== null) $fields['status'] = $this->status;
        if ($this->archived !== null) $fields['archived'] = $this->archived;
        if ($this->bid_count !== null) $fields['bid_count'] = $this->bid_count;
        if ($this->notes !== null) $fields['notes'] = $this->notes;

        if (! count($fields))
        {
            throw new NothingToUpdate(trans('messages.nothingToUpdate'));
        }

        $lead->fill($fields);
        $lead->save();

        $this->reset();

        return $lead;
    }

    public function find(int $id)
    {
        $lead = $this->leads->with(['city', 'hauler'])->find($id);

        if ($lead === null)
        {
            throw new LeadNotFound(trans('messages.leadNotFound', ['id' => $id]));
        }

        return $lead;
    }

    public function delete(int $id)
    {
        $lead = $this->find($id);

        return $lead->delete();
    }

    public function archive(int $id, bool $archived = true)
    {
        $lead = $this->find($id);

        $lead->archived = $archived;
        return $lead->save();
    }

    /**
     * Copies a lead to the clients table along with all
     * current information for the lowest bidder.
     *
     * If the client already exists, will copy the new
     * bid information over.
     *
     * @param int                                   $leadID
     * @param \WasteMaster\v1\Clients\ClientManager $clients
     *
     * @throws \WasteMaster\v1\Bids\BidNotFound
     * @throws \WasteMaster\v1\Leads\LeadNotFound
     */
    public function convertToClient(int $leadID, ClientManager $clients)
    {
        // Get the Lead
        $lead = $this->leads->find($leadID);

        if ($lead === null)
        {
            throw new LeadNotFound(trans('messages.leadNotFound'));
        }

        // Get the lowest bid
        $bid = $lead->acceptedBid();

        if ($bid === null)
        {
            throw new BidNotFound(trans('messages.bidNotFound'));
        }

        // Archive the lead
        $lead->archived = 1;
        $lead->status = Lead::CONVERTED_TO_CLIENT;
        $lead->save();

        // Archive the bids
        $bids = $lead->bids;

        foreach ($bids as $b)
        {
            if ($b->id == $bid->id) continue;

            $b->archived = 1;
            $b->save();
        }

        // Get or create the client
        $client = $clients->findOrCreate([
            'company' => $lead->company,
            'address' => $lead->address,
            'city_id' => $lead->city_id,
            'archived' => 0
        ]);

        return $clients->setContactName($lead->contact_name)
            ->setContactEmail($lead->contact_email)
            ->setAccountNum($lead->account_num)
            ->setWaste($lead->msw_qty, $lead->msw_yards, $lead->msw_per_week)
            ->setRecycling($lead->rec_qty, $lead->rec_yards, $lead->rec_per_week)
            ->setWaste2($lead->msw2_qty, $lead->msw2_yards, $lead->msw2_per_week)
            ->setRecycling2($lead->rec2_qty, $lead->rec2_yards, $lead->rec2_per_week)
            ->setPriorTotal($lead->monthly_price)
            ->setWastePrice($bid->msw_price ?? 0)
            ->setRecyclePrice($bid->rec_price ?? 0)
            ->setRecycleOffset($bid->rec_offset ?? 0)
            ->setFuelSurcharge($bid->fuel_surcharge ?? 0)
            ->setEnvironmentalSurcharge($bid->env_surcharge ?? 0)
            ->setRecoveryFee($bid->recovery_fee ?? 0)
            ->setAdminFee($bid->admin_fee ?? 0)
            ->setOtherFees($bid->other_fees ?? 0)
            ->setHaulerID($bid->hauler_id)
            ->setLeadID($lead->id)
            ->setGross($bid->gross_profit)
            ->setNet($bid->net_monthly)
            ->setTotal($bid->net_monthly + $bid->gross_profit)
            ->setServiceAreaID($lead->service_area_id)
            ->update($client->id);
    }

    /**
     * Sets the date for the lead's pre bid match request was sent
     * to the current time.
     *
     * @param int $leadID
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function setPreMatchDate(int $leadID)
    {
        $lead = $this->find($leadID);

        $lead->pre_match_sent = date('Y-m-d H:i:s');
        $lead->save();

        return $lead;
    }

    /**
     * Sets the date for the lead's post bid match request was sent
     * to the current time.
     *
     * @param int $leadID
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function setPostMatchDate(int $leadID)
    {
        $lead = $this->find($leadID);

        $lead->post_match_sent = date('Y-m-d H:i:s');
        $lead->save();

        return $lead;
    }

    /**
     * Determines if the current hauler either has the lowest bid
     * or has a matching bid.
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    public function doesCurrentHaulerMatch(Lead $lead): bool
    {
        $cheapest = $lead->cheapestBidObject();
        $current  = $lead->currentHaulersBid();

        if (empty($current) || empty($cheapest)) return false;

        if (round($current->net_monthly, 0) === round($cheapest->net_monthly,0)) return true;

        return false;
    }

    /**
     * Show Post Bid Matching? Only show if lowest bid is lower than
     * current monthly total - unless rebidding, than ignore gross_profit
     * and work off of net_monthly here.
     *
     * @param \App\Lead $lead
     *
     * @return bool
     */
    public function shouldShowPostMatchBid(Lead $lead, Bid $lowBid=null)
    {
        // If no bids have been received then it's an easy out.
        if ($lead->bid_count == 0 || $lowBid == null) return false;

        // If lowest Bid is higher than current total, don't show.
        if ($lead->status(true) === Lead::REBIDDING)
        {
            return ($lead->monthly_price - $lead->gross_profit) >= $lowBid->net_monthly;
        }
        else
        {
            return $lead->monthly_price >= $lowBid->net_monthly;
        }
    }


    public function findOrCreate(array $params)
    {
        return $this->leads->firstOrCreate($params);
    }


    /**
     * Used internally after a create or udpate
     * to reset the class properties.
     */
    protected function reset()
    {
        $this->company = null;
        $this->address = null;
        $this->city_id = null;
        $this->service_area_id = null;
        $this->contact_name = null;
        $this->contact_email = null;
        $this->account_num = null;
        $this->hauler_id = null;
        $this->msw_qty = null;
        $this->msw_yards = null;
        $this->msw_per_week = null;
        $this->rec_qty = null;
        $this->rec_per_week = null;
        $this->rec_yards = null;
        $this->monthly_price = null;
        $this->status = null;
        $this->archived = null;
        $this->bid_count = null;
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
            'address', 'contact_name', 'contact_email', 'account_num', 'monthly_price'
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
            throw new MissingRequiredFields(trans('messages.leadValidationErrors', ['fields' => implode(', ', $errorFields)]));
        }
    }

}
