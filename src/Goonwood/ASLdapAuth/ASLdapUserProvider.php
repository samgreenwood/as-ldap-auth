<?php namespace Goonwood\ASLdapAuth;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\UserInterface;

class ASLDAPUserProvider implements UserProviderInterface {

	protected $conn;

	/**
	 * Create a new instance of the ASLdapUserProvider
	 * When this is created we want to establish an update the the Air-Stream LDAP Server
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->conn = ldap_connect("ldaps://mail.air-stream.org", 636);
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveById($identifier) {}

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public function retrieveByToken($identifier, $token) {}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserInterface $user, $token) {}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Auth\UserInterface|null
	 */
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

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		return $this->bind($credentials);
	}


	/**
	 * Bind to the LDAP server using the given credentials
	 * 
	 * @param  array  $credentials
	 * @return bool
	 */
	private function bind(array $credentials)
	{
		$username = "uid=".$credentials['username'].",ou=people,dc=air-stream,dc=org";
		$password = $credentials['password'];
		
		return @ldap_bind($this->conn, $username, $password);	
	}
	
}
