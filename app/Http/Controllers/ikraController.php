<?php

namespace App\Http\Controllers;

use App\Models\ikraUsers;
use App\Models\ikraContacts;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;    
use Illuminate\Support\Facades\Auth;
use App\Notifications\contactNotification;
use Illuminate\Support\Facades\Notification;

class ikraController extends Controller
{
    public function register(Request $request){

        $fields = $request->validate([
            'name'=> 'required|string',
            'email'=> 'required|string|unique:ikra_users,email',
            'password'=> 'required|string|confirmed',
        ]);
        $user = ikraUsers::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('appToken')->plainTextToken;

        return response()->json([$user ,$token], 200);
    }

    public function index(){
        $user =  ikraUsers::all();

        return response()->json($user, 200);
    }


    public function update(request $requst , $id){
       
        $fields = $requst->validate([
            'name'=> 'nullable|string',
            'email'=> 'nullable|string',
            'password'=> 'nullable|string',
        ]);

        if(array_key_exists('password',$fields)){
            $fields['password'] = bcrypt($fields['password']);
        }

        ikraUsers::where('id', $id)->update($fields);

        return response()->json('updated', 200);
    }

    public function delete($id)
    {
        ikraUsers::where('id', $id)->delete();
        return response()->json('deleted', 200);
    }

    // public function getNotifications()
    // {   
    //     $user = Auth::user();

    //     foreach ($user->unreadNotifications as $notification) {
    //         $data[] = $notification->data;
    //     }
        
    //     return  response()->json($data, 200);
    // }

    public function login(request $requst){
        $fields = $requst->validate([
            'email'=> 'required|string',
            'password'=> 'required|string',
        ]);

        $user = ikraUsers::where('email',$fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)){
                return response()->json(['message'=>'invalid Credintials'], 401);
        }

            $token = $user->createToken('appToken')->plainTextToken;

        return response()->json([$user ,$token], 200);
    }


    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json('logged out', 200);
    }


    public function storeContact(Request $request)
    {
        $user = Auth::user();
        // *******************************
        $request->merge(['ikra_id' => $user->id]);

        $fields = $request->validate([
            'ikra_id' => 'required',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'location' => 'required|string',
        ]);

        $contact = $user->ikraContacts()->create($fields);

        return response()->json($contact, 201);
    }

    public function showContacts()
    {
        $user = Auth::user();

        $contacts = $user->ikraContacts()->with('ikraUsers')->get();        
        return response()->json($contacts, 200);
    }

    public function updateContact(Request $request, $id)
    {
        $user = Auth::user();
        $fields = $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $updated = $user->ikraContacts()->where('id', $id)->update($fields);  

        return response()->json($updated, 200);
    }

    public function destroyContact($id)
    {
        $user = Auth::user();

        $deleted = $user->ikraContacts()->where('id', $id)->delete();
        return response()->json($deleted, 200);
    }

    public function notify(){
        $user = Auth::user();
        $contacts = $user->ikraContacts()->get();
        $msg = "this is a new test message please ignore";
        Notification::send($contacts, new contactNotification($msg));

    //    return smsapi()->sendMessage("0544069203", "this is a new test message please ignore")->response();

        // $api = 'v0lu4bVhQuMIU5hrxncAJ5WAE';
        // $msg=  "this is a test message please\n please ignore";
        // $msg = urlencode($msg);
        // $sender_id = "Ikra";
        // $receiver = '0505462092';
        // $url = "https://apps.mnotify.net/smsapi?key=$api&to=$receiver&msg=$msg&sender_id=$sender_id";
        // $response = file_get_contents($url);
        // if (strpos($response, '1000') !== false){
        //     return response()->json([
        //         'msg'=> $msg,
        //         'receipent'=>$receiver
        //         ]
        //     , 200);
        // }
        // else{
        //     return $response;
        // }
    }
}
