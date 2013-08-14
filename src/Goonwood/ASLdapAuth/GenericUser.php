<?php namespace Goonwood\ASLdapAuth;

use Illuminate\Auth\GenericUser as IlluminateGenericUser;

class GenericUser extends IlluminateGenericUser 
{	
	public function toArray()
	{
		return $this->attributes;
	}

}
