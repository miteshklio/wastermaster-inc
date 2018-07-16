<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;

class CityController extends Controller
{
    public function autocomplete(Request $request)
    {
        $query = $request->input('query');

        $rows = \DB::table('cities')->where('name', 'like', $query.'%')
            ->limit(20)
            ->get();

        if ($rows === null)
        {
            return response()->json(['cities' => null]);
        }

        $cities = [];

        foreach ($rows as $row)
        {
            $cities[] = $row->name;
        }

        return response()->json(['cities' => $cities]);
    }

}
