<?php
include_once base_path().'/app/libs/Facebook.php';
class LoginController extends \BaseController {

	public function getFacebook($redirect = "")
	{

		$auth = Facebook::auth($redirect);
		if(!Input::get('code') && !Session::get('fb_token'))
		{
			return Redirect::to($auth->getLoginUrl(Facebook::FACEBOOK_PERMISSIONS));
		}
		if(Input::get('code'))
		{
			$access = $auth->getAccess(Input::get('code'));
			Session::put('fb_token',$access['access_token']);
			$user_profile 	= Facebook::graph()->getUser();
			$user 			= new User;
			$user->provider = "facebook";
			$user->fb_token = $access['access_token'];
			$user->profile  = $user_profile;
			$user->username  = $user_profile['username'];

			if(isset($user_profile['email'])) 
			{
				$user->email 	= $user_profile["email"];
			}
			User::update(array('username'=>$user_profile['username']),$user->toArray(),array("upsert"=>true)); 
			Session::put('user', $user);
			if($redirect == "")
			{
					return Redirect::to(Session::get('redirect_uri', '/'));
			}
			else
			{
					$url = base64_decode($redirect);
					return Redirect::to($url);
			}
		}

		return Redirect::to(Session::get('redirect_uri', '/'));
	}
}