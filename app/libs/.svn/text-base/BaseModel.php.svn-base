<?php

namespace Libs;

/**
* Base class for all models
*/
class BaseModel extends MongovelModel
{
	public function save()
	{
		try {
			self::insert(self::toArray());
			$saved = true;
		} catch (Exeption $e) {
			$saved = false;
		}
		
		return $saved;
	}

	public function all()
	{
		return self::find()->all();
	}
}