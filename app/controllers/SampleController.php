<?php

class SampleController extends \BaseController {

	public function getIndex(){
		$sample = new Sample;
		$sample->name = "Jethro";
		$sample->save();
	}
}