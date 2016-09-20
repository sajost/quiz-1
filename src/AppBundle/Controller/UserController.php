<?php
// src/AppBundle/Controller/UserController.php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends QController {
	
	
	/**
	 * @param unknown $id
	 */
	public function driveAction($u_id = null)
	{
		
		$uu = $this->em()->getRepository ( 'AppBundle:User' )->find($u_id);
		if (!$uu) {
			throw $this->createNotFoundException ( 'Unable to find User by id: '.$u_id );
		}
		$cars = $this->em()->getRepository('AppBundle:Car')->getCarsByUserStatus($uu->getId(),1);
		$cars0 = $this->em()->getRepository('AppBundle:Car')->getCarsByUserStatus($uu->getId(),0);
	
		return $this->render('AppBundle:User:drive.html.twig', array('uu'=>$uu, 'cars' => $cars, 'cars0'=>$cars0));
	}
	
	/**
	 * @param unknown $id
	 */
	public function driveNaviAction($u_id = null)
	{
	
		$uu = $this->em()->getRepository ( 'AppBundle:User' )->find($u_id);
		if (!$uu) {
			throw $this->createNotFoundException ( 'Unable to find User by id: '.$u_id );
		}
		$cars = $this->em()->getRepository('AppBundle:Car')->getCarsByUser($uu->getId());
	
		return $this->render('AppBundle:User:carsNavi.html.twig', array('uu'=>$uu, 'cars' => $cars));
	}
	
	/**
	 * @param unknown $id
	 */
	public function readAction($id = null, $typ='car')
	{
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return $this->redirect ( $this->generateUrl ( 'login_route') );
		}
		//*************RIGHTS************************************
	
		//init current user
		$uu = $this->em()->getRepository('AppBundle:User')->find($id);
		if (!$uu) {
			throw $this->createNotFoundException('User is not found, id='-$id);
		}
	
		return $this->render('AppBundle:User:read.'.$typ.'.html.twig', array(
				'u'=>$this->u(),
				'uu'=>$uu,
		));
	}
	
	/**
	 * @param unknown $id
	 */
	public function readerAction($id = null)
	{
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return $this->redirect ( $this->generateUrl ( 'login_route') );
		}
		//*************RIGHTS************************************
		
		//init current user
		$uu = $this->em()->getRepository('AppBundle:User')->find($id);
		if (!$uu) {
			throw $this->createNotFoundException('User is not found, id='-$id);
		}
		
		$readmeusers = $this->em()->getRepository('AppBundle:UserReader')->getUserReaderByUserReadMe($uu->getId());
	
	
		return $this->render('AppBundle:User:reader.html.twig', array(
				'u'=>$this->u(),
				'uu'=>$uu,
				'readmeusers'=>$readmeusers,
		));
	}
	
	/**
	 * @param unknown $id
	 */
	public function qtipAjAction(Request $request, $id = null)
	{
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return $this->redirect ( $this->generateUrl ( 'login_route') );
		}
		//*************RIGHTS************************************
		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}
	
		//init current user
		$uu = $this->em()->getRepository('AppBundle:User')->find($id);
		if (!$uu) {
			throw $this->createNotFoundException('User is not found, id='-$id);
		}
	
		//check if opponent-user is in the black user
		$uu_in_black = array('s'=>0,'class'=>' but-red but-black-active ','title'=>$this->p('but_black_in'));
		if ($this->isU1InBlackOfU2($uu,$this-u()))$uu_in_black = array('s'=>1,'class'=>' but-grey ','title'=>$this->p('but_black_out'));
	
		return $this->render('AppBundle:User:qtip.html.twig', array(
				'u'=>$this->u(),
				'uu'=>$uu,
				'uu_in_black'=>$uu_in_black,
		));
	}
	
	/**
	 * @param unknown $id
	 */
	public function blackAction($id = null)
	{
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
			return $this->redirect ( $this->generateUrl ( 'login_route') );
		}
		//*************RIGHTS************************************
	
		//init current user
		$uu = $this->em()->getRepository('AppBundle:User')->find($id);
		if (!$uu) {
			throw $this->createNotFoundException('User is not found, id='-$id);
		}
	
		return $this->render('AppBundle:User:black.user.html.twig', array(
				'u'=>$this->u(),
				'uu'=>$uu,
		));
	}

}