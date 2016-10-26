<?php
// src/AppBundle/Controller/PageController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class DefaultController extends QController {
	
	/**
	 * Main-start of the Webportal
	 * @Route("/", name="home")
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request) {
		 if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
			//  authenticated (NON anonymous)
		 	
			$quizs = $this->r('Quiz')->getQuizsAll();
	
			return $this->render ( 'default/index.html.twig', array (
					'quizs' => $quizs,
			) );
		}else{
			$quizs = $this->r('Quiz')->getQuizsAll();
			
			return $this->render ( 'default/index.html.twig', array (
					'quizs' => $quizs,
			) );
		}
	}
	
	
	
}