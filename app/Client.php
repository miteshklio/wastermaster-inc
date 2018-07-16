<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $table = 'clients';

    public $fillable = [
        'company', 'address', 'city_id', 'contact_name', 'contact_email', 'account_num', 'hauler_id',
        'msw_qty', 'msw_yards', 'msw_per_week', 'rec_qty', 'rec_yards', 'rec_per_week', 'prior_total',
        'msw2_qty', 'msw2_yards', 'msw2_per_week', 'rec2_qty', 'rec2_yards', 'rec2_per_week',
        'msw_price', 'rec_price', 'rec_offset', 'fuel_surcharge', 'env_surcharge', 'recovery_fee',
        'admin_fee', 'other_fees', 'net_monthly', 'gross_profit', 'total', 'archived', 'lead_id', 'service_area_id'
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

    public function lead()
    {
        return $this->hasOne('App\Lead', 'id', 'lead_id');
    }


}
