<?php namespace BikePonyExpress\Http\Controllers;

use BikePonyExpress\Http\Requests;
use BikePonyExpress\Http\Controllers\Controller;
use BikePonyExpress\Delivery;
use BikePonyExpress\Agent;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;
use Input;
use Response;
use DateTime;
use DateInterval;

use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;


class DeliveryController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$deliveries = Delivery::all();
		return !$deliveries? Response::json("Error", 400) : $deliveries;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
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
      return Response::json("Error", 400);
    } 
    else 
    {

      $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '0')->first();
      
      if(!$agent)
        $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '1')->first();
      
      if(!$agent)
        $agent = Agent::where("status", true)->whereHas('deliveries', function($q){ $q->where('state', '<', 2); }, '=', '2')->first();
      
      if(!$agent) {
        return Response::json("Error", 503);
      }

      $delivery = new Delivery;
      $delivery->sender_address     = Input::get('sender_address');
      $delivery->sender_position    = Input::get('sender_position');
      $delivery->sender_email       = Input::get('sender_email');
      $delivery->recipient_address  = Input::get('recipient_address');
      $delivery->recipient_position = Input::get('recipient_position');
      $delivery->recipient_email    = Input::get('recipient_email');
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

      return $delivery;
    }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$delivery = Delivery::find($id);
		return !$delivery? Response::json("Error", 400) : $delivery;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$delivery = Delivery::find($id);

    if(!$delivery)
    {
      return Response::json("Error", 400);
    }
    else
    {

	    $rules = array(
	      'sender_email'    => 'email',
	      'recipient_email' => 'email',
        // 'agent_id'        => 'integer'
	    );

	    $validator = Validator::make(Input::all(), $rules);

	    if ($validator->fails()) 
	    {
	      return Response::json("Error", 400);
	    } 
	    else 
	    {
        // No, not in production, baby!
        // if(Input::get('agent_id') !== null) {

        //   $agent = Agent::find((int)Input::get('agent_id'));
        //   if(!$agent)
        //   {
        //     return Response::json("Error", 400);
        //   }
        //   else
        //   {  
        //     $delivery->agent_id  = Input::get('agent_id');
        //   }
        // }

        //Okay, first let's check if the  the current logged agent
        $userId = Authorizer::getResourceOwnerId();
        $agent = Agent::where('user_id', $userId)->first();
        if(!$agent)
          return Response::json("Error", 400);

        // An agent can modify just his own deliveries
            if($delivery->agent_id != $agent->id)
              return Response::json("Error", 403);

	      if(Input::get('sender_address') !== null)
	        $delivery->sender_address  = Input::get('sender_address');

        if(Input::get('sender_position') !== null)
          $delivery->sender_position  = Input::get('sender_position');

	      if(Input::get('sender_info') !== null)
	        $delivery->sender_info  = Input::get('sender_info');
	      
	      if(Input::get('sender_email') !== null)
	        $delivery->sender_email  = Input::get('sender_email');

	      if(Input::get('recipient_address') !== null)
	        $delivery->recipient_address  = Input::get('recipient_address');

        if(Input::get('recipient_position') !== null)
          $delivery->recipient_position  = Input::get('recipient_position');

	      if(Input::get('recipient_info') !== null)
	        $delivery->recipient_info  = Input::get('recipient_info');

	      if(Input::get('recipient_email') !== null)
	        $delivery->recipient_email  = Input::get('recipient_email');

	      if(Input::get('state') !== null)
	        $delivery->state  = Input::get('state');

	      if(Input::get('pickup_time') !== null)
	        $delivery->pickup_time  = Input::get('pickup_time');

	      if(Input::get('delivery_time') !== null)
	        $delivery->delivery_time  = Input::get('delivery_time');

	      if(Input::get('estimated_pickup') !== null)
	        $delivery->estimated_pickup  = Input::get('estimated_pickup');

	      if(Input::get('estimated_delivery') !== null)
	        $delivery->estimated_delivery  = Input::get('estimated_delivery');
	      
	      $delivery->save();

	      return $delivery;
    	}
    }
	}

  public function updateDeliveries()
  {
    //Okay, first let's check if the  the current logged agent
    $userId = Authorizer::getResourceOwnerId();
    $agent = Agent::where('user_id', $userId)->first();
    if(!$agent)
      return Response::json("Error", 400);

    $deliveriesJSON = Input::get('deliveries');

    if(!empty($deliveriesJSON)) {

      $deliveries = json_decode($deliveriesJSON, true);

      if($deliveries !== null && count($deliveries) > 0)
      {
        foreach ($deliveries as $id => $updates)
        {
          $delivery = Delivery::find(intval($id));

          if($delivery)
          {
            // An agent can modify just his own deliveries
            if($delivery->agent_id != $agent->id)
              return Response::json("Error", 403);

            // Estimated times

            $notifySender = false;
            $notifyRecipient = false;


            if(!empty($updates["estimated_pickup"]))
            {
              if(static::validateDate($updates["estimated_pickup"])){

                if($delivery->estimated_pickup != $updates["estimated_pickup"])
                {
                  if(static::dateDifference($delivery->estimated_pickup, $updates["estimated_pickup"]) > 300)
                  {
                    $notifySender = true;
                  }
                  $delivery->estimated_pickup = $updates["estimated_pickup"];
                }

              }
            }

            if(!empty($updates["estimated_delivery"]))
            {
              if(static::validateDate($updates["estimated_delivery"])){
                if ($delivery->estimated_delivery != $updates["estimated_delivery"])
                {
                  if(static::dateDifference($delivery->estimated_delivery, $updates["estimated_delivery"]) > 300)
                  {
                    $notifyRecipient = true;
                  }
                  $delivery->estimated_delivery = $updates["estimated_delivery"];
                }
              }
            }

            // Actual times
            if(!empty($updates["pickup_time"]))
            {
              if(static::validateDate($updates["pickup_time"]))
              {
                $delivery->pickup_time = $updates["pickup_time"];

                if($delivery->state == 0) 
                  $delivery->state = 1;
              }
            }

            if(!empty($updates["delivery_time"]))
            {
              if(static::validateDate($updates["delivery_time"]))
              {
                $delivery->delivery_time = $updates["delivery_time"];

                if($delivery->state < 2) 
                  $delivery->state = 2;
              }
            }

            // // Delivery state (explicit)
            // if($updates["state"] != null)
            // {
            //   $state = intval($updates["state"]);
            //   if($state < 0 || $state > 2)
            //     return Response::json("Error", 400);

            //   $delivery->state = $state;
            // }

            $delivery->save();

            if($notifySender) {
              $estimated_pickup = (new DateTime($delivery->estimated_pickup))->format('H:i');
              Mail::queue('emails.updatesender', ['tracking_code' => $delivery->tracking_code, 'estimated_pickup' => $estimated_pickup], function($message) use($delivery)
              {
                  $message->from('support@bikeponyexpress.me', 'BikePonyExpress');
                  $message->to($delivery->sender_email)->subject('BikePonyExpress: Pickup time updated!');
              });
            }

            if($notifyRecipient) {
              $estimated_delivery = (new DateTime($delivery->estimated_delivery))->format('H:i');
              Mail::queue('emails.updaterecipient', ['tracking_code' => $delivery->tracking_code, 'estimated_delivery' => $estimated_delivery], function($message) use($delivery)
              {
                  $message->from('support@bikeponyexpress.me', 'BikePonyExpress');
                  $message->to($delivery->recipient_email)->subject('BikePonyExpress: Delivery time updated!');
              });
            }
          }
          else
          {
            return Response::json("Error", 400);
          }
        }

        $updatedDeliveries = $agent->deliveries()->where('state', '<', 2)->orderBy('updated_at', 'desc')->get();
        return !$updatedDeliveries? Response::json("Error", 400) : $updatedDeliveries;
      }
    }

    return Response::json("Error", 400);
  }

	// Return the agent assigned to a certain delivery.

  public function agent($id)
  {
    $delivery = Delivery::find($id);
    
    if($delivery) {
      $agent = $delivery->agent;
      return !$agent? Response::json("Error", 400) : $agent;
    }

    return Response::json("Error", 400);
  }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$delivery = Delivery::find($id);

    if(!$delivery)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $delivery->delete();
      return Response::json("OK", 200);
    }
	}

  public function sign(Request $request, $id)
  {
    $delivery = Delivery::find($id);

    if(!$delivery || $delivery->state == 0)
    {
      return Response::json("Error", 400);
    }
    else
    {

      //Okay,  let's check if the  the current logged agent
      $userId = Authorizer::getResourceOwnerId();
      $agent = Agent::where('user_id', $userId)->first();

      if(!$agent)
        return Response::json("Error", 400);

       if($delivery->agent_id != $agent->id)
        return Response::json("Error", 403);

      $file = $request->file('signature');
      
      if($file !== null)
      {
        if($file->isValid())
        {
          $file->move(public_path()."/uploads", $id."_signature.png");
          return Response::json("OK", 200);
        }        
      }
    }

    return Response::json("Error", 400);
  }


  public static function validateDate($date, $format = 'Y-m-d H:i:s')
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }

  public static function dateDifference($dt1, $dt2)
  {
    return abs(strtotime($dt1) - strtotime($dt2));
  }

}




