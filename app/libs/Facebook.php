<?php
	include_once base_path().'/app/eden/eden.php'; 
	class Facebook{
			/* Constants
			-------------------------------*/
			const FACEBOOK_KEY				= '541577505926051';
			const FACEBOOK_SECRET			= '2d828bbf9d47ddb03191817915edfdc9';
			const FACEBOOK_PERMISSIONS 		= 'email, user_likes, user_birthday, publish_stream, publish_actions, read_stream';

		
			const GOOGLE_PLUS_ID			= '693612696086.apps.googleusercontent.com';
			const GOOGLE_PLUS_SECRET		= 'FtL--eyDiIa3NXTL2vpoZj0w';
		public static function auth($redirect = ""){
			return eden('facebook')->auth(self::FACEBOOK_KEY,self::FACEBOOK_SECRET, URL::to('/login/facebook/'.$redirect));
		}

		public static function getUserImage(){
			return Facebook::graph()->getPictureUrl();
		}
		public static function graph(){
			return eden('facebook')->graph(Session::get('fb_token'));
		}
		public static function isLoggedIn(){
			return Session::get('fb_token');
		}
		public static function getLogoutUrl(){
			return Facebook::graph()->getLogoutUrl(URL::to('/'));
		}

		public static function getEden()
		{
			return eden('facebook');
		}
		public static function getLogin(){
			return URL::to('/login/facebook/'.base64_encode(URL::current()));
		}
		public static function postStatus($message){
			return Facebook::graph()->post($message)->create();
		}

		public static function createStory($url) {
			if(Session::get('fb_token')) {
				$postUrl 	= "https://graph.facebook.com/me/video.watches?access_token=".Session::get('fb_token')."&method=POST&episode=".$url;
				$ch 		= curl_init();
				curl_setopt($ch, CURLOPT_URL, $postUrl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$response  	= curl_exec($ch);

				curl_close($ch);

				return $response;
			}else{
				return null;
			}

		}
	}