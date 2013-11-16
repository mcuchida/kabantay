<?php

namespace Libs;

use Illuminate\Support\Facades\Facade;

class MongoValidation extends Facade {

    protected static function getFacadeAccessor() { return 'mvalidation'; }

}