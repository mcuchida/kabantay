<?php

include_once base_path().'/app/eden/eden.php';

class Twitter {

	//prod
	const TWITTER_CLIENT_ID      = 'A4GRiWdUDgiGPckQSykzA';
	const TWITTER_CLIENT_SECRET  = 'nSyjpFDTr3eEmd8tFeBM1lwVrTNPLaHrcx0DicTEEuw';

	const TWITTER_ACCESS_TOKEN   = '1678747572-lbkg1jxpe5HypoTRSnRMeiVIe0RBMxhdjLJ0GJ6';
	const TWITTER_ACCESS_SECRET  = '1qVP0pHXV9HDgKhHDIy2HHqqnHFBm3sI5AR2ZDwZxlM';

	public static function auth()
	{
		$auth = eden('twitter')->auth(self::TWITTER_CLIENT_ID, self::TWITTER_CLIENT_SECRET);
		return $auth;
	}

	public static function getUserInfo(){
		$users = eden('twitter')->users(self::TWITTER_CLIENT_ID, self::TWITTER_CLIENT_SECRET, SESSION::get('twitter_access_token'), SESSION::get('twitter_access_secret'));
		$screen_name = $users->getAccountSettings();

		$users =  $users->getDetail($screen_name['screen_name']);
		return $users;
	}

	public static function getInstance()
	{
		return eden('twitter');
	}
	
	public static function search($hash)
	{
		$twitter = self::getInstance();
		$twitter_access_token  = (Session::has('twitter_access_token')) ? Session::get('twitter_access_token') : self::TWITTER_ACCESS_TOKEN;
		$twitter_access_secret = (Session::has('twitter_access_secret')) ? Session::get('twitter_access_secret') : self::TWITTER_ACCESS_SECRET;

		$i = $twitter->search(self::TWITTER_CLIENT_ID, self::TWITTER_CLIENT_SECRET, 
					$twitter_access_token, $twitter_access_secret);

		$result = $i->search($hash);
		return $result;
	}
	public static function tweetStatus($status){
		//dd(Session::all());
		$twitter = self::getInstance();
		$tweets = $twitter->tweets(self::TWITTER_CLIENT_ID, self::TWITTER_CLIENT_SECRET, Session::get('twitter_access_token'), Session::get('twitter_access_secret'));
	
	  return $tweets->tweet($status);
	}
}