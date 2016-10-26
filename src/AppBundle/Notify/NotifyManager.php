<?php

namespace AppBundle\Notify;


use Symfony\Component\Templating\EngineInterface;
use AppBundle\Notify;

/**
 *
 * @author astk
 *        
 */
class NotifyManager {
	protected $mailer;
	
	protected $templating;
	
	protected $p=1;
	
	public function __construct(\Swift_Mailer $mailer,EngineInterface $templating, $p) {
		$this->mailer = $mailer;
		$this->templating = $templating;
		$this->p=$p;
	}
	
	public function send($m) {
		switch ($this->p){
			case 1:
				$message = \Swift_Message::newInstance ();
				break;
			case 2:
				$message = PHPNotify::newInstance ();
				break;
		}
		$message
		->setSubject ($m['s'] )
		->setFrom ( $m['from'] )
		->setTo ( $m['to'] )
		->setBody ( $this->templating->render ( 'email/'.$m['bn'].'.html.twig', $m['bo']), 'text/html' );
		switch ($this->p) {
			case 1 :
				return $this->mailer->send ( $message );
				break;
			case 2 :
				return $message->send();
				break;
		}
		return false;
	}
	
	public function setP($p) {
		$this->p = $p;
		return $this;
	}
	
	
}