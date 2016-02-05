<?php namespace BikePonyExpress\Http\Controllers;

use BikePonyExpress\Http\Requests;
use BikePonyExpress\Http\Controllers\Controller;
use BikePonyExpress\Delivery;
use BikePonyExpress\Agent;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;
use Input;
use Response;
use DateTime;
use DateInterval;


class WelcomeController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('guest');
	}
	
	public function index()
	{
		return view('index');
	}

	public function request()
	{
		return view('request');
	}

	public function saveRequest()
	{
		$rules = array(
      'sender_address'   => 'required',
      'sender_position'   => 'required',
      'sender_email'   => 'required|email',
      'recipient_address'   => 'required',
      'recipient_position'   => 'required',
      'recipient_email'   => 'required|email'
    );

    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails()) 
    {
    	return view('request');
    } 
    else 
    {

      $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '0')->first();
      
      if(!$agent)
        $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '1')->first();
      
      if(!$agent)
        $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '2')->first();
      
      if(!$agent) {
        return view('unavailable');
      }

      $delivery = new Delivery;
      $delivery->sender_address 		= Input::get('sender_address');
      $delivery->sender_position 		= Input::get('sender_position');
      $delivery->sender_email 			= Input::get('sender_email');
      $delivery->recipient_address 	= Input::get('recipient_address');
      $delivery->recipient_position = Input::get('recipient_position');
      $delivery->recipient_email   	= Input::get('recipient_email');
      $delivery->agent_id           = $agent->id;

      $dt = new DateTime;
      $dt_string = $dt->format('m-d-y H:i:s');
			$delivery->submission_time = $dt;
			$half_hour = new DateInterval("PT30M");
			$one_hour = new DateInterval("PT1H");
			$delivery->estimated_pickup = (new DateTime)->add($half_hour);
			$delivery->estimated_delivery = (new DateTime)->add($one_hour);

			// ACHTUNG!


			$tracking_code = md5($delivery->sender_address . $delivery->recipient_address . $dt_string);
			$delivery_code = md5($delivery->recipient_address . $delivery->sender_address . $dt_string);

			$delivery->tracking_code = substr($tracking_code, 0, 8);
			$delivery->delivery_code = substr($delivery_code, 0, 8);


      $delivery->save();



      Mail::queue('emails.newsender', ['tracking_code' => $delivery->tracking_code], function($message) use($delivery)
			{
					$message->from('support@bikeponyexpress.me', 'BikePonyExpress');
			    $message->to($delivery->sender_email)->subject('BikePonyExpress: Pickup requested!');
			});

			Mail::queue('emails.newrecipient', ['tracking_code' => $delivery->tracking_code, 'delivery_code' => $delivery->delivery_code], function($message) use($delivery)
			{
					$message->from('support@bikeponyexpress.me', 'BikePonyExpress');
			    $message->to($delivery->recipient_email)->subject('BikePonyExpress: You got mail!');
			});

    	$latlng = explode(',', $delivery->sender_position);

      $lat = $latlng[0];
    	$lng = $latlng[1];

     	$current_dt = new DateTime;

      $data = array(
      	'tracking_code' => $delivery->tracking_code,
      	'delivery' => $delivery,
      	'title' => 'Pickup requested!',
      	'lat' => $lat,
      	'lng' => $lng,
      	'state' => "Waiting for pickup",
      	'estimated_pickup' => $delivery->estimated_pickup->format('H:i'),
      	'estimated_delivery' => $delivery->estimated_delivery->format('H:i'),
      	'current_dt' => $current_dt->format('D, d M Y, H:i')
      );

      return view('track', $data);
    }
	}

	public function track($tracking_code)
	{
		if(!empty($tracking_code)){

			$delivery = Delivery::where("tracking_code", $tracking_code)->first();

			if(!$delivery)
				abort(404);

      $latlng = null;

      if($delivery->state == 0)
      {
      	$latlng = explode(',', $delivery->sender_position);
      }
      else if($delivery->state == 1 && $delivery->agent)
      {
      	$latlng = explode(',', $delivery->agent->last_position);
      }
      else if($delivery->state == 2)
      {
      	$latlng = explode(',', $delivery->recipient_position);
      }

      $lat = $latlng[0];
    	$lng = $latlng[1];

      $state_text = array(
      	"Waiting for pickup",
      	"Being delivered",
      	"Delivered"
     	);

     	$current_dt = new DateTime;
		
			$data = array(
      	'tracking_code' => $tracking_code,
      	'delivery' => $delivery,
      	'title' => 'Delivery Tracking',
      	'lat' => $lat,
      	'lng' => $lng,
      	'state' => $state_text[$delivery->state],
      	'estimated_pickup' => (new DateTime($delivery->estimated_pickup))->format('H:i'),
      	'estimated_delivery' => (new DateTime($delivery->estimated_delivery))->format('H:i'),
      	'current_dt' => $current_dt->format('D, d M Y, H:i')
      );

			return view('track', $data);
		}
		else
		{
			abort(404);
		}

	}

}
