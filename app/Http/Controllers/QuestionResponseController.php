<?php namespace BikePonyExpress\Http\Controllers;

use BikePonyExpress\Http\Requests;
use BikePonyExpress\Http\Controllers\Controller;
use BikePonyExpress\QuestionResponse;
use BikePonyExpress\Question;

use Illuminate\Http\Request;
use Validator;
use Input;
use Response;

class QuestionResponseController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$responses = QuestionResponse::all();
		return !$responses? Response::json("Error", 400) : $responses;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
        'question_id'    => 'required|integer',
        'vote' => 'required|integer|min:0|max:5'    
    );

    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails()) 
    {
      return Response::json("Error", 400);
    } 
    else 
    {
    	$question = Question::find((int)Input::get('question_id'));

    	if(!$question)
    	{
    		return Response::json("Error", 400);
    	}
    	else
    	{
	    	$response = new QuestionResponse;
	    	$response->question_id = Input::get('question_id');
	    	$response->vote = Input::get('vote');
	    	$response->save();

	    	return $response;
    	}
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
		$response = QuestionResponse::find($id);
		return !$response? Response::json("Error", 400) : $response;
	}



	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$response = QuestionResponse::find($id);

    if(!$response)
    {
      return Response::json("Error", 400);
    }
    else
    {
    	$rules = array(
        'vote' => 'required|integer|min:0|max:5'    
	    );

	    $validator = Validator::make(Input::all(), $rules);

	    if ($validator->fails()) 
	    {
	      return Response::json("Error", 400);
	    } 
	    else 
	    {

	    	if(Input::get('vote') !== null)
	        $response->vote  = Input::get('vote');

	      $response->save();

	      return $response;
	    }
    }
	}

	public function question($id)
	{
		$response = QuestionResponse::find($id);
		
		if($response) {
			$question = $response->question;
			return !$question? Response::json("Error", 400) : $question;
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
		$response = QuestionResponse::find($id);

    if(!$response)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $response->delete();
      return Response::json("OK", 200);
    }
	}


  // Create multiple responses at once

  public function createResponses()
  {
  
    $responsesJSON = Input::get('responses');
    $createdResponses = [];

    if(!empty($responsesJSON)) {

      $responses = json_decode($responsesJSON, true);

      if($responses !== null && count($responses) > 0)
      {
        foreach ($responses as $id => $updates)
        {
          $question = Question::find(intval($id));

          if($question)
          {
            $vote = intval($updates["vote"]);
            
            if($vote >= 0 && $vote <= 5)
            {
              $response = new QuestionResponse;
              $response->question_id = intval($id);
              $response->vote = $vote;
              $response->save();
              array_push($createdResponses, $response);
            }
            else
            {
              return Response::json("Error", 400);
            }
          }
          else
          {
            return Response::json("Error", 400);
          }
        }

        return empty($createdResponses) ? Response::json("Error", 400) : $createdResponses;
      }
    }

    return Response::json("Error", 400);
  }

}
