<?php

namespace App\Http\Controllers;

use App\Corona;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CoronaController extends Controller
{
    public function bihar()
    {
        $corona = Corona::where('updated_at','>',Carbon::now()->subMinute(1)->toDateTimeString())->get();
        /*Checking for First time*/
        if (count($corona) === 0) {
            $this->updateDatabase();
        }
        $updated_corona = Corona::all();
        $all_data = $updated_corona[0]['data'];
        $all_data_json = json_decode($all_data);
        return response()->json([
            'data' => $all_data_json->state_wise->Bihar,
            'last_updated' => $updated_corona[0]->updated_at,
        ]);
    }

    private function updateDatabase()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://corona-virus-world-and-india-data.p.rapidapi.com/api_india",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-rapidapi-host: corona-virus-world-and-india-data.p.rapidapi.com",
                "x-rapidapi-key: 2c59650585msh58e4c2844e9f3cep1d496ajsn08538162993c"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
//            echo "cURL Error #:" . $err;
        } else {
            $corona = new Corona();
            $corona->data = $response;
            $corona->save();
        }

    }
}
