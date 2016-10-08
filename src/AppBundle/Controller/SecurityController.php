<?php
// src/AppBundle/Controller/SecurityController.php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserActivate;
use AppBundle\Form\UserActivateType;
use AppBundle\Form\UserRegType;
use AppBundle\Utils\Ses;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class SecurityController extends QController {
	
	/**
	 * @Route("login", name="login")
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request) {
		$session = $request->getSession ();
		
		 $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();
	
	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();
		
		$activate = $session->get ( "me_acktivate_ok" );
		
		return $this->render ( 'security/login.html.twig', array (
				'last_username' => $lastUsername,
				'error' => $error,
				'activate' => $activate,
		) );
	}
	
	
	/**
	 * @Route("reg", name="security_reg")
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function regAction(Request $request) {
		//anonymous
		if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
			
			$user = new User();
			$form = $this->createForm ( UserRegType::class, $user );
			
			if ($request->isMethod ( 'POST' )) {
				$form->handleRequest ( $request );
				if ($form->isValid()) {
					// by registration use is not active
					$user->setStatus(0);
					//$user->setUsername(Ses::before('@', $user->getEmail()));
					//$this->em()->persist($user);
					$user->setAvatar('avatar.jpg');
					//$this->em()->persist($ui);
					//create role
					//$ur = $this->em()->getRepository('AppBundle:UserRole')->findOneBy(array('role'=>'ROLE_USER'));//new UserRole();
					//$user->addUserRole($ur);
					//copy avatar.jpg to user folder
					if (!file_exists(Ses::getUpDirTmp($user->getUsername()).'/')) {
						mkdir(Ses::getUpDirTmp($user->getUsername()).'/', 0777, true);
					}
					copy(Ses::getUpDirImg().'/avatar.jpg', Ses::getUpDirTmp($user->getUsername()).'/avatar.jpg');
					$this->em()->persist($user);
					$this->em()->flush();
					//$session->set ( "me_reg_user",$user->getUsername() );
					//if global mail send is on
					if ($this->p("notify_off")=="1"){
						return $this->redirect($this->generateUrl('security_activate',array('username' => $user->getUsername(),'token'=>$user->getUnid())));
					}else{
						$nm = $this->get('app.notify.manager');
						if (!$nm->send(array(
								'to'=>$user->getEmail(),
								'from'=>$this->p('mail_reg_from_adr'),
								's'=>'Aktivierungscode',
								'bn'=>'activate',
								'bo'=>array('username' => $user->getUsername(),'token'=>$user->getUnid())
						))){
							//TODO Show error-page: Mail is not send, try again
						}
					}
					//return $this->redirect($this->generateUrl('security_activate', array('user' => $user)));
					return $this->redirect($this->generateUrl('security_activate',array('username' => $user->getUsername(),'token'=>$user->getUnid())));
				}else{
					//@TODO Error hanlder write to log and show to user
					//$session->set ( "me_reg_valid",$form->getErrors() );
				}
			}
			return $this->render ( 'security/reg.html.twig', array (
					'form' => $form->createView(),
			) );
		}
		return $this->render ( 'security/logoff.html.twig', array (
		) );
	}
	
	
	
	/**
	 * @Route("activate", name="security_activate")
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function activateAction(Request $request) {
		if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return $this->redirect($this->generateUrl('home'));
		}else{
			$em = $this->getDoctrine ()->getManager ();
			//$user = $this->get('security.token_storage')->getToken()->getUser();
			$ua = new UserActivate();
			//$ua->username=$session->get ( "me_acktivate_user");
			if ($request->query->get('username')!==""){
				$ua->username=$request->query->get('username');
			}
			if ($request->query->get('token')!==""){
				$ua->token=$request->query->get('token');
			}
			$form = $this->createForm(UserActivateType::class, $ua, array('em' => $em)); 
		
			if ($request->isMethod('POST')) {
				$form->handleRequest ( $request );
				if ($form->isValid()) {
					$user = $this->em()->getRepository('AppBundle:User')->getUserByActivation($ua->username,$ua->token);
					if ($user != null){
						// everything is OK
						$user->setStatus(1);
						$this->em()->persist($user);
						$this->em()->flush();
						//$session->set ( "me_acktivate_valid","" );
						return $this->redirect($this->generateUrl('security_wellcome', array ('_' => 'a' )));
					}else{
						//$session->set ( "me_acktivate_valid",$this->p('valid_activate') );
						return $this->redirect($this->generateUrl('security_activate'));
						//return $this->redirect($this->generateUrl('security_activate',array('login' => $ua->username,'token'=>$ua->token,'.'=>'')));
					}
				} else{
					//@TODO Error hanlder write to log and show to user
					//$session->set ( "me_acktivate_valid",$form->getErrors() );
				}
			}
			return $this->render ('security/activate.html.twig', array (
					'form' => $form->createView(),
					'ua' => $ua
			) );
		}
	}
	
	/**
	 * @Route("confirm", name="security_confirm")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function confirmAction(Request $request) {
	if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return $this->redirect($this->generateUrl('home'));
		}else{
			$em = $this->getDoctrine ()->getManager ();
			//$user = $this->get('security.token_storage')->getToken()->getUser();
			$ua = new UserActivate();
			
			if (!$request->isMethod('POST')) {
				if ($request->query->get('username')=="" || $request->query->get('token')==""){
					return $this->redirect($this->generateUrl('security_activate'));
				}
				if ($request->query->get('username')!==""){
					$ua->username=$request->query->get('username');
				}
				if ($request->query->get('token')!==""){
					$ua->token=$request->query->get('token');
				}
				$user = $this->em()->getRepository('AppBundle:User')->getUserByActivation($ua->username,$ua->token);
				if ($user != null){
					// everything is OK
					$user->setStatus(1);
					$this->em()->persist($user);
					$this->em()->flush();
					
					if ($this->p("notify_off")=="1"){
						//TODO - Log it for admins
					}else{
						$nm = $this->get('app.notify.manager');
						if (!$nm->send(array(
								'to'=>$user->getEmail(),
								'from'=>$this->p('mail_reg_from_adr'),
								's'=>'Willkommen',
								'bn'=>'wellcome',
								'bo'=>array('user' => $user)
						))){
							//TODO Show error-page: Mail is not send, try again
						}
					}
					//$session->set ( "me_acktivate_valid","" );
					return $this->redirect($this->generateUrl('security_wellcome', array ('_' => 'a' )));
				}else{
					//$session->set ( "me_acktivate_valid",$this->p('valid_activate') );
					return $this->redirect($this->generateUrl('security_activate'));
				}
			}
			
			$form = $this->createForm(UserActivateType::class, $ua, array('em' => $em));
		
			if ($request->isMethod('POST')) {
				$form->handleRequest ( $request );
				if ($form->isValid()) {
					$user = $this->em()->getRepository('AppBundle:User')->getUserByActivation($ua->username,$ua->token);
					if ($user != null){
						// everything is OK
						$user->setStatus(1);
						$this->em()->persist($user);
						$this->em()->flush();
						//$session->set ( "me_acktivate_valid","" );
						return $this->redirect($this->generateUrl('security_wellcome', array ('_' => 'a' )));
					}else{
						//$session->set ( "me_acktivate_valid",$this->p('valid_activate') );
						return $this->redirect($this->generateUrl('security_activate'));
					}
				} else{
					//@TODO Error hanlder write to log and show to user
					//$session->set ( "me_acktivate_valid",$form->getErrors() );
				}
			}
			return $this->render ('security/confirm.html.twig', array (
					'form' => $form->createView(),
			) );
		}
	}
	
	
	/**
	 * @Route("resend_aj", name="security_resend_aj")
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function resendAction(Request $request) {
		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}
		
		if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return new Response('Anonymous only are allowed to resend',420);;
		}else{
			//further
		}
		
		$u = $request->query->get('u');
		if($u==null || $u==''){
			return new Response('Username/Email is empty <'.$u.'>, skip it',423);;
		}
		$user = $this->em()->getRepository('AppBundle:User')->getUserByUE($u);
		if ($user !== null){
			// everything is OK
			if ($user->getStatus()==1){
				return new Response('User is already activated <'.$u.'>, nothing todo',421);;
			}
			if ($this->p("notify_off")=="1"){
				return new Response('It is not possible to send mails at the moment. Try it later',422);
			}else{
				$nm = $this->get('app.notify.manager');
				if (!$nm->send(array(
						'to'=>$user->getEmail(),
						'from'=>$this->p('mail_reg_from_adr'),
						's'=>'Aktivierungscode',
						'bn'=>'activate',
						'bo'=>array('username' => $user->getUsername(),'token'=>$user->getUnid())
				))){
					return new Response('System Error. Contact Admins.',423);
				}else {
					return new Response('OK',200);
				}
			}
		}else{
			return new Response('User is not found: '.$u,421);;
		}
	}
	
	
	/**
	 * @Route("w", name="security_wellcome") 
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function wellcomeAction(Request $request) {
		if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return $this->redirect($this->generateUrl('home'));
		}else{
// 			if ($this->p("notify_off")=="1"){
// 				//TODO - Log it for admins
// 			}else{
// 				$nm = $this->get('app.notify.manager');
// 				if (!$nm->send(array(
// 						'to'=>$this->u(),
// 						'from'=>$this->p('mail_reg_from_adr'),
// 						's'=>'Willkommen',
// 						'bn'=>'wellcome',
// 						'bo'=>array('username' => '???')
// 				))){
// 					//TODO Show error-page: Mail is not send, try again
// 				}
// 			}
			return $this->render ('security/wellcome.html.twig', array (
			) );
		}
	}
	
}

