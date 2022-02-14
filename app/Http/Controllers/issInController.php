<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class issInController extends Controller
{
    //
    public static function GetLocation (Request $request){
        try {

            $client = new Client(['base_uri' => 'https://api.wheretheiss.at/']);

            $start = now()->subHour(); 
            $end = now()->addHour();

            if(
                $request->has('date') &&
                $request->has('time')
            ) {
                $start = sprintf("%s %s", $request->date, $request->time);

                $start = Carbon::parse($start)->subHour();
                $end = $start->copy()->addHours(2);
            }

            $timestamps = collect(CarbonInterval::minutes(10)->toPeriod($start, $end)->toArray());
            $formatted = $timestamps->map(function($d) {
                return $d->timestamp;
            })->implode(',');

            $response = $client->request('GET', 'v1/satellites/25544/positions?timestamps=' . $formatted);

            $positions = json_decode($response->getBody()->getContents(), true);

            foreach($positions as $i => $position) {
                $latitude = $position['latitude'];
                $longitude  = $position['longitude'];

                $locationRes = $client->request('GET', '/v1/coordinates/'. sprintf("%s,%s", $latitude, $longitude));

                $position['location_details'] = json_decode($locationRes->getBody()->getContents(), true);

                $positions[$i] = $position;
            }

            return view('locations')->with('positions', $positions);

        } catch(\Exception $e) {
            return 'Rate limited error.';
        }

    }
}
