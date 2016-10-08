<?php
// src/AppBundle/Controller/PageController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class TestController extends QController {
	
	/**
	 * @Route("test/email/php", name="test_email_php")
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function emailPHPAction() {
		
// 		$protocol = "ssl";	//tcp ssl tls
// 		$host = "smtp.1und1.de";
// 		$port = 465; 		//25 465 587
// 		$errno = 1;
// 		$errstr = "";
// 		$timeout = 30;
// 		$socket_context = stream_context_create(array());
		
		//$smtp_conn =
		//stream_socket_client($protocol."://".$host.":".$port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $socket_context);
		// Verify we connected properly
// 		if(empty($smtp_conn)) {
// 			$error = array('error' => 'Failed to connect to server','errno' => $errno,'errstr' => $errstr);
// 		}
// 		else{
// 			$error = $smtp_conn;
// 		}
// 		var_dump($error);
		
		
		
		//$this->get ( 'mailer' )->send ( $message );
		
		$to = "jankieone@gmail.com";
		$this->get('app.notify.manager')->setP(2);//php
		if (!$this->get('app.notify.manager')->send(array(
				'to'=>$to,
				'from'=>'sascha.stayan@web.de',
				's'=>"Willkomen zu QUIZ - TEST-PHP",
				'bn'=>"activate",
				'bo'=>array ('username' => 'jo','token' => '1234567890')
		))){
			$p = 'PHP-Mailer Error: ';// . $m->ErrorInfo;
		} else {
			$p = 'Message has been sent';
		}
		
// 		$headers = "From: " . strip_tags($this->getParameter('mailer_user')) . "\r\n";
// 		$headers .= "Reply-To: ". strip_tags($this->getParameter('mailer_user')) . "\r\n";
// 		//$headers .= "CC: susan@example.com\r\n";
// 		$headers .= "MIME-Version: 1.0\r\n";
// 		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
// 		$subject = "FAFA TEST EMAIL SEND";
// 		$message = $this->renderView ( 'AppBundle:Email:reg.html.twig', array (
// 				'login' => 'jo',
// 				'token' => '1234567890' 
// 			));
		
// 		mail($to,$subject,$message,$headers);
		
		// return new Response('Is sent! w.sent');
		return $this->render ( 'test/email.html.twig', array (
				'p' => $p 
		) );
	}
	
	/**
	 * @Route("test/email/swift", name="test_email_swift")
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function emailSWIFTAction() {
	
		
		$to = "jankieone@gmail.com";
		$this->get('app.notify.manager')->setP(1);//swift
		if (!$this->get('app.notify.manager')->send(array(
				'to'=>$to,
				'from'=>'sascha.stayan@web.de', 
				's'=>"Willkomen zu QUIZ - SWIFT-PHP",
				'bn'=>"activate",
				'bo'=>array ('username' => 'jo','token' => '1234567890')
		))){
			$p = 'Swift-Mailer Error: ' ;//. $m->ErrorInfo;
		} else {
			$p = 'Message has been sent';
		}
	
		// return new Response('Is sent! w.sent');
		return $this->render ( 'test/email.html.twig', array (
				'p' => $p
		) );
	}
	
	public function errorAction() {
		$p = "No errors";
		//$t = 3/0;
		throw new \Exception('Ahhhhahahhhah');
		return $this->render ( 'AppBundle:Test:email.html.twig', array (
				'k' => $p
		) );
	}
}