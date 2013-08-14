<?php namespace Goonwood\ASLdapAuth;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\UserInterface;

class ASLDAPUserProvider implements UserProviderInterface {

	protected $conn;
	protected $dn;
	protected $filter;

	public function __construct()
	{
		$this->conn = ldap_connect("ldaps://mail.air-stream.org", 636);
	}

	
	public function retrieveByID($identifier)
	{
		return $identifier;
	}	
	
	public function retrieveByCredentials(array $credentials)
	{
		if($this->bind($credentials))
		{
			$filter = "(|(uid=".$credentials['username'].")(email=".$credentials['username']."))";
			$dn = "dc=air-stream,dc=org";
			$attribs = array("uid", "givenname", "sn", "uidnumber", "mail");
			$sr = ldap_search($this->conn, $dn, $filter, $attribs);

			$info = ldap_get_entries($this->conn, $sr);

			$user = array();
			$user['id'] = $info[0]["uidnumber"][0];
			$user['username'] = $info[0]["uid"][0];
			$user['firstname'] = $info[0]["givenname"][0];
			$user['surname'] = $info[0]["sn"][0];
			$user['email'] = $info[0]["mail"][0];
			
			return new GenericUser($user);
		}
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{
		return $this->bind($credentials);
	}

	private function bind(array $credentials)
	{
		$username = "uid=".$credentials['username'].",ou=people,dc=air-stream,dc=org";
		$password = $credentials['password'];
		
		return @ldap_bind($this->conn, $username, $password);	
	}
	
}
