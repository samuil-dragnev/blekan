<?php

namespace Blekan\Auth;

use Blekan\Models\User;

class Auth {
	
	public function logout($request, $response) {
		unset ( $_SESSION ['user_id'] );
	}
	
	public function getCurrentUser() {
		return isset ( $_SESSION ['user_id'] ) ? User::find ( $_SESSION ['user_id'] ) : null;
	}
	
	public function isSignedIn() {
		return isset ( $_SESSION ['user_id'] );
	}
	
	public function attemptAuthentication($email, $password) {
		$user = User::where ( 'email', $email )->first ();
		
		if (! $user) {
			return false;
		}
		
		if (password_verify ( $password, $user->password )) {
			$_SESSION ['user_id'] = $user->id;
			
			return true;
		}
		
		return false;
	}
}
