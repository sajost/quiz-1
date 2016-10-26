<?php

namespace AppBundle\Form;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 *
 * @author astk
 *        
 */
class UserPassword {
	
	/**
	 * by change password, to check if actual is correct 
	 * 
     * @SecurityAssert\UserPassword(message = "Passwort ist falsch")
	 */
	public $pwd;
	
	public $password;
	
}