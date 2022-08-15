<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use App\Models\dailyWeatherData;
use App\Models\address;
use App\Models\liveweather;
use Illuminate\Http\Request;
use Carbon\Carbon;


class dailyWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Artisan command to retrive and store data daily';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'authkey' => '634e0aea96ce7d3961075b2a23446111'
        ])
        ->acceptJson()
        ->get('https://service.weatherimpact.com/api/data/ciat_forecast/weather_sms_text?datetime=latest');

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
                $breakdown[$current_val2 - 1] = '0mm';
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
            
            $address = address::where('latitude', $neededBreakdown[0])->where('longitude',$neededBreakdown[1])->first();
            if($address){

                $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt($curl,CURLOPT_TIMEOUT, 3000);
    
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => "https://api.openweathermap.org/data/2.5/weather?lat=$neededBreakdown[0]&lon=$neededBreakdown[1]&appid=c2df596100d32243755dd4827069e38b&units=metric",
                    CURLOPT_USERAGENT => 'Codular Sample cURL Request'
                ));
                // Send the request & save response to $resp
                ini_set('max_execution_time', 1800);
                $resp = curl_exec($curl);
                $res = json_decode($resp, true);
    
                // Use parse_url() function to parse the URL
                // and return an associative array which
                // contains its various components
                $url_components = parse_url("https://api.openweathermap.org/data/2.5/weather?lat=$neededBreakdown[0]&lon=$neededBreakdown[1]&appid=c2df596100d32243755dd4827069e38b&units=metric");
                    
                // Use parse_str() function to parse the
                // string passed via URL
                parse_str($url_components['query'], $params);                    
    
                // print_r($res );
                if($res){
                    // dd($res);
                    if(array_key_exists('rain',$res)){
                        $rain = $res['rain']['1h'];
                    }else{
                        $rain = 0.00;
                    }
                $data = liveweather::create([
                    'latitude'=> $res['coord']['lat'],
                    'longitude'=> $res['coord']['lon'],
                    'temperature' => $res['main']['temp'],
                    'humidity' => $res['main']['humidity'],
                    'rain' => $rain,
                    'description' => $res['weather'][0]['description'],
                ]);     
                }else{
                    $data = liveweather::create([
                        'latitude'=> $neededBreakdown[0],
                        'longitude'=> $neededBreakdown[1],
                        'temperature' => 0.0,
                        'humidity' => 0.0,
                        'rain' => 0.0,
                        'description' => 0.0,
                    ]); 

                }
    
                                
                // Close request to clear up some resources
                curl_close($curl);

                $data = dailyWeatherData::create([
                    'address_id'=> $address->id,
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
            }

            
        }















































        // $response = Http::withHeaders([
        //     'authkey' => '634e0aea96ce7d3961075b2a23446111'
        // ])
        // ->acceptJson()
        // ->get('https://service.weatherimpact.com/api/data/ciat_forecast/weather_sms_text?datetime=latest');

        //     dd($response['data']);
        // foreach ($response['Data'][0]['Data'] as $key => $value) {
        //     $neededBreakdown = array();
        //     $breakdown = explode(" ",  $value);
        //     switch (count($breakdown)) {
        //         case 21:
        //             array_push(
        //                 $neededBreakdown,
        //                 $response['GridDefinition']['Latitude'][$key],
        //                 $response['GridDefinition']['Longitude'][$key],
        //                 date("Y-m-d",strtotime($response['Data'][0]['Date'])),
        //                 rtrim($breakdown[11], ". "),
        //                 rtrim($breakdown[9], ". "),
        //                 rtrim($breakdown[8], ", "),
        //                 $breakdown[20],
        //                 rtrim($breakdown[18], ". "),                
        //                 rtrim($breakdown[17], ", "),                
        //             );
        //           break;
        //         case 23:
        //             array_push(
        //                 $neededBreakdown,
        //                 $response['GridDefinition']['Latitude'][$key],
        //                 $response['GridDefinition']['Longitude'][$key],
        //                 date("Y-m-d",strtotime($response['Data'][0]['Date'])),
        //                 rtrim($breakdown[12], ". "),
        //                 rtrim($breakdown[10], ". "),
        //                 rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
        //                 $breakdown[22],
        //                 rtrim($breakdown[20], ". "),              
        //                 rtrim($breakdown[18], ", "). ' '.rtrim($breakdown[19], ", "),                
        //             );
        //           break;
        //         case 22:
        //                 if($breakdown[8] == 'very'){
        //                     array_push(
        //                     $neededBreakdown,
        //                     $response['GridDefinition']['Latitude'][$key],
        //                     $response['GridDefinition']['Longitude'][$key],
        //                     date("Y-m-d",strtotime($response['Data'][0]['Date'])),
        //                     rtrim($breakdown[12], ". "),
        //                     rtrim($breakdown[10], ". "),
        //                     rtrim($breakdown[8], ", ").' '.rtrim($breakdown[9], ", "),
        //                     $breakdown[21],
        //                     rtrim($breakdown[19], ". "),              
        //                     rtrim($breakdown[18], ", "), 
        //                     );
        //                 }elseif($breakdown[17] == 'very'){
        //                     array_push(
        //                     $neededBreakdown,
        //                     $response['GridDefinition']['Latitude'][$key],
        //                     $response['GridDefinition']['Longitude'][$key],
        //                     date("Y-m-d",strtotime($response['Data'][0]['Date'])),
        //                     rtrim($breakdown[11], ". "),
        //                     rtrim($breakdown[9], ". "),
        //                     rtrim($breakdown[8], ", "),
        //                     $breakdown[21],
        //                     rtrim($breakdown[19], ". "),              
        //                     rtrim($breakdown[17], ", "). ' '.rtrim($breakdown[18], ", "), 
        //                     );
        //                 }                                       
        //           break;
        //       }
            
        //     $data = dailyWeatherData::create([
        //         'latitude' => $neededBreakdown[0],
        //         'longitude'=> $neededBreakdown[1],
        //         'date'=> $neededBreakdown[2],
        //         'current_temperature'=> $neededBreakdown[3],
        //         'current_rain_level'=> $neededBreakdown[4],
        //         'current_chance_rain'=> $neededBreakdown[5],
        //         'next_temperature'=> $neededBreakdown[6],
        //         'next_rain_level'=> $neededBreakdown[7],
        //         'next_chance_rain' => $neededBreakdown[8]
        //     ]);
        //     echo $data;
        // }
    }
}
