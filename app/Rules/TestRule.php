<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class TestRule implements Rule
{

/**
 * 	Finally, we use this class in controller's store() method we have this code:
public function store(Request $request)
{
$this->validate($request, ['year' => new OlympicYear]);
}

 * 
 */

public function passes($attribute, $value)
{
	return $value >= 1896 && $value <= date('Y') && $value % 4 == 0;
}
	// Next, we can update error message to be this:
public function message()
{
	return ':attribute should be a year of Olympic Games';
}

}