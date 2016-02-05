<?php namespace BikePonyExpress\Http\Controllers;

use BikePonyExpress\Http\Requests;
use BikePonyExpress\Http\Controllers\Controller;
use BikePonyExpress\User;
use BikePonyExpress\Agent;
use BikePonyExpress\Delivery;

use Illuminate\Http\Request;
use Illuminate\Auth;
use Validator;
use Input;
use Response;
use DateTime;

use LucaDegasperi\OAuth2Server\Facades\AuthorizerFacade as Authorizer;


class AgentController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$agents = Agent::all();
		return !$agents? Response::json("Error", 400) : $agents;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
    $rules = array(
        'name'    => 'required',
        'surname' => 'required',
        'phone'   => 'required',
        'user_id'   => 'required'
    );

    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails()) 
    {
      return Response::json("Error", 400);
    } 
    else 
    {
      $agent = new Agent;
      $agent->name    = Input::get('name');
      $agent->surname = Input::get('surname');
      $agent->phone   = Input::get('phone');
      $agent->user_id = Input::get('user_id');
      $agent->save();

      return $agent;
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
		$agent = Agent::find($id);
		return !$agent? Response::json("Error", 400) : $agent;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
    $agent = Agent::find($id);

    if(!$agent)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $rules = array(
        'status'   => 'integer'
      );

      $validator = Validator::make(Input::all(), $rules);

      if ($validator->fails()) 
      {
        return Response::json("Error", 400);
      } 
      else 
      {

        if(Input::get('name') !== null)
          $agent->name  = Input::get('name');

        if(Input::get('surname') !== null)
          $agent->surname  = Input::get('surname');
        
        if(Input::get('phone') !== null)
          $agent->phone  = Input::get('phone');

        if(Input::get('status') !== null)
          $agent->status  = Input::get('status');

        if(Input::get('last_position') !== null) {
          $agent->last_position  = Input::get('last_position');
        }
        if(Input::get('user_id') !== null)
          $agent->user_id  = Input::get('user_id');

        $agent->last_update  = (new DateTime);
        
        $agent->save();
        
        $agent->last_update = $agent->last_update->format('Y-m-d H:i:s');
        return $agent;
      }
    }
	}

  public function updateAndGetDeliveries($id)
  {
    $agent = Agent::find($id);

    if(!$agent)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $rules = array(
        'status'   => 'integer'
      );

      $validator = Validator::make(Input::all(), $rules);

      if ($validator->fails()) 
      {
        return Response::json("Error", 400);
      } 
      else 
      {

        if(Input::get('name') !== null)
          $agent->name  = Input::get('name');

        if(Input::get('surname') !== null)
          $agent->surname  = Input::get('surname');
        
        if(Input::get('phone') !== null)
          $agent->phone  = Input::get('phone');

        if(Input::get('status') !== null)
          $agent->status  = Input::get('status');

        if(Input::get('last_position') !== null) {
          $agent->last_position  = Input::get('last_position');
        }
        if(Input::get('user_id') !== null)
          $agent->user_id  = Input::get('user_id');

        $agent->last_update  = (new DateTime);
        
        $agent->save();
        
        $agent->last_update = $agent->last_update->format('Y-m-d H:i:s');

        if($agent) {
          $deliveries = $agent->deliveries()->where('state', '<', 2)->orderBy('updated_at', 'desc')->get();
          return !$deliveries? Response::json("Error", 400) : $deliveries;
        }

        return Response::json("Error", 400);
      }
    }
  }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$agent = Agent::find($id);

    if(!$agent)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $agent->delete();
      return Response::json("OK", 200);
    }
	}

  // List all the (assigned, not completed) deliveries of a certain agent.

  public function deliveries($id)
  {
    $agent = Agent::find($id);
    
    if($agent) {
      $deliveries = $agent->deliveries()->where('state', '<', 2)->orderBy('updated_at', 'desc')->get();
      return !$deliveries? Response::json("Error", 400) : $deliveries;
    }

    return Response::json("Error", 400);
  }

  public function active()
  {
    $agents = Agent::where('status', 1)->get();
    return !$agents? Response::json("Error", 400) : $agents;
  }

  public function current()
  {
    $userId = Authorizer::getResourceOwnerId();
    $agent = Agent::where('user_id', $userId)->first();

    if(!$agent)
      return Response::json("Error", 400);

    $agent->status = 1;
    $agent->save();

    return $agent;
  }

  public function passwordChange()
  {
    $oldPassword = Input::get('old_password');
    $newPassword = Input::get('new_password');

    if(!empty($oldPassword) && !empty($newPassword))
    {
      $userId = Authorizer::getResourceOwnerId();
      $user = User::find($userId);
      if(!empty($user))
      {
        if(\Auth::validate(['email' => $user->email, 'password' => $oldPassword]))
        {
          $user->password = \Hash::make($newPassword);
          $user->save();
          return Response::json("OK", 200);
        }
      }
    }

    return Response::json("Error", 400);
  }

}
