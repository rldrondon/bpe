<?php namespace BikePonyExpress;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model {

	protected $guarded = [
    "id"
  ];

  public function deliveries() 
  {
    return $this->hasMany("BikePonyExpress\Delivery");
  }

}
