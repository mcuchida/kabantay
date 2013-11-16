<?php
  include_once base_path().'/app/eden/eden.php'; 

  class Foursquare {

    const FOURSQUARE_CLIENT_ID = 'V2UF5V53WN3XJJEDM2LCSIFXG13X5DPCEO5ETYC1XYJG1MD2';
    const FOURSQUARE_CLIENT_SECRET = '3GAKO0GT3GAC02EIFMERIA1SYSPSTXSJHPZOLU00GG4B32KJ';

    public static function auth($redirect = ""){
      return eden('foursquare')->auth(self::FOURSQUARE_CLIENT_ID, self::FOURSQUARE_CLIENT_SECRET, URL::to('/login/foursquare/'.$redirect));
    }

    public static function getEvents(){
      return eden('foursquare')->events(Session::get('foursquare_token'))->getList();
    }

  }