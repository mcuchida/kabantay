<?php

class HomeController extends \BaseController {
	protected $layout = "landing.default";
	public function getIndex(){
		$this->layout->title = "Kabantay";
		$this->layout->head = View::make("landing.head");
		$this->layout->body	= View::make("landing.body");
		$this->layout->foot  = "";
	}

}