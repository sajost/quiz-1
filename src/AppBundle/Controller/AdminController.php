<?php
// src/AppBundle/Controller/AdminController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\QuestionCat;
use Symfony\Component\Form\CallbackTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends QController {
	
	/**
	 * @Route("admin/", name="admin")
	 * yes
	 */
	public function indexAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		return $this->render ( 'admin/index.html.twig', array (
		) );
	}

	
	
	public function countryAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
		$countrys = $this->em()->getRepository('AppBundle:Country')->getCountrysAll();
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('countrynew', 'textarea', array(
				'label' => 'New countrys',
				'required'=>false,
				'attr' => array('style' => 'width:600px;height:800px;')
		))
		->add('countrys', 'entity', array(
				'class' => 'AppBundle:Country',
				'property' => 'country',
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder ( 'c' )->select ( 'c' )->addOrderBy ( 'c.country', 'ASC' );
					},
				))
		->add('submit','submit')
		->getForm();
			
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------remove
				$vehbrands = $form->get('countrys')->getData();
				foreach($vehbrands as $co_del) {
					//$b_del = $this->em()->getRepository('AppBundle:VehBrand')->getVehBrandByName(strtolower($bnew));
					if (true !== is_null ($co_del)){
						$this->em()->remove($co_del);
						$this->em()->flush();
					}
				}
				//------------------new
				$countrynew = preg_split ('/\n|\r\n?/', $form->get('countrynew')->getData());
				//var_dump($countrynew);
				$co = null;$co_old=null;
				foreach($countrynew as $conew) {
					if (!is_null($conew) & $conew!==""){
						$co_old = $this->em()->getRepository('AppBundle:Country')->getCountryByName(strtolower($conew));
						if (true === is_null ($co_old)){
							$co = new Country();
							$co->setCountry($conew);
							$co->setLogo(str_replace(' ', '_', strtolower($conew)));
							$this->em()->persist($co);
						}
					}
					$co_old=null;
				}
				if ($co!=null){
					$this->em()->flush();
					$this->get('session')->set('admin_country_ok', 'Country is OK');
				}else {
					$this->get('session')->set('admin_country_ok', 'Nothing is created');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_country') );
				//$countrys = $this->em()->getRepository('AppBundle:Country')->getCountrysAll();
			}
		}
			
		return $this->render ( 'AppBundle:Admin:country.html.twig', array (
				'countrys' => $countrys,
				'form'=>$form->createView()
		) );
	}
	
	public function cityAction(Request $request,$coid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$country = $this->em()->getRepository('AppBundle:Country')->find($coid);
		$citys = $this->em()->getRepository('AppBundle:City')->getCitysByCountry($coid);
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('bid', 'hidden', array(
				'data' => $coid,
		))
		->add('citys', 'entity', array(
				'class' => 'AppBundle:City',
				'property' => 'city',
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'query_builder' => function (EntityRepository $er) use($coid) {
					return $er->createQueryBuilder ( 'c' )->select ( 'c' )->where('c.country = :country')
						->addOrderBy ( 'c.city', 'ASC' )->setParameter('country', $coid);;
					},
				))
		->add('country', 'text', array(
				'data' => $country->getCountry(),
		))
		->add('citynew', 'textarea', array(
				'label' => 'New citys',
				'attr' => array('style' => 'width:600px;height:800px;')
		))
		->getForm();
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit country
				$country_country = $form->get('country')->getData();
				if (!is_null($country_country) & $country_country!=""){
					$country->setCountry($country_country);
					$this->em()->persist ( $country );
					$this->em()->flush();
				}
				//------------------remove
				$citys = $form->get('citys')->getData();
				foreach($citys as $c_del) {
					if (true !== is_null ($c_del)){
						$this->em()->remove($c_del);
						$this->em()->flush();
					}
				}
				//------------------new
				$citynew = preg_split ('/\n|\r\n?/', $form->get('citynew')->getData());
				//var_dump($citynew);
				$ci = null;$ci_old=null;
				foreach($citynew as $cinew) {
					if (!is_null($cinew) & $cinew!==""){
						$ci_old = $this->em()->getRepository('AppBundle:City')->getCityByName($coid,strtolower($cinew));
						//var_dump("cinew - ".$cinew);
						//var_dump("coid - ".$coid."  ci_old-- ".$ci_old); 
						if (true === is_null ($ci_old)) {
							$ci = new City ();
							$ci->setCountry ( $country ); 
							$ci->setCity ( $cinew );
							$ci->setLogo ( str_replace ( ' ', '_', strtolower ( $cinew ) )  );
							$this->em()->persist ( $ci );
						}
					}
					$ci_old=null;
				}
				if (is_null($ci)===true){
					$this->get('session')->set('admin_city_ok', 'Nothing is created');
				}else {
					$this->em()->flush();
					$this->get('session')->set('admin_city_ok', 'City is OK');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_city', array ('coid' => $coid)) );
				//$citys = $this->em()->getRepository('AppBundle:City')->getCitysByCountry($coid);
			}
		}
	
		return $this->render ( 'AppBundle:Admin:city.html.twig', array (
				'country' => $country,
				'citys' => $citys,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @param Request $request
	 * @param unknown $coid
	 * @param unknown $ciid
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function cityeAction(Request $request,$coid=null, $ciid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
	
		$city = $this->em()->getRepository('AppBundle:City')->find($ciid);
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('ciid', 'hidden', array(
				'data' => $ciid,
		))
		->add('city', 'text', array(
				'data' => $city->getCity(),
		))
		->getForm();
					
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit city
				$city_city = $form->get('city')->getData();
				if (!is_null($city_city) & $city_city!=""){
					$city->setCity($city_city);
					$this->em()->persist ( $city );
					$this->em()->flush();
					$this->get('session')->set('admin_citye_ok', 'City is OK');
				}else{
					$this->get('session')->set('admin_citye_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_city', array ('coid' => $coid)) );
				//$citys = $this->em()->getRepository('AppBundle:City')->getCitysByCountry($coid);
			}
		}

		return $this->render ( 'AppBundle:Admin:citye.html.twig', array (
				'city' => $city,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function globalAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
	
		//$city = $this->em()->getRepository('AppBundle:City')->find($ciid);
	
		$dd = array('1' => 'What');
		$form = $this->createFormBuilder($dd)
		->add('id', 'hidden', array(
				'data' => '1',
		))
		->add('notify_off', 'text', array(
				'data' => $this->p('notify_off'),
		))
		->add('notify_use', 'text', array(
				'data' => $this->p('notify_use'),
		))
		->getForm();
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit notify_off
				$notify_off = $form->get('notify_off')->getData();
				if (!is_null($notify_off) & $notify_off!=""){
					$this->p('notify_off',$notify_off); 
					$this->get('session')->set('admin_notify_off_ok', 'notify_off is OK');
				}else{
					$this->get('session')->set('admin_notify_off_ok', 'Nothing is edited');
				}
				//------------------edit notify_use
				$notify_use = $form->get('notify_use')->getData();
				if (!is_null($notify_use) & $notify_use!=""){
					$this->p('notify_use',$notify_use);
					$this->get('session')->set('admin_notify_use_ok', 'notify_use is OK');
				}else{
					$this->get('session')->set('admin_notify_use_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_global') );
				//$citys = $this->em()->getRepository('AppBundle:City')->getCitysByCountry($coid);
			}
		}
	
		return $this->render ( 'AppBundle:Admin:global.html.twig', array (
				'dd' => $dd,
				'form'=>$form->createView()
		) );
	}
	
	public function questioncatAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
		$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsAll();
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('questioncatnew', 'textarea', array(
				'label' => 'New questioncats',
				'required'=>false,
				'attr' => array('style' => 'width:600px;height:800px;')
		))
		->add('questioncats', 'entity', array(
				'class' => 'AppBundle:QuestionCat',
				'property' => 'sub',
				'choice_label' => function ($clc) {
				       return $clc->getCat()."~~".$clc->getSub();
				 },
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'query_builder' => function (EntityRepository $er) { return $er->getQuestionCatsAllQB(); } )
		)
		->add('submit','submit')
		->getForm();
					
					
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------remove
				$catlogcats = $form->get('questioncats')->getData();
				foreach($catlogcats as $clc_del) {
					//$b_del = $this->em()->getRepository('AppBundle:VehBrand')->getVehBrandByName(strtolower($bnew));
					if (true !== is_null ($clc_del)){
						$this->em()->remove($clc_del);
						$this->em()->flush();
					}
				}
				//------------------new
				$questioncatnew = preg_split ('/\n|\r\n?/', $form->get('questioncatnew')->getData());
				//var_dump($questioncatnew);
				$clc = null;$clc_old=null;
				foreach($questioncatnew as $conew) {
					if (!is_null($conew) & $conew!==""){
						$cat="";$sub="";
						list($cat,$sub)=split('~~', $conew);
						$clc_old = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatByCatSub(strtolower($cat),strtolower($sub));
						if (true === is_null ($clc_old)){
							$clc = new QuestionCat();
							$clc->setCat($cat);
							$clc->setSub($sub);
							$this->em()->persist($clc);
							$this->em()->flush();
						}
					}
					$clc_old=null;
				}
				if ($clc!=null){
					$this->get('session')->set('admin_questioncat_ok', 'QuestionCat is OK');
				}else {
					$this->get('session')->set('admin_questioncat_ok', 'Nothing is created');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_questioncat') );
				//$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsAll();
			}
		}
			
		return $this->render ( 'AppBundle:Admin:questioncat.html.twig', array (
				'questioncats' => $questioncats,
				'form'=>$form->createView()
		) );
	}
	
	public function questioncateAction(Request $request,$clcid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
	
		$questioncat = $this->em()->getRepository('AppBundle:QuestionCat')->find($clcid);
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('clcid', 'hidden', array(
				'data' => $clcid,
		))
		->add('cat', 'text', array(
				'data' => $questioncat->getCat(),
		))
		->add('sub', 'text', array(
				'data' => $questioncat->getSub(),
		))
		->getForm();
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit questioncat
				$cat = $form->get('cat')->getData();
				$sub = $form->get('sub')->getData();
				if (!is_null($cat) & $cat!="" & !is_null($sub) & $sub!=""){
					$questioncat->setCat($cat);
					$questioncat->setSub($sub);
					$this->em()->persist ( $questioncat );
					$this->em()->flush();
					$this->get('session')->set('admin_questioncate_ok', 'QuestionCat is OK');
				}else{
					$this->get('session')->set('admin_questioncate_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_questioncat') );
				//$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsByCountry($coid);
			}
		}
	
		return $this->render ( 'AppBundle:Admin:questioncate.html.twig', array (
				'questioncat' => $questioncat,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function userAction(Request $request) {
	
		$users = $this->em()->getRepository('AppBundle:User')->findAll();
	
		return $this->render ( 'AppBundle:Admin:user.html.twig', array (
				'users' => $users,
		) );
	}
	
	public function usereAction(Request $request,$uid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		
	
		$user = $this->em()->getRepository('AppBundle:User')->find($uid);
	
		$defaultData = array('1' => 'What');
		$builder = $this->createFormBuilder($defaultData)
		->add('role', 'entity', array(
        		'class'       => 'AppBundle:UserRole',
        		'property' => 'role',
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'label'=>'Rollen',
        		'query_builder' => function (EntityRepository $er) use($uid) {
					return $er->createQueryBuilder ( 'c' )->select ( 'c' );
				},
				'data'=> $user->getUserroles()
        ))
        ->add('status','checkbox', array(
        		'required' => false,
        		'label'=>'Aktiviert: ',
        		'data'=> $user->getStatus(),
        ))
        
        ;
        $builder->get('status')->addModelTransformer(new CallbackTransformer( function ($v) { return $v==1?true:false; },function ($v) { return $v==true?1:0; }));
		
        $form = $builder->getForm();
		
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit user
				$role = $form->get('role')->getData();
				$status = $form->get('status')->getData();
				if (!is_null($role) | !is_null($status)){
					$user->setStatus($status);
					//foreach ($role as $r){ 
						//dump($r);
						//$user->addUserRole($r);
					//}
					//$this->em()->persist ( $user );
					$this->em()->flush();
					$this->get('session')->set('admin_usere_ok', 'user is OK');
				}else{
					$this->get('session')->set('admin_usere_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_usere',array(
						'uid' => $user->getId(),
						'_'=>'y'
				)) );
			}
		}
	
		return $this->render ( 'AppBundle:Admin:usere.html.twig', array (
				'user' => $user,
				'form'=>$form->createView()
		) );
	}
}