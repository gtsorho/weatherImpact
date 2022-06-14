<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\dailyWeatherData;
use Illuminate\Http\Request;

class appContoller extends Controller
{
    function index(){
        $response = Http::withHeaders([
            'authkey' => '634e0aea96ce7d3961075b2a23446111'
        ])
        ->acceptJson()
        ->get('https://service.weatherimpact.com/api/data/ciat_forecast/weather_sms_text?datetime=latest');


        foreach ($response['Data'][0]['Data'] as $key => $value) {
            $neededBreakdown = array();
            $breakdown = explode(" ",  $value);
            switch (count($breakdown)) {
                case 21:
                    array_push(
                        $neededBreakdown,
                        $response['GridDefinition']['Latitude'][$key],
                        $response['GridDefinition']['Longitude'][$key],
                        date("Y-m-d",strtotime($response['Data'][0]['Date'])),
                        rtrim($breakdown[11], ". "),
                        rtrim($breakdown[9], ". "),
                        rtrim($breakdown[8], ", "),
                        $breakdown[20],
                        rtrim($breakdown[18], ". "),                
                        rtrim($breakdown[17], ", "),                
                    );
                  break;
                case 23:
                    array_push(
                        $neededBreakdown,
                        $response['GridDefinition']['Latitude'][$key],
                        $response['GridDefinition']['Longitude'][$key],
                        date("Y-m-d",strtotime($response['Data'][0]['Date'])),
                        rtrim($breakdown[12], ". "),
                        rtrim($breakdown[10], ". "),
                        rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
                        $breakdown[22],
                        rtrim($breakdown[20], ". "),              
                        rtrim($breakdown[18], ", "). ' '.rtrim($breakdown[19], ", "),                
                    );
                  break;
                case 22:
                        if($breakdown[8] == 'very'){
                            array_push(
                            $neededBreakdown,
                            $response['GridDefinition']['Latitude'][$key],
                            $response['GridDefinition']['Longitude'][$key],
                            date("Y-m-d",strtotime($response['Data'][0]['Date'])),
                            rtrim($breakdown[12], ". "),
                            rtrim($breakdown[10], ". "),
                            rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
                            $breakdown[21],
                            rtrim($breakdown[19], ". "),              
                            rtrim($breakdown[18], ", "), 
                            );
                        }elseif($breakdown[17] == 'very'){
                            array_push(
                            $neededBreakdown,
                            $response['GridDefinition']['Latitude'][$key],
                            $response['GridDefinition']['Longitude'][$key],
                            date("Y-m-d",strtotime($response['Data'][0]['Date'])),
                            rtrim($breakdown[11], ". "),
                            rtrim($breakdown[9], ". "),
                            rtrim($breakdown[8], ", "),
                            $breakdown[21],
                            rtrim($breakdown[19], ". "),              
                            rtrim($breakdown[17], ", "). ' '.rtrim($breakdown[18], ", "), 
                            );
                        }                                       
                  break;
              }
            
            $data = dailyWeatherData::create([
                'latitude' => $neededBreakdown[0],
                'longitude'=> $neededBreakdown[1],
                'date'=> $neededBreakdown[2],
                'current_temperature'=> $neededBreakdown[3],
                'current_rain_level'=> $neededBreakdown[4],
                'current_chance_rain'=> $neededBreakdown[5],
                'next_temperature'=> $neededBreakdown[6],
                'next_rain_level'=> $neededBreakdown[7],
                'next_chance_rain' => $neededBreakdown[8]
            ]);
            echo $data;
        }
    }
}
