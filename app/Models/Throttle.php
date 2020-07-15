<?php

namespace App\Models;

class Throttle extends EntityModel
{

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
	
}
