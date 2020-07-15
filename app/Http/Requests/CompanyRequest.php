<?php

namespace App\Http\Requests;

class CompanyRequest extends EntityRequest
{
	protected $entityType = ENTITY_COMPANY;

	public function authorize()
	{
		return true;
	}
}
