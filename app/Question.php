<?php namespace BikePonyExpress;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {

	protected $guarded = [
    "id"
  ];

  public function questionResponses()
  {
    return $this->hasMany("BikePonyExpress\QuestionResponse");
  }

}
