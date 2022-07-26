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

        // dd($response['Data'][0]['Data']);

        foreach ($response['Data'][0]['Data'] as $key => $value) { 
            $neededBreakdown = array();
            $breakdown = explode(" ",  $value);

            $value = preg_replace('/[^a-z\s 0-9 -]/', '', strtolower($value));
            $value = preg_split('/\s+/', $value, NULL, PREG_SPLIT_NO_EMPTY);

            $current_val1 = array_search('temperature', $value );
            $current_val2 = array_search('temp', $value);
            
            if (!preg_match('~[0-9]+~', $breakdown[$current_val1 - 1])) {
                $breakdown[$current_val1 - 1] = '0mm';
                $currForcast = 'No rain';
            }else{
                $currForcast = 'Possible Rain';
            }

            if (!preg_match('~[0-9]+~', $breakdown[$current_val2 - 1])) {
                $breakdown[$current_val1 - 1] = '0mm';
                $nextForcast = 'No rain';
            }else{
                $nextForcast = 'Possible Rain';
            }

            array_push(
                $neededBreakdown, 
                $response['GridDefinition']['Latitude'][$key],  
                $response['GridDefinition']['Longitude'][$key],
                date("Y-m-d",strtotime($response['Data'][0]['Date'])),
                rtrim($breakdown[$current_val1 + 1], "."),
                $currForcast,
                $breakdown[$current_val1 - 1],
                $breakdown[$current_val2 + 1],
                $nextForcast,
                rtrim($breakdown[$current_val2 - 1], "."),        
            );


            $data = dailyWeatherData::create([
                'latitude' => $neededBreakdown[0],
                'longitude'=> $neededBreakdown[1],
                'date'=> $neededBreakdown[2],
                'current_temperature'=> $neededBreakdown[3],
                'current_rain_level'=> $neededBreakdown[5],
                'current_chance_rain'=> $neededBreakdown[4],
                'next_temperature'=> $neededBreakdown[6],
                'next_rain_level'=> $neededBreakdown[8],
                'next_chance_rain' => $neededBreakdown[7]
            ]);
            echo($data ."\r\n");
        }
        
        return response()->json($data, 200);

    }

}




            // $neededBreakdown = array();

            // $breakdown = explode(" ",  $value);

            // echo(count($breakdown). '\n');
            // switch (count($breakdown)) {
            //     case 21:
            //         array_push(
            //             $neededBreakdown,
            //             $response['GridDefinition']['Latitude'][$key],  
            //             $response['GridDefinition']['Longitude'][$key],
            //             date("Y-m-d",strtotime($response['Data'][0]['Date'])),
            //             rtrim($breakdown[11], ". "),
            //             rtrim($breakdown[9], ". "),
            //             rtrim($breakdown[8], ", "),
            //             $breakdown[20],
            //             rtrim($breakdown[18], ". "),                
            //             rtrim($breakdown[17], ", "),                
            //         );
            //       break;
            //     case 23:
            //         array_push(
            //             $neededBreakdown,
            //             $response['GridDefinition']['Latitude'][$key],
            //             $response['GridDefinition']['Longitude'][$key],
            //             date("Y-m-d",strtotime($response['Data'][0]['Date'])),
            //             rtrim($breakdown[12], ". "),
            //             rtrim($breakdown[10], ". "),
            //             rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
            //             $breakdown[22],
            //             rtrim($breakdown[20], ". "),              
            //             rtrim($breakdown[18], ", "). ' '.rtrim($breakdown[19], ", "),                
            //         );
            //       break;
            //     case 22:
            //             if($breakdown[8] == 'very'){
            //                 array_push(
            //                 $neededBreakdown,
            //                 $response['GridDefinition']['Latitude'][$key],
            //                 $response['GridDefinition']['Longitude'][$key],
            //                 date("Y-m-d",strtotime($response['Data'][0]['Date'])),
            //                 rtrim($breakdown[12], ". "),
            //                 rtrim($breakdown[10], ". "),
            //                 rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
            //                 $breakdown[21],
            //                 rtrim($breakdown[19], ". "),              
            //                 rtrim($breakdown[18], ", "), 
            //                 );
            //             }elseif($breakdown[17] == 'very'){
            //                 array_push(
            //                 $neededBreakdown,
            //                 $response['GridDefinition']['Latitude'][$key],
            //                 $response['GridDefinition']['Longitude'][$key],
            //                 date("Y-m-d",strtotime($response['Data'][0]['Date'])),
            //                 rtrim($breakdown[11], ". "),
            //                 rtrim($breakdown[9], ". "),
            //                 rtrim($breakdown[8], ", "),
            //                 $breakdown[21],
            //                 rtrim($breakdown[19], ". "),              
            //                 rtrim($breakdown[17], ", "). ' '.rtrim($breakdown[18], ", "), 
            //                 );
            //             }                                       
            //       break;
            //   }
            
            // $data = dailyWeatherData::create([
            //     'latitude' => $neededBreakdown[0],
            //     'longitude'=> $neededBreakdown[1],
            //     'date'=> $neededBreakdown[2],
            //     'current_temperature'=> $neededBreakdown[3],
            //     'current_rain_level'=> $neededBreakdown[4],
            //     'current_chance_rain'=> $neededBreakdown[5],
            //     'next_temperature'=> $neededBreakdown[6],
            //     'next_rain_level'=> $neededBreakdown[7],
            //     'next_chance_rain' => $neededBreakdown[8]
            // ]);
            // echo $data;