<?php
// src/AppBundle/Controller/SecurityController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends Controller {
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request) {
		$session = $request->getSession ();
		
		if ($request->attributes->has ( Security::AUTHENTICATION_ERROR )) {
			$error = $request->attributes->get ( Security::AUTHENTICATION_ERROR );
		} elseif (null !== $session && $session->has ( Security::AUTHENTICATION_ERROR )) {
			$error = $session->get ( Security::AUTHENTICATION_ERROR );
			$session->remove ( Security::AUTHENTICATION_ERROR );
		} else {
			$error = '';
		}
		
		$activate = $session->get ( "me_acktivate_ok" );
		
		$lastUsername = (null === $session) ? '' : $session->get ( Security::LAST_USERNAME );
		
		return $this->render ( 'AppBundle:Security:login.html.twig', array (
				'last_username' => $lastUsername,
				'error' => $error,
				'activate' => $activate,
		) );
	}
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginaAction(Request $request) {
		$session = $request->getSession ();
	
		if ($request->attributes->has ( Security::AUTHENTICATION_ERROR )) {
			$error = $request->attributes->get ( Security::AUTHENTICATION_ERROR );
		} elseif (null !== $session && $session->has ( Security::AUTHENTICATION_ERROR )) {
			$error = $session->get ( Security::AUTHENTICATION_ERROR );
			$session->remove ( Security::AUTHENTICATION_ERROR );
		} else {
			$error = '';
		}
	
		$activate = $session->get ( "me_acktivate_ok" );
	
		$lastUsername = (null === $session) ? '' : $session->get ( Security::LAST_USERNAME );
	
		return $this->render ( 'AppBundle:Security:login.html.twig', array (
				'last_username' => $lastUsername,
				'error' => $error,
				'activate' => $activate,
		) );
	}
	
	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function loginCheckAction(Request $request) {
		return $this->redirect ( $this->generateUrl ( 'home' ) );
	}
	
	
}

