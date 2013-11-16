<?php 
	class Analytics {
		public static function incrementVideoView($show_id, $video_id){
			self::addVideoView($show_id, $video_id);
		}

		public static function instantiateShow($show_id){
			$_id = new MongoId();
			try{
				Analytic::update(
						array('show_id'=>$show_id),
						array(
							'show_id' =>  $show_id,
							'_id' 	  =>  $_id,
							'videos'  =>  array()
						),
						array('upsert' 	=> 	1)
					);
			}catch(Exception $e){

			}
		}


		public static function getViews($show_slug, $video_slug){
			$show  = Analytic::findOne(array('show_id'=>$show_slug,'videos.video_slug'=>$video_slug));
			if($show == null){
				self::instantiateVideo($show_slug, $video_slug);
				return self::getViews($show_slug, $video_slug);
			}
			$video = array();

			foreach($show->videos as $_video){
				if($_video['video_slug'] == $video_slug){
					$video = $_video;
					break;
				}
			}

			return isset($video['views']) ? $video['views'] : 0;
		}

		public static function getTotalViews($show_slug){
			$show  = Analytic::findOne(array('show_id'=>$show_slug));
			if($show == null){
				self::instantiateShow($show_slug);
				return self::getTotalViews($show_slug);
			}
			$views = 0;
			foreach($show->videos as $video){
				$views += $video['views'];
			} 
			return $views;
		}

		public static function addVideoView($show_id, $video_id){
			$show = Analytic::findOne(array('show_id'=>$show_id,'videos.video_slug'=>$video_id));
			if($show == null){
				self::instantiateVideo($show_id, $video_id);
			}
			Analytic::update(
					array('videos.video_slug'=>$video_id),
					array(
						'$inc' => array(
								'videos.$.views' => 1
							)
						)
				);
			
			Analytic::update(
				array('videos.video_slug'=>$video_id),
				array(
					'$push' => array(
							'videos.$.users' => array(
									'user'=> Session::get('user') ? Session::get('user')->profile['id'] : "non-user",
									'date'=> $startTime = new MongoDate()
									)
						)
					)
			);

		}
		public static function instantiateVideo($show_id, $video_id){
			$show = Analytic::findOne(array('show_id'=>$show_id));
			if($show == null){
				self::instantiateShow($show_id);
			}
			Analytic::update(
					array('show_id'=>$show_id),
					array(
							'$push' => array(
									'videos' => array(
										'video_slug' => $video_id,
										'users'		 => array(), 
										'views' 	=> 0
										)
								)
						)
				);
		}
		
		public static function getShow($show_id){
			return Analytic::findOne(array('show_id'=>$show_id));
		}
		
		public static function getDailyViewsOfVideo($show_slug, $video_slug){
			$date 		= date("Y-m-d");
			// $date 		= "2010-01-01";
			$startTime	= new MongoDate(strtotime($date . " 00:00:00"));
			$endTime	= new MongoDate(strtotime($date . " 24:00:00"));
			$users 		= array();

			$show 		= Analytic::findOne(
										array(
											'show_id'		   => $show_slug,
											'videos.video_slug'=> $video_slug,
											'videos.users.date'=> array('$gt'=>$startTime,'$lte'=>$endTime)
											)
									);
			
			if($show == null){
				return $users;
			}

			foreach($show->videos as $video) {
				if($video["video_slug"] == $video_slug) {
					foreach($video["users"] as $user) {
						if(!isset($user['date']) || $user['date'] < $startTime || $user['date'] > $endTime ) continue;
						$users[]  = $user;
					}
					break;
				}
			}
			return $users;
		}

		public static function getUserCountByAge($show_slug, $video_slug, $range = array(), $date = null) {
			$users = self::getUsers($show_slug ,$video_slug, $date);
			$count = 0;
			if(empty($range) || !isset($range['floor']) || !isset($range['ceiling'])) {
				return count($users);
			}else {
				$floor 	 = $range['floor'];
				$ceiling = $range['ceiling'];
				foreach($users as $user) {
					if($user == null)
						continue;
					else {
						if($user->getAge() < $floor || $user->getAge() > $ceiling) continue;
						else {
							$count++;
						}
					}
				}
			}
			return $count;
		}

		public static function getUsersBySex($show_slug, $video_slug, $sex = "male", $date = null){
			$users = self::getUsers($show_slug, $video_slug, $date);
			$count = 0;
			foreach($users as $user) {
				if($user == null) 
					continue;
				else {
					if($user->profile['gender'] == $sex) {
						$count++;
					}
				}
			}
			return $count;
		}

		
		public static function getFacebookUsers($show_slug, $video_slug, $date = null) {
			$users 		= self::getUsers($show_slug, $video_slug, $date);
			$fb_users 	= array();

			

			foreach($users as $user) {
				if($user == null) continue; 

				if($user->provider == "facebook") {
					$fb_users[] = $user;
				}
			}

			
			return $fb_users;

		}
	
		public static function getUsers($show_slug, $video_slug, $date = null, $recurring = false) {
			$users 		= array();
			$episode	= array();
			$dups 		= array();
			$startTime	= new MongoDate(strtotime($date . " 00:00:00"));
			$endTime	= new MongoDate(strtotime($date . " 24:00:00"));
			if($date) {
				$show 	= Analytic::findOne(array('show_id'=>$show_slug,'videos.video_slug'=>$video_slug,'videos.users.date'=> array('$gt'=>$startTime,'$lte'=>$endTime)));
			}else {
				$show 	= Analytic::findOne(array('show_id'=>$show_slug,'videos.video_slug'=>$video_slug));
			}


			if(!$show) {
				return array();
			}else {

				foreach($show->videos as $video) {
					if($video['video_slug'] == $video_slug) {
						$episode = $video;
						break;
					}
				}


				//flatten array first
				foreach($video['users'] as $user) {
					if(!isset($user['date']) || $user['date'] < $startTime || $user['date'] > $endTime ) continue;
					$users[] = $user["user"];
				}
				if(!$recurring) {
					$users = array_unique($users);
				}else {
					foreach(array_count_values($users) as $val=>$c) {
						if($c > 1) {
							$dups[] = $val;
						} 
					}
					$users = $dups;
				}

				$users = array_merge($users, array());
				
				for($i = 0; $i < count($users); $i++) {
					if($users[$i] == "non-user") {
						$users[$i] = null;
					}else { 
						$users[$i] = User::findOne(array('profile.id'=>$users[$i]));
					}
				}

				return $users;
			}
		}
	}