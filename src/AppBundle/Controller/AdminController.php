<?php
// src/AppBundle/Controller/AdminController.php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\QuestionCat;
use Symfony\Component\Form\CallbackTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Entity\QuestionTag;
use AppBundle\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Question;
use AppBundle\Form\UserEType;
use AppBundle\Utils\Ses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Form\QuestionType;
use AppBundle\Entity\Quiz;
use AppBundle\Form\QuizType;
use AppBundle\Entity\Answer;
use AppBundle\Entity\QuizQuestion;
use AppBundle\Form\QuizQuestionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminController extends QController {
	
	/**
	 * @Route("admin/", name="admin")
	 * yes
	 */
	public function indexAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		return $this->render ( 'admin/index.html.twig', array (
				'base_dir' => realpath ( $this->getParameter ( 'kernel.root_dir' ) . '/..' )
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
	
	/**
	 * @Route("admin/user", name="admin_user")
	 */
	public function userAction(Request $request) {
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//????
		}
		//*************RIGHTS************************************
	
		$user = new User();
	
		$form = $this->createForm ( UserType::class, $user );
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				
				$p = $request->get ( 'user' );
				//var_dump($p);
				// crop & resize the avatar image
				if ($p ['avatar'] != null && $p ['avatar'] != '') {
					if (!Ses::imgCrop (
							Ses::getUpDirTmp ( $user->getUsername() ) . "/" . $p ['avatar'],
							$p ['avatar_x'],
							$p ['avatar_y'],
							$p ['avatar_w'],
							$p ['avatar_h'] )) {
								$this->get('session')->set ( "admin_user_ok", 'The Image-Crop-Function return false, check logs' );
							}
					$img_constraint = array (
							'constraint' => array (
									'width' => 100,
									'height' => 100
							)
					);
					$avatar=Ses::after('tmp_', $p ['avatar']);
					Ses::imgResize ( Ses::getUpDirTmp ($user->getUsername()) . "/" . $p ['avatar'], Ses::getUpDirTmp ($user->getUsername()) . "/" . $avatar, $img_constraint );
					$user->setAvatar($avatar); 
				}
				$this->em()->persist ( $user );
				$this->em()->flush ();
				$this->get('session')->set('admin_user_ok', 'User is OK');
				$form = $form2;//disable a message for resend data by page-refresh
			}else{
				$this->get('session')->set('admin_user_ok', 'Nothing is created');
			}
		}
		$users = $this->em()->getRepository('AppBundle:User')->findBy(array(), array('username' => 'ASC'));
			
		return $this->render ( 'admin/user.html.twig', array (
				'users' => $users,
				'user' => $user,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 *	upAvatarAction
	 * @Route("admin/usere/avatar", name="admin_usere_avatar")
	 *
	 * @param
	 *
	 */
	public function upAvatarAction(Request $request) {
		$ret1 = null;
		if (is_object(parent::upFotoAction1($ret1))) return $ret1;
		
		//TODO check username if already exist, then return validation-message, check only by new users
	
		$fd = $request->files->get('user');
		$un = $request->get('user_username');
		$ffoto = $fd['avatar_f'];//$request->files->get('user[avatar_f]', array(), true);
		//var_dump($request->files->all());
		//$ix = $blog->getFotos()->count()+1;
		$typ="admin_avatar";
		$img_min_w = 100;
		$img_min_h = 100;
		$img_p_big = array(	'constraint' => array('width' => 200, 'height' => 200));
		$img_p_sm = array(	'constraint' => array('width' => 100, 'height' => 100));
	
		return new JsonResponse(
				parent::upFotoAction2(1,1,$ffoto,$typ,$img_min_w,$img_min_h,$img_p_sm,$img_p_big,$un),
				200,
				array('Content-Type'=>'application/json')
				);
	
	}
	
	
	/**
	 * @Route("admin/usere/{eid}", name="admin_usere")
	 */
	public function usereAction(Request $request,$eid=null) {
		//*************RIGHTS************************************
		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
			//????
		}
		//*************RIGHTS************************************
	
		$user = $this->em()->getRepository('AppBundle:User')->find($eid);
	
		$form = $this->createForm ( UserEType::class, $user ); 
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				
				$p = $request->get ( 'user' );
				//var_dump($p);
				// crop & resize the avatar image
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if (!Ses::imgCrop (
							Ses::getUpDirTmp ( $user->getUsername() ) . "/" . $p ['avatar'],
							$p ['avatar_x'],
							$p ['avatar_y'],
							$p ['avatar_w'],
							$p ['avatar_h'] )) {
								$this->get('session')->set ( "admin_user_ok", 'The Image-Crop-Function return false, check logs' );
							}
						
					$img_constraint = array (
							'constraint' => array (
									'width' => 100,
									'height' => 100
							)
					);
					$avatar=Ses::after('tmp_', $p ['avatar']);
					dump($avatar);
					Ses::imgResize ( Ses::getUpDirTmp ($user->getUsername()) . "/" . $p ['avatar'], Ses::getUpDirTmp ($user->getUsername()) . "/" . $avatar, $img_constraint );
					$user->setAvatar($avatar); 
				}
				
				$this->em()->persist ( $user );
				$this->em()->flush ();
				$this->get('session')->set('admin_user_ok', 'User is OK');
				$user = $this->em()->getRepository('AppBundle:User')->find($eid);
				$form = $form2;//disable a message for resend data by page-refresh
			}else{
				$this->get('session')->set('admin_user_ok', 'Nothing is created');
			}
		}
		
		return $this->render ( 'admin/usere.html.twig', array (
				'user' => $user,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("admin/user/is", name="admin_user_is")
	 * 
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function userIsAction(Request $request){
		//*************RIGHTS************************************
		// 		if( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ){
		// 			return $this->redirect ( $this->generateUrl ( 'login_route') );
		// 		}
		//*************RIGHTS************************************
		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}
		$enty=null;
		$ret = '';//[];
		$err = "";
		$up = $request->query->get('user');//Structure is: array(1) { ["user"]=> array(1) { ["username"]=> string(1) "b" } } 
		$new = $up[key( $up )];
		//var_dump(key( $up ) . '---' . $new);
		//init current user
		//
		switch (key( $up )){
			case "username":
				$enty = $this->em()->getRepository ( 'AppBundle:User' )->getUserByUsername( $new );
				$err = "Solche Login ist schon vorhanden";
				break;
			case "email":
				$enty = $this->em()->getRepository ( 'AppBundle:User' )->getUserByEmail( $new );
				$err = "Solche Email ist schon vorhanden";
				break;
			default:
				$enty = $this->em()->getRepository ( 'AppBundle:User' )->getUserByActivation ( $new );
				$err = "Solche Benutzer ist schon vorhanden";
				break;
		}
	
		if ($enty) {
			$ret = $err;//array('jerr'=>$err);
			return new JsonResponse($ret,419);
		}else{
			$ret = 'OK';//array('jerr'=>"OK");
			return new JsonResponse($ret,200);
		}
	
	}
	
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function uroleAction(Request $request) {
	
		$users = $this->em()->getRepository('AppBundle:User')->findAll();
	
		return $this->render ( 'AppBundle:Admin:user.html.twig', array (
				'users' => $users,
		) );
	}
	
	public function uroleeAction(Request $request,$uid=null) {
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
	
	/**
	 * @Route("admin/qcat", name="admin_qcat")
	 */
	public function questioncatAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsAll();
		//$questioncats = array();
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('questioncatnew', TextType::class, array(
				'label' => false,
				'required'=>false,
		))
		->add('questioncats', EntityType::class, array(
				'label' => false,
				'class' => 'AppBundle:QuestionCat',
				'choice_label' => 'title',
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'query_builder' => function (EntityRepository $er) {
					return $er->createQueryBuilder ( 'e' )->select ( 'e' )->addOrderBy ( 'e.title', 'ASC' );
				},
				))
		->add('submit',SubmitType::class)
		->getForm();
					
					
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------remove
				$questioncats = $form->get('questioncats')->getData();
				foreach($questioncats as $e_del) {
					if (true !== is_null ($e_del)){
						$this->em()->remove($e_del);
						$this->em()->flush();
					}
				}
				//------------------new
				//$questioncatnew = preg_split ('/\n|\r\n?/', $form->get('questioncatnew')->getData());
				$questioncatnew = $form->get('questioncatnew')->getData();
				//var_dump($questioncatnew);
				$e = null;$e_old=null;
				//foreach($questioncatnew as $dnew) {
				$dnew = $questioncatnew;
				if (!is_null($dnew) & $dnew!==""){
					$e_old = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatByCat(strtolower($dnew));
					if (true === is_null ($e_old)){
						$e = new QuestionCat();
						$e->setTitle($questioncatnew);
						$this->em()->persist($e);
						$this->em()->flush();
					}
				}
				$e_old=null;
				//}
				if ($e!=null){
					$this->get('session')->set('admin_questioncat_ok', 'QuestionCat is OK');
				}else {
					$this->get('session')->set('admin_questioncat_ok', 'Nothing is created');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_qcat') );
			}
		}
			
		return $this->render ( 'admin/q.cat.html.twig', array (
				'questioncats' => $questioncats,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("admin/qcate/{eid}", name="admin_qcate")
	 */
	public function questioncateAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
	
		$questioncat = $this->em()->getRepository('AppBundle:QuestionCat')->find($eid);
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('eid', HiddenType::class, array(
				'data' => $eid,
		))
		->add('title', TextType::class, array(
				'data' => $questioncat->getTitle(),
		))
		->getForm();
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit questioncat
				$dedit = $form->get('title')->getData();
				if (!is_null($dedit) & $dedit!=""){
					$questioncat->setTitle($dedit);
					$this->em()->persist ( $questioncat );
					$this->em()->flush();
					$this->get('session')->set('admin_questioncate_ok', 'QuestionCat is OK');
				}else{
					$this->get('session')->set('admin_questioncate_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_qcat') );
				//$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsByCountry($coid);
			}
		}
	
		return $this->render ( 'admin/q.cate.html.twig', array (
				'questioncat' => $questioncat,
				'form'=>$form->createView(),
		) );
	}



	/**
	 * @Route("admin/qtag", name="admin_qtag")
	 */
	public function questiontagAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$questiontags = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagsAll();
		//$questiontags = array();
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('questiontagnew', TextType::class, array(
				'label' => false,
				'required'=>false,
		))
		->add('questiontags', EntityType::class, array(
				'label' => false,
				'class' => 'AppBundle:QuestionTag',
				'choice_label' => 'title',
				'expanded' => true,
				'multiple' => true,
				'required'=>false,
				'query_builder' => function (EntityRepository $er) {
				return $er->createQueryBuilder ( 'e' )->select ( 'e' )->addOrderBy ( 'e.title', 'ASC' );
				},
				))
				->add('submit',SubmitType::class)
				->getForm();
					
					
				if ($request->isMethod ( 'POST' )) {
					$form->handleRequest ( $request );
					if ($form->isValid ()) {
						//------------------remove
						$questiontags = $form->get('questiontags')->getData();
						foreach($questiontags as $e_del) {
							if (true !== is_null ($e_del)){
								$this->em()->remove($e_del);
								$this->em()->flush();
							}
						}
						//------------------new
						//$questiontagnew = preg_split ('/\n|\r\n?/', $form->get('questiontagnew')->getData());
						$questiontagnew = $form->get('questiontagnew')->getData();
						//var_dump($questiontagnew);
						$e = null;$e_old=null;
						//foreach($questiontagnew as $dnew) {
						$dnew = $questiontagnew;
						if (!is_null($dnew) & $dnew!==""){
							$e_old = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagByTag(strtolower($dnew));
							if (true === is_null ($e_old)){
								$e = new QuestionTag();
								$e->setTitle($questiontagnew);
								$this->em()->persist($e);
								$this->em()->flush();
							}
						}
						$e_old=null;
						//}
						if ($e!=null){
							$this->get('session')->set('admin_questiontag_ok', 'QuestionTag is OK');
						}else {
							$this->get('session')->set('admin_questiontag_ok', 'Nothing is created');
						}
						return $this->redirect ( $this->generateUrl ( 'admin_qtag') );
					}
				}
					
				return $this->render ( 'admin/q.tag.html.twig', array (
						'questiontags' => $questiontags,
						'form'=>$form->createView()
				) );
	}
	
	/**
	 * @Route("admin/qtage/{eid}", name="admin_qtage")
	 */
	public function questiontageAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
	
		$questiontag = $this->em()->getRepository('AppBundle:QuestionTag')->find($eid);
	
		$defaultData = array('1' => 'What');
		$form = $this->createFormBuilder($defaultData)
		->add('eid', HiddenType::class, array(
				'data' => $eid,
		))
		->add('title', TextType::class, array(
				'data' => $questiontag->getTitle(),
		))
		->getForm();
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit questiontag
				$dedit = $form->get('title')->getData();
				if (!is_null($dedit) & $dedit!=""){
					$questiontag->setTitle($dedit);
					$this->em()->persist ( $questiontag );
					$this->em()->flush();
					$this->get('session')->set('admin_questiontage_ok', 'QuestionTag is OK');
				}else{
					$this->get('session')->set('admin_questiontage_ok', 'Nothing is edited');
				}
				return $this->redirect ( $this->generateUrl ( 'admin_qtag') );
				//$questiontags = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagsByCountry($coid);
			}
		}
	
		return $this->render ( 'admin/q.tage.html.twig', array (
				'questiontag' => $questiontag,
				'form'=>$form->createView()
		) );
	}
	
	
	
	/**
	 * @Route("admin/question", name="admin_question")
	 */
	public function questionAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$questions = $this->em()->getRepository('AppBundle:Question')->getQuestionsAll();
		//$questions = array();
	
		$question = $this->getQuestionForm();

	
		$form = $this->createForm ( QuestionType::class, $question );
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$index = 1;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") $question->removeAnswer($answer);
					$index++;
				}
				
				$this->em()->persist ( $question );
				$this->em()->flush ();
				$this->get('session')->set('admin_question_ok', 'Question is OK');
				$form = $form2;
			}else{
				$this->get('session')->set('admin_question_ok', 'Nothing is created');
			}
		}
		$questions = $this->em()->getRepository('AppBundle:Question')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/question.html.twig', array (
				'questions' => $questions,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("admin/questione/{eid}", name="admin_questione")
	 */
	public function questioneAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$question = $this->getQuestionForm($eid);
		//dump("aaa ".$question->getAnswers()->count());
	
		$form = $this->createForm ( QuestionType::class, $question );
		//$form->get('answercount')->setData($answercount);
		//$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$index = 1;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") $question->removeAnswer($answer);
					$index++;
				}
				
				$this->em()->persist ( $question );
				$this->em()->flush ();
				$this->get('session')->set('admin_question_ok', 'Question is OK');
				
				//redirect for full data submit
				return $this->redirect ( $this->generateUrl ( 'admin_questione',array(
						'eid' => $question->getId(),
						'_'=>'y'
				)) );
				//$answercount=$question->getAnswers()->count();
// 				$form = $form2;
// 				$question = $this->getQuestionForm($eid);
// 				$form = $this->createForm ( QuestionType::class, $question );
			}else{
				$this->get('session')->set('admin_question_ok', 'Nothing is saved');
			}
		}
	
		return $this->render ( 'admin/questione.html.twig', array (
				'question' => $question,
				'form'=>$form->createView(),
				'answercount'=>$question->answercount
		) );
	}
	
	/**
	 * @param unknown $eid
	 * @return \AppBundle\Entity\Question|object
	 */
	private function getQuestionForm($eid=null){
		if ($eid==null){
			$question = new Question();
		}else{
			$question = $this->em()->getRepository('AppBundle:Question')->find($eid);
		}
		
		$answercount=4;
		if ($question->getAnswers()->count()<1){
			for ($x = 1; $x <= 8; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<2){
			for ($x = 1; $x <= 7; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<3){
			for ($x = 1; $x <= 6; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<4){
			for ($x = 1; $x <= 5; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<5){
			for ($x = 1; $x <= 4; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<6){
			$answercount=5;
			for ($x = 1; $x <= 3; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<7){
			$answercount=6;
			for ($x = 1; $x <= 2; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<8){
			$answercount=7;
			for ($x = 1; $x <= 1; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else{
			$answercount=8;
		}
		$question->answercount=$answercount;
		return $question;
	}
	
	/**
	 * @Route("admin/quiz", name="admin_quiz")
	 */
	public function quizAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$quizs = $this->em()->getRepository('AppBundle:Quiz')->getQuizsAll();
		//$quizs = array();
	
		$quiz = new Quiz();
	
		$form = $this->createForm ( QuizType::class, $quiz );
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$this->em()->persist ( $quiz );
				$this->em()->flush ();
				$this->get('session')->set('admin_quiz_ok', 'Quiz is OK');
				$form = $form2;
			}else{
				$this->get('session')->set('admin_quiz_ok', 'Nothing is created');
			}
		}
		$quizs = $this->em()->getRepository('AppBundle:Quiz')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/quiz.html.twig', array (
				'quizs' => $quizs,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("admin/quize/{eid}", name="admin_quize")
	 */
	public function quizeAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$quiz = $this->em()->getRepository('AppBundle:Quiz')->find($eid);
		//$quizs = array();
	
		$form = $this->createForm ( QuizType::class, $quiz );
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$this->em()->persist ( $quiz );
				$this->em()->flush ();
				$this->get('session')->set('admin_quiz_ok', 'Quiz is OK');
				$form = $form2;
				return $this->redirect ( $this->generateUrl ( 'admin_quize',array(
						'eid' => $quiz->getId(),
						'_'=>'y'
				)) );
			}else{
				$this->get('session')->set('admin_quiz_ok', 'Nothing is created');
			}
		}
		return $this->render ( 'admin/quize.html.twig', array (
				'quiz' => $quiz,
				'form'=>$form->createView()
		) );
	}
	
	/**
	 * @Route("admin/quizquestion/all/{eid}", name="admin_quizquestion_all")
	 */
	public function quizquestionallAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$quiz = $this->em()->getRepository('AppBundle:Quiz')->find($eid);
		//$quizs = array();
		$questions = $this->em()->getRepository('AppBundle:Question')->getQuestionsAll();
		foreach ($quiz->getQuizquestions() as $qq) {
			foreach ($questions as $question) {
				if($qq->getQuestion()->getId()==$question->getId()){
		            $question->quizin=1;
		            break 1; //go to next entity $qq
		        }
			}
		}
		
		$question = $this->getQuestionForm();
		$form = $this->createForm ( QuestionType::class, $question );
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$index = 1;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") $question->removeAnswer($answer);
					$index++;
				}
				$this->em()->persist ( $question );
				$this->em()->flush ();
				$this->get('session')->set('admin_quizquestion_all_ok', 'Question is OK');
				return $this->redirect ( $this->generateUrl ( 'admin_quizquestion_all',array(
						'eid' => $quiz->getId(),
						'_'=>'y'
				)) );
			}else{
				$this->get('session')->set('admin_quizquestion_all_ok', 'Nothing is created');
			}
		}
		
		return $this->render ( 'admin/quiz.question.all.html.twig', array (
				'quiz' => $quiz,
				'questions' => $questions,
				'form'=>$form->createView()
		) );
	}
	
	
	/**
	 * @Route("admin/quezquestion/aj", name="admin_quizquestion_aj")
	 * 
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function quizquestionAjAction(Request $request)
	{
		//*************RIGHTS************************************
		//???
		//*************RIGHTS************************************
	
		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}
	
		$id1 = $request->query->get('id1');
		$id2 = $request->query->get('id2');
		$act = $request->query->get('act');
		//init current user
		//
		$quiz=null;$question=null;
		$quiz = $this->em()->getRepository ( 'AppBundle:Quiz' )->find ( $id1 );
		$question = $this->em()->getRepository ( 'AppBundle:Question' )->find ( $id2 );
		if (!$quiz) throw $this->createNotFoundException ( 'Quiz is not found, id='. $id1 );
		if (!$question) throw $this->createNotFoundException ( 'Question is not found, id='. $id2 );
		if ($act=="add"){
			$qq = new QuizQuestion($quiz,$question);
			$this->em()->persist($qq);
			$this->em()->flush();
		}elseif ($act=="rem"){
			$qqs = $quiz->getQuizquestions();
			foreach ($qqs as $qq) {
				if ($qq->getQuestion()->getId()==$id2) {
					//$quiz->removeQuizquestion($qq);
					$this->em()->remove($qq);
				}
			}
			//$this->em()->persist($qq);
			$this->em()->flush();
		}
	
		return new JsonResponse([]);
	}
	
	
	/**
	 * @Route("admin/quizquestion/new", name="admin_quizquestion_new")
	 */
	public function quizquestionnewAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		//$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page, ADMINs only!');
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
	
		$questions = $this->em()->getRepository('AppBundle:Question')->getQuestionsAll();
		//$questions = array();
	
		$question = $this->getQuestionForm();
	
	
		$form = $this->createForm ( QuestionType::class, $question );
		$form2 = clone $form;
			
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$index = 1;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") $question->removeAnswer($answer);
					$index++;
				}
	
				$this->em()->persist ( $question );
				$this->em()->flush ();
				$this->get('session')->set('admin_question_ok', 'Question is OK');
				$form = $form2;
			}else{
				$this->get('session')->set('admin_question_ok', 'Nothing is created');
			}
		}
		$questions = $this->em()->getRepository('AppBundle:Question')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/quiz.question.new.html.twig', array (
				'questions' => $questions,
				'form'=>$form->createView()
		) );
	}
}