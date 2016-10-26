<?php

namespace AppBundle\Notify;

use AppBundle\Utils\PHPMailer;

/**
 *
 * @author astk
 *        
 */
class PHPNotify extends PHPMailer {
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see \AppBundle\Utils\PHPMailer::__construct()
	 */
	public function __construct( $exceptions = false) {
		// TODO Auto-generated method stub
		parent::__construct ((boolean) $exceptions );
	}
	
	public static function newInstance( $exceptions = false)
	{
		return new self( $exceptions);
	}
	
	
	/**
	 *
	 * @param string 
	 *
	 * @return PHPNotify
	 */
	public function setSubject($subject)
	{
		$this->Subject=$subject;
	
		return $this;
	}
	
	/**
	 *
	 * @param string
	 *
	 * @return PHPNotify
	 */
	public function setBody($body)
	{
		$this->isHTML(true);
		$this->Body=$body;
	
		return $this;
	}
	
	/**
	 *
	 * @param string
	 *
	 * @return PHPNotify
	 */
	public function setFrom($address, $name = '', $auto = true)
	{
		parent::setFrom($address, $name, $auto);
	
		return $this;
	}
	
	/**
	 *
	 * @param string
	 *
	 * @return PHPNotify
	 */
	public function setTo($address)
	{
		$this->addAddress($address);
	
		return $this;
	}
}