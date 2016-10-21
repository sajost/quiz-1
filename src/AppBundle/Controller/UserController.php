<?php
// src/AppBundle/Controller/UserController.php
namespace AppBundle\Controller;


use AppBundle\Utils\Ses;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UserMyType;
use AppBundle\Form\UserUsername;
use AppBundle\Form\UserUsernameType;
use AppBundle\Form\UserEmailType;
use AppBundle\Form\UserPasswordType;
use AppBundle\Form\UserPassword;

class UserController extends QController {
	
	
	/**
	 * @Route("my/user", name="my_user")
	 */
	public function usereAction(Request $request,$eid=null) {
		// *************RIGHTS************************************
		if (! $this->get ( 'security.authorization_checker' )->isGranted ( 'IS_AUTHENTICATED_FULLY' )) {
			return $this->redirect ( $this->generateUrl ( 'login' ) );
		}
		//*************RIGHTS************************************
	
	
		$form = $this->createForm ( UserMyType::class, $this->u() ); 
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				
				$p = $request->get ( 'user' );
				//var_dump($p);
				// crop & resize the avatar image
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if (!Ses::imgCrop (
							Ses::getUpDirTmp ( $this->u()->getUsername() ) . "/" . $p ['avatar'],
							$p ['avatar_x'],
							$p ['avatar_y'],
							$p ['avatar_w'],
							$p ['avatar_h'] )) {
								$this->get('session')->set ( "my_user_ok", 'The Image-Crop-Function return false, check logs' );
							}
						
					$img_constraint = array (
							'constraint' => array (
									'width' => 100,
									'height' => 100
							)
					);
					$avatar=Ses::after('tmp_', $p ['avatar']);
					//dump($avatar);
					Ses::imgResize ( Ses::getUpDirTmp ($this->u()->getUsername()) . "/" . $p ['avatar'], Ses::getUpDirTmp ($this->u()->getUsername()) . "/" . $avatar, $img_constraint );
					$this->u()->setAvatar($avatar); 
				}
				
				$this->em()->persist ( $this->u() );
				$this->em()->flush ();
				$this->get('session')->set('my_user_ok', 'User is OK');
				$form = $form2;//disable a message for resend data by page-refresh
			}else{
				$this->get('session')->set('my_user_ok', 'Nothing is created');
			}
		}
		
		return $this->render ( 'user/user.html.twig', array (
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("my/username", name="my_username")
	 */
	public function usernameAction(Request $request) {
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			return $this->redirect ( $this->generateUrl ( 'login') );
		}
		//init current user
		$session = $request->getSession();
		$session->set ( 'my_username_valid', '' );
	
		//$up = new UserUsername();
		$form = $this->createForm ( UserUsernameType::class, $this->u() );
		$form2 = clone $form;
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$uu = $this->em()->getRepository('AppBundle:User')->findOneBy(array('username' => $this->u()->getUsername()));
				if ($uu){
					$session->set( 'my_username_valid','Der Benutzername ist schon vorhanden');
					//return $this->redirect ( $this->generateUrl ( 'my_edit_username') );
					return $this->render ( 'user/username.html.twig', array (
							'form' => $form->createView () ,
							'u' => $this->u()
					) );
				}
				// TODO: Persist the ui entity
				$dn_old = Ses::getUpDirTmp($this->u()->getUsername());
				$dn_new = Ses::getUpDirImg()."/".$this->u()->getUsername();
				//$this->u()->setUsername($this->u()->getUsername());
				if (rename($dn_old,$dn_new)) {
					$this->em()->persist ( $this->u() );
					$this->em()->flush ();
					$session->set ( 'my_username_valid', '' );
					$form = $form2;//disable a message for resend data by page-refresh
				}else{
					$session->set( 'my_username_valid', 'User-folder could not be renamed' );
				}
				return $this->redirect ( $this->generateUrl ( 'my_user') );
			} else {
				// @TODO Error hanlder write to log and show to user
				$session->set ( "my_username_valid",$form->getErrors() );
			}
		}
	
		return $this->render ( 'user/username.html.twig', array (
				'form' => $form->createView () ,
		) );
	}
	
	/**
	 * @Route("my/email", name="my_email")
	 */
	public function emailAction(Request $request) {
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			return $this->redirect ( $this->generateUrl ( 'login') );
		}
		//init current user
		$session = $request->getSession();
		$session->set ( 'my_email_valid', '' );
	
		//$up = new UserEmail();
		$form = $this->createForm ( UserEmailType::class, $this->u() );
		$form2 = clone $form;
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$uu = $this->em()->getRepository('AppBundle:User')->findOneBy(array('email' => $this->u()->getEmail()));
				if ($uu){
					$session->set( 'my_email_valid','Der Benutzername ist schon vorhanden');
					//return $this->redirect ( $this->generateUrl ( 'my_edit_email') );
					return $this->render ( 'user/email.html.twig', array (
							'form' => $form->createView () ,
							'u' => $this->u()
					) );
				}
				// TODO: Persist the ui entity
				//$this->u()->setEmail($this->u()->getEmail());
				$this->em()->persist ( $this->u() );
				$this->em()->flush ();
				$session->set ( 'my_email_valid', '' );
				$form = $form2;//disable a message for resend data by page-refresh
				return $this->redirect ( $this->generateUrl ( 'my_user') );
			} else {
				// @TODO Error hanlder write to log and show to user
				$session->set ( "my_email_valid",$form->getErrors() );
			}
		}
	
		return $this->render ( 'user/email.html.twig', array (
				'form' => $form->createView () ,
		) );
	}
	
	
	/**
	 * @Route("my/password", name="my_password") 
	 */
	public function passwordAction(Request $request) {
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			return $this->redirect ( $this->generateUrl ( 'login') );
		}
		//init current user
		$session = $request->getSession();
		$session->set ( 'my_password_valid', '' );
		
		$up = new UserPassword();
	
		//$up = new UserPassword();
		$form = $this->createForm ( UserPasswordType::class, $up );
		$form2 = clone $form;
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				// TODO: Persist the ui entity
				$this->u()->setPassword($up->password);
				$this->em()->persist ( $this->u() );
				$this->em()->flush ();
				$session->set ( 'my_password_valid', '' );
				$form = $form2;//disable a message for resend data by page-refresh
				return $this->redirect ( $this->generateUrl ( 'my_user') );
			} else {
				// @TODO Error hanlder write to log and show to user
				$session->set ( "my_password_valid",$form->getErrors() );
			}
		}
	
		return $this->render ( 'user/password.html.twig', array (
				'form' => $form->createView () ,
		) );
	}

}