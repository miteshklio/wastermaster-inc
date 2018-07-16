<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    const STATUS_LIVE     = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_CLOSED   = 3;

    protected $table = 'bids';

    public $fillable = [
        'hauler_id', 'hauler_email', 'lead_id', 'status', 'notes',
        'msw_qty', 'msw_yards', 'msw_per_week', 'rec_qty', 'rec_yards', 'rec_per_week', 'prior_total',
        'msw_price', 'rec_price', 'rec_offset', 'fuel_surcharge', 'env_surcharge', 'recovery_fee',
        'admin_fee', 'other_fees', 'net_monthly', 'gross_profit', 'no_bid'
    ];

    /**
     * The City/State combo the lead is in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
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
     * The Lead this bid was for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lead()
    {
        return $this->hasOne('App\Lead', 'id', 'lead_id');
    }

    /**
     * Takes the current instance's status value and
     * returns a human-readable string describing it.
     *
     * @return string
     */
    public function describeStatus()
    {
        $status = '';

        switch ($this->status)
        {
            case self::STATUS_LIVE:
                $status = 'Live';
                break;
            case self::STATUS_ACCEPTED:
                $status = 'Accepted';
                break;
            case self::STATUS_CLOSED:
                $status = 'Closed';
                break;
        }

        return $status;
    }

}
