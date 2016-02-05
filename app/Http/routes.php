<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');
Route::get('request', 'WelcomeController@request');
Route::post('request', 'WelcomeController@saveRequest');
Route::get('track/{tracking_code}', 'WelcomeController@track');

Route::get('private', 'HomeController@index'); // Delete this in production!


Route::group(['before' => 'oauth'], function() {

  Route::get('agent', 'AgentController@current');
  Route::post('agent/password', 'AgentController@passwordChange');

  Route::put('agents/{id}/update+deliveries', 'AgentController@updateAndGetDeliveries');
  Route::get('agents/active', 'AgentController@active');
  Route::get('agents/{id}/deliveries', 'AgentController@deliveries');
  Route::resource('agents','AgentController', ['except' => ['create', 'edit']]);

  Route::post('deliveries/{id}/sign', 'DeliveryController@sign');
  Route::get('deliveries/{id}/agent', 'DeliveryController@agent');
  Route::put('deliveries', 'DeliveryController@updateDeliveries');
  Route::resource('deliveries', 'DeliveryController', ['except' => ['create', 'edit']]);

  Route::get('questions/{id}/responses', 'QuestionController@responses');
  Route::resource('questions', 'QuestionController', ['except' => ['create', 'edit']]);

  Route::put('responses', 'QuestionResponseController@createResponses');
  Route::get('responses/{id}/question', 'QuestionResponseController@question');
  Route::resource('responses', 'QuestionResponseController', ['except' => ['create', 'edit']]);

});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});
