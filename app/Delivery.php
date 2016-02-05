<?php namespace BikePonyExpress;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model {

  protected $guarded = [
    "id",
    "agent_id"
  ];

  public function agent()
  {
    return $this->belongsTo("BikePonyExpress\Agent");
  }

}
