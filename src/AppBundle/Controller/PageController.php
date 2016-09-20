<?php
// src/AppBundle/Controller/PageController.php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class PageController extends QController {
	
	/**
	 * Main-start of the Webportal
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request) {
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
			//  authenticated (NON anonymous)
			$vehbrands = $this->r('VehBrand')->getVehBrandsAll();
			//$carpromoday = $this->em()->getRepository('AppBundle:Car')->getCarsPromoBest(1);
			$carmostvisited = $this->getMostVisitedToday('Car'); //$this->em()->getRepository('AppBundle:CarGuest')->getMostVisitedToday($today0, $today23);
			$carpromodaylast = $this->em()->getRepository('AppBundle:Car')->getCarsPromoBest(15);
			//$promocarday = $this->em()->getRepository('AppBundle:Promotion')->getPromoDay();
			$bloglast = $this->em()->getRepository('AppBundle:Blog')->getLast(4);
			$carlast = $this->em()->getRepository('AppBundle:Car')->getLast(4);
			$carloglast = $this->em()->getRepository('AppBundle:CarLog')->getLast(4);
			$comybloglast = $this->em()->getRepository('AppBundle:ComyBlog')->getLast(4);
				
	
			return $this->render ( 'AppBundle:Page:index.html.twig', array (
					'vehbrands' => $vehbrands,
					'carpromodaylast' => $carpromodaylast,
					'carmostvisited' => $carmostvisited,
					//'carpromoday' => $carpromoday,
					'bloglast' => $bloglast,
					'carlast' => $carlast,
					'carloglast' => $carloglast,
					'comybloglast' => $comybloglast,
			) );
		}else{
			//  NON authenticated (ANONYMOUS)
			$session = $request->getSession ();
				
			if ($request->attributes->has ( Security::AUTHENTICATION_ERROR )) {
				$error = $request->attributes->get ( Security::AUTHENTICATION_ERROR );
			} elseif (null !== $session && $session->has ( Security::AUTHENTICATION_ERROR )) {
				$error = $session->get ( Security::AUTHENTICATION_ERROR );
				$session->remove ( Security::AUTHENTICATION_ERROR );
			} else {
				$error = '';
			}
			$lastUsername = (null === $session) ? '' : $session->get ( Security::LAST_USERNAME );
				
			$carmostvisited = $this->getMostVisitedToday('Car');
			//$cars = $this->em()->getRepository('AppBundle:Car')->getLast(10);
			$carpromodaylast = $this->r('Car')->getCarsPromoBest(15);
				
			$bloglast = $this->em()->getRepository('AppBundle:Blog')->getLast(4);
			$carloglast = $this->em()->getRepository('AppBundle:CarLog')->getLast(4);
				
			return $this->render ( 'AppBundle:Page:index.all.html.twig', array (
					'last_username' => $lastUsername,
					'error' => $error,
					'carmostvisited'  => $carmostvisited,
					'carpromodaylast' => $carpromodaylast,
					'bloglast' => $bloglast,
					'carloglast' => $carloglast,
			) );
		}
	}
	
	public function regWellcomeAction(Request $request) {
		if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return $this->redirect($this->generateUrl('home'));
		}else{
			return $this->render ('AppBundle:Page:reg.wellcome.html.twig', array (
			) );
		}
	}
	
	
	public function regActivateAction(Request $request) {
		if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//  authenticated (NON anonymous)
			return $this->redirect($this->generateUrl('home'));
		}else{
		    //$session = $request->getSession();
		    //$session->set ( "me_acktivate_valid","" );
			$em = $this->getDoctrine ()->getManager ();
			//$user = $this->get('security.token_storage')->getToken()->getUser();
			$ua = new UserActivate();
			//$ua->username=$session->get ( "me_acktivate_user");
			if ($ua->username=="")
				$ua->username=$request->query->get('login');
			$ua->token=$request->query->get('token');
			$form = $this->createForm(new UserActivateType($em), $ua); 
			//$cars = $this->em()->getRepository('AppBundle:Car')->getLast(10);
			$carmostvisited = $this->getMostVisitedToday('Car');
	
			if ($request->isMethod('POST')) {
				$form->handleRequest ( $request );
				if ($form->isValid()) {
					$user = $this->em()->getRepository('AppBundle:User')->getUserByActivation($ua->username,$ua->token);
					if ($user != null){
						// TODO: Persist the user entity
						$user->setStatus(1);
						$this->em()->persist($user);
						$this->em()->flush();
						//$session->set ( "me_acktivate_valid","" );
						return $this->redirect($this->generateUrl('me_activate_wellcome', array ('_' => 'a' )));
					}else{
						//$session->set ( "me_acktivate_valid",$this->p('valid_activate') );
						return $this->redirect($this->generateUrl('me_activate'));
						return $this->redirect($this->generateUrl('me_activate',array('login' => $ua->username,'token'=>$ua->token,'.'=>'')));
					}
				} else{
					//@TODO Error hanlder write to log and show to user
				    //$session->set ( "me_acktivate_valid",$form->getErrors() );
				}
			}
			return $this->render ('AppBundle:Page:reg.send.activation.html.twig', array (
					'form' => $form->createView(),
					'carmostvisited' => $carmostvisited
			) );
		}
	}
	
	
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function formRegAction(Request $request) {
	    //anonymous
	    if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
	        $form = $this->regAction($request);
	        $ucount = count($this->em()->getRepository('AppBundle:User')->findAll());
	        return $this->render ( 'AppBundle:Page:form.reg.html.twig', array (
	        		'formreg' => $form->createView(),
	        		'ucount' => $ucount,
	        ) );
	    }
	    return $this->render ( 'AppBundle::e.html.twig', array (
	    ) );
	}
	
	
	
	
}