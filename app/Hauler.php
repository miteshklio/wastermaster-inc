<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hauler extends Model
{
    protected $table = 'haulers';

    public $fillable = [
        'name', 'city_id', 'service_area_id', 'svc_recycle', 'svc_waste', 'emails'
    ];

    public $timestamps = true;

    /**
     * Returns a string with the correct abbreviations
     * for the waste types this company supports.
     *
     * @return string
     */
    public function listWasteTypes()
    {
        $types = [];

        if ($this->svc_recycle)
        {
            $types[] = 'REC';
        }
        if ($this->svc_waste)
        {
            $types[] = 'MSW';
        }

        return implode(', ', $types);
    }

    /**
     * Unserializes and creates a list of the email
     * addresses associated with this object.
     *
     * @return string
     */
    public function listEmails()
    {
        if (empty($this->emails)) return '';

        return implode(', ', unserialize($this->emails));
    }

    /**
     * @return mixed
     */
    public function getEmailArrayAttribute()
    {
        return substr($this->emails, 0, 1) == 'a'
            ? unserialize($this->emails)
            : $this->emails;
    }


    public function city()
    {
        return $this->hasOne('App\City', 'id', 'city_id');
    }

    public function serviceArea()
    {
        return $this->hasOne('App\ServiceArea', 'id', 'service_area_id');
    }

}
