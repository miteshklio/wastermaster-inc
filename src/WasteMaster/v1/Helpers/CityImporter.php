<?php namespace WasteMaster\v1\Helpers;

/**
 * Class CityImporter
 *
 * Imports the resources/cities.txt file, which is a tab-delimited
 * file with city, states, and more.
 *
 * @package WasteMaster\v1\Helpers
 */
class CityImporter
{
    public function import(string $file)
    {
        $path = resource_path($file);

        if (! is_file($path))
        {
            throw new \InvalidArgumentException('Cities file cannot be found at: '. $path);
        }

        $this->truncateTables();

        /*
         * Read the file in, line by line, and create our cities and states.
         */

        $headers = [
            'country_code' => 0,
            'postal_code'  => 1,
            'city_name'    => 2,
            'state_name'   => 3,
            'state_code'   => 4,
            'county_name'  => 5,
            'county_code'  => 6,
            'unused_1'     => 7,
            'unused_2'     => 8,
            'latitude'     => 9,
            'longitude'    => 10,
            'accuracy'     => 11
        ];

        // we need to record last state and city information so that we
        // don't hit the database with duplicate data.
        $lastStateID = 0;
        $lastState   = null;
        $lastStateCode = null;
        $lastCity    = null;

        $fp = fopen($path, 'r');

        while (! feof($fp))
        {
            $line = fgets($fp, 2048);

            $data = str_getcsv($line, "\t");

            $state     = $data[$headers['state_name']] ?? null;
            $stateCode = $data[$headers['state_code']] ?? null;
            $city      = $data[$headers['city_name']] ?? null;

            if (! empty($state) && $state != $lastState)
            {
                $lastStateID = \DB::table('states')
                    ->insertGetID([
                        'name' => $state,
                        'abbr' => $stateCode
                    ]);

                $lastState = $state;
                $lastStateCode = $stateCode;
            }

            if (! empty($city))
            {
                $city = implode(', ', [$city, $lastStateCode]);
            }

            if (! empty($city) && $city != $lastCity)
            {
                \DB::table('cities')->insert([
                    'name' => $city,
                    'state_id' => $lastStateID
                ]);

                $lastCity = $city;
            }
        }

        fclose($fp);
    }

    /**
     * Ensure a clean starting state in case of migration:refresh commands
     */
    protected function truncateTables()
    {
        \DB::table('cities')->truncate();
        \DB::table('states')->truncate();
    }


}
