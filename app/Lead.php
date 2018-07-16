<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use WasteMaster\v1\Bids\BidManager;

class Lead extends Model
{
    use SoftDeletes;

    // Status constants
    const NEW                 = 1;
    const REBIDDING           = 2;
    const BIDS_REQUESTED      = 3;
    const BID_ACCEPTED        = 4;
    const CONVERTED_TO_CLIENT = 5;

    protected $table = 'leads';

    /**
     * Cache for bid so we don't keep hitting db.
     * @var Bid
     */
    protected $acceptedBid;

    public $fillable = [
        'company', 'address', 'city_id', 'contact_name', 'contact_email', 'account_num',
        'hauler_id', 'msw_qty', 'msw_yards', 'msw_per_week', 'rec_qty', 'rec_yards', 'rec_per_week',
        'msw2_qty', 'msw2_yards', 'msw2_per_week', 'rec2_qty', 'rec2_yards', 'rec2_per_week',
        'monthly_price', 'status', 'archived', 'bid_count', 'notes', 'pre_match_sent', 'post_match_sent',
        'gross_profit', 'service_area_id'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The City/State combo the lead is in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }

    public function serviceArea()
    {
        return $this->hasOne('App\ServiceArea', 'id', 'service_area_id');
    }

    /**
     * The current Hauler they have when the lead is created.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hauler()
    {
        return $this->hasOne('App\Hauler', 'id', 'hauler_id');
    }

    /**
     * Grab the bids for this lead.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bids()
    {
        return $this->hasMany('App\Bid');
    }


    /**
     * Increments the bid count by one.
     * We keep the count separate here because count() in MySQL
     * can be slow and, at times, inaccurate.
     *
     * @return $this
     */
    public function incrementBidCount()
    {
        $this->bid_count++;

        return $this;
    }

    /**
     * Displays the cheapest bid amount this lead has received so far,
     * or 'N/A' if none.
     *
     * if $format is true, will display with currency symbol.
     *
     * @param bool $format
     *
     * @return string
     */
    public function cheapestBid($format = false)
    {
        $bids = app(BidManager::class);

        $bid = $bids->cheapestForLead($this->id);

        return $bid === null
            ? 'N/A'
            : '$'. number_format($bid->net_monthly, 2);
    }

    /**
     * Returns the actual bid object of the cheapest bid.
     *
     * @return mixed
     */
    public function cheapestBidObject()
    {
        $bids = app(BidManager::class);

        return $bids->cheapestForLead($this->id);
    }

    /**
     * Returns the lead's accepted bid object, if any.
     *
     * @return \App\Bid
     */
    public function acceptedBid()
    {
        if ($this->acceptedBid instanceof Bid) return $this->acceptedBid;

        $bid = \DB::table('bids')
                    ->where('status', Bid::STATUS_ACCEPTED)
                    ->where('lead_id', $this->id)
                    ->where('archived', 0)
                    ->first();

        $this->acceptedBid = $bid;

        return $bid;
    }

    /**
     * Returns the amount of the accepted bid.
     *
     * @return string
     */
    public function acceptedBidAmount()
    {
        $bid = $this->acceptedBid();

        return $bid === null
            ? 'N/A'
            : '$'. number_format($bid->net_monthly, 2);
    }

    /**
     * Gets the bid that belongs this Lead's current hauler.
     *
     * @return mixed
     */
    public function currentHaulersBid()
    {
        $hauler = $this->hauler;

        if (empty($hauler)) return null;

        $bid = \DB::table('bids')
                  ->where('hauler_id', $hauler->id)
                  ->where('lead_id', $this->id)
                  ->where('archived', 0)
                  ->first();

        return $bid;
    }



    public function status($raw=null)
    {
        if ($raw === true) return $this->status;

        switch ($this->status)
        {
            case self::NEW:
                return 'New';
                break;
            case self::REBIDDING:
                return 'Re-Bidding';
                break;
            case self::BIDS_REQUESTED:
                return 'Bids Requested';
                break;
            case self::BID_ACCEPTED:
                return 'Bid Accepted';
                break;
            case self::CONVERTED_TO_CLIENT:
                return 'Client';
                break;
        }

        return 'New';
    }

    public function getHaulerNameAttribute()
    {
        $hauler = $this->hauler;

        return $hauler->name ?? '';
    }


}
