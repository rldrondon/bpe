<?php namespace BikePonyExpress;

use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model {

	protected $guarded = [
    "id"
  ];

  public function question()
  {
    return $this->belongsTo("BikePonyExpress\Question");
  }

}
