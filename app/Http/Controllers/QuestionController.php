<?php namespace BikePonyExpress\Http\Controllers;

use BikePonyExpress\Http\Requests;
use BikePonyExpress\Http\Controllers\Controller;
use BikePonyExpress\Question;

use Illuminate\Http\Request;
use Validator;
use Input;
use Response;

class QuestionController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$questions = Question::all();
		return !$questions? Response::json("Error", 400) : $questions;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rules = array(
        'question_text' => 'required'
    );

    $validator = Validator::make(Input::all(), $rules);

    if ($validator->fails()) 
    {
      return Response::json("Error", 400);
    } 
    else 
    {
    	$question = new Question;
    	$question->question_text = Input::get('question_text');
    	$question->save();

    	return $question;
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
		$question = Question::find(1);
		return !$question? Response::json("Error", 400) : $question;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$question = Question::find($id);

		if(!$question)
    {
      return Response::json("Error", 400);
    }
    else
    {
      if(Input::get('question_text') !== null)
        $question->question_text  = Input::get('question_text');

      $question->save();

      return $question;
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
		$question = Question::find($id);

    if(!$question)
    {
      return Response::json("Error", 400);
    }
    else
    {
      $question->delete();
      return Response::json("OK", 200);
    }
	}

	// List all the responses to a certain question.

	public function responses($id)
	{
		$question = Question::find($id);
		
		if($question) {
			$responses = $question->questionResponses;
			return !$responses? Response::json("Error", 400) : $responses;
		}

		return Response::json("Error", 400);
	}

}
