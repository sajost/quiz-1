<?php
// src/AppBundle/Controller/AdminController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\QuestionCat;
use AppBundle\Entity\QuestionTag;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\QuizCat;
use AppBundle\Entity\QuizQuestion;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRole;
use AppBundle\Form\QuestionCatType;
use AppBundle\Form\QuestionTagType;
use AppBundle\Form\QuestionType;
use AppBundle\Form\QuizType;
use AppBundle\Form\UserEType;
use AppBundle\Form\UserRoleType;
use AppBundle\Form\UserType;
use AppBundle\Utils\Ses;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Form\QuizQuestionType;

class AdminController extends QController {

	/**
	 * @Route("admin/", name="admin")
	 * @Security("has_role('ROLE_ADMIN')")
	 * yes
	 */
	public function indexAction(Request $request) {
		//+++++++++++++++++++++++ @Security ADMINS ONLY++++++++++++++++++++++++++++++++++
		$quizs = $this->em()->getRepository('AppBundle:Quiz')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/quiz.html.twig', array (
				'quizs' => $quizs,
		) );
// 		return $this->render ( 'admin/index.html.twig', array (
// 				'y' => 'y'
// 		) );
	}

	/**
	 * @Route("uuu", name="admin_uuu")
	 * @Method({"GET"})
	 * @Template("admin/uuu.html.twig")
	 * TODO -block it by production server
	 */
	public function uuuAction(Request $request) {
		//+++++++++++++++++++++++ @Security ADMINS ONLY++++++++++++++++++++++++++++++++++
		$uuu=$this->r()->findAll();
		return array('uuu' => $uuu);
	}





	/**
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function globalAction(Request $request) {
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
	 * @Route("admin/userrole", name="admin_userrole")
	 * @Security("has_role('ROLE_SUPER')")
	 */
	public function userroleAction(Request $request) {
		//*************RIGHTS************************************

		$userroles = $this->em()->getRepository('AppBundle:UserRole')->findBy(array('status'=>'1'), array('role' => 'ASC'));

		return $this->render ( 'admin/user.role.html.twig', array (
				'userroles' => $userroles,
		) );
	}


	/**
	 * @Route("admin/userrolene/{eid}", name="admin_userrole_ne")
	 * @Security("has_role('ROLE_SUPER')")
	 */
	public function userroleneAction(Request $request,$eid=null) {
		//*************RIGHTS************************************

		if ($eid==null || $eid==0){
			$userrole = new UserRole();
		}else{
			$userrole = $this->em()->getRepository('AppBundle:UserRole')->find($eid);
		}
		if ($userrole==null) $userrole = new UserRole();

		$form = $this->createForm ( UserRoleType::class, $userrole );
		$form2 = clone $form;

		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//var_dump($p);
				$this->em()->persist ( $userrole );
				$this->em()->flush ();
				$this->get('session')->set('admin_userrole_ok', 'UserRole is OK');
				$userrole = $this->em()->getRepository('AppBundle:UserRole')->find($eid);
				$form = $form2;//disable a message for resend data by page-refresh
			}else{
				$this->get('session')->set('admin_userrole_ok', 'Nothing is created');
			}
		}

		return $this->render ( 'admin/user.role.ne.html.twig', array (
				'userrole' => $userrole,
				'form'=>$form->createView()
		) );
	}




	/**
	 * @Route("admin/user", name="admin_user")
	 * @Security("has_role('ROLE_SUPER')")
	 */
	public function userAction(Request $request) {
		//*************RIGHTS************************************

		$users = $this->em()->getRepository('AppBundle:User')->findBy(array(), array('username' => 'ASC'));

		return $this->render ( 'admin/user.html.twig', array (
				'users' => $users,
		) );
	}


	/**
	 * @Route("admin/userne/{eid}", name="admin_user_ne")
	 * @Security("has_role('ROLE_SUPER')")
	 */
	public function userneAction(Request $request,$eid=null) {
		//*************RIGHTS************************************

		if ($eid==null || $eid==0){
			$user = new User();
		}else{
			$user = $this->em()->getRepository('AppBundle:User')->find($eid);
		}
		if ($user==null || !$user->getId()) {
			$user = new User();
			$form = $this->createForm ( UserType::class, $user );
		}else{
			$form = $this->createForm ( UserEType::class, $user );
		}


		//$form2 = clone $form;

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
					//dump($avatar);
					Ses::imgResize ( Ses::getUpDirTmp ($user->getUsername()) . "/" . $p ['avatar'], Ses::getUpDirTmp ($user->getUsername()) . "/" . $avatar, $img_constraint );
					$user->setAvatar($avatar);
				}

				//special case for super admins
				if($user->getUsername()=="admin" || $user->getUsername()=="support" || $user->getUsername()=="service"){
					//$user->addUserRole('ROLE_ADMIN');
					//$ur = $this->r('UserRole')->findOneBy(array('role'=>'ROLE_SUPER'));
					$ur = $this->r('UserRole')->findOneBy(array('role'=>'ROLE_SUPER'));
					$user->addUserRole($ur);
				}

				$this->em()->persist ( $user );
				$this->em()->flush ();
				$this->get('session')->set('admin_user_ok', 'User is OK');
				//$user = $this->em()->getRepository('AppBundle:User')->find($user->getId());
				//$form = $form2;//disable a message for resend data by page-refresh
				return $this->redirect ( $this->generateUrl ( 'admin_user',array(
						'_'=>'y'
				)) );
			}else{
				$this->get('session')->set('admin_user_ok', 'Nothing is created');
			}
		}

		return $this->render ( 'admin/user.ne.html.twig', array (
				'user' => $user,
				'form'=>$form->createView()
		) );
	}

	/**
	 * @Route("admin/useroff/{eid}", name="admin_user_off", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function useroffAction(Request $request, $eid=null) {
		//*************RIGHTS************************************

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_user_ok', 'Nothing is deleted');
		}else{
			$user = $this->em()->getRepository('AppBundle:User')->find($eid);
		}
		if ($user==null) {
			$this->get('session')->set('admin_user_ok', 'Nothing is deleted');
		}else{
			$user->setStatus(0);
			$this->em()->persist($user);
			$this->em()->flush ();
			$this->get('session')->set('admin_user_ok', 'User deactivation is OK');
		}

		return $this->redirect ( $this->generateUrl ( 'admin_user') );
	}

	/**
	 * @Route("admin/useron/{eid}", name="admin_user_on", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function useronAction(Request $request, $eid=null) {
		//*************RIGHTS************************************

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_user_ok', 'Nothing is deleted');
		}else{
			$user = $this->em()->getRepository('AppBundle:User')->find($eid);
		}
		if ($user==null) {
			$this->get('session')->set('admin_user_ok', 'Nothing is deleted');
		}else{
			$user->setStatus(1);
			$this->em()->persist($user);
			$this->em()->flush ();
			$this->get('session')->set('admin_user_ok', 'User activation is OK');
		}

		return $this->redirect ( $this->generateUrl ( 'admin_user') );
	}

	/**
	 *	userAvatarAction
	 * @Route("aj/user/avatar", name="aj_user_avatar")
	 *
	 * @param
	 *
	 */
	public function userAvatarAction(Request $request) {
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


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/


	/**
	 * @Route("admin/questioncat", name="admin_question_cat")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questioncatAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsAll();

		return $this->render ( 'admin/question.cat.html.twig', array (
				'questioncats' => $questioncats,
		) );
	}

	/**
	 * @Route("admin/questioncat/{eid}", name="admin_question_cat_ne")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questioncatneAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$title_old = '';
		if ($eid==null || $eid==0){
			$questioncat = new QuestionCat();
		}else{
			$questioncat = $this->em()->getRepository('AppBundle:QuestionCat')->find($eid);
			$title_old = $questioncat->getTitle();
		}
		if ($questioncat==null) $questioncat = new QuestionCat();


		$form = $this->createForm ( QuestionCatType::class, $questioncat );
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit questioncat
				if ($questioncat->getQuizcat()==null){
					$quizcat = $this->em()->getRepository('AppBundle:QuizCat')->getQuizCatByCat(strtolower($title_old));
					//dump($quizcat);
					if (true === is_null ($quizcat)){
						$quizcat = new QuizCat();
						$quizcat->setTitle($questioncat->getTitle());
						$quizcat->setQuestioncat($questioncat);
						$questioncat->setQuizcat($quizcat);
						$this->em()->persist ( $quizcat );
					}else{
						$questioncat->setQuizcat($quizcat);
						$quizcat->setQuestioncat($questioncat);
					}
				}else{
					$questioncat->getQuizcat()->setTitle($questioncat->getTitle());
					$questioncat->getQuizcat()->setQuestioncat($questioncat);
				}
				$this->em()->persist ( $questioncat );
				$this->em()->flush();
				$this->get('session')->set('admin_questioncate_ok', 'QuestionCat+QuizCat is OK');
				if ($form->get('save_add')->isClicked()) {
					return $this->redirect($this->generateUrl('admin_question_ne',array('eid'=>'+')));
				}
				return $this->redirect ( $this->generateUrl ( 'admin_question_cat') );
				//$questioncats = $this->em()->getRepository('AppBundle:QuestionCat')->getQuestionCatsByCountry($coid);
			}
		}

		return $this->render ( 'admin/question.cat.ne.html.twig', array (
				'questioncat' => $questioncat,
				'form'=>$form->createView(),
		) );
	}

	/**
	 * @Route("admin/questioncat/-/{eid}", name="admin_question_cat_d", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questioncatdAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_question_cat_ok', 'Nothing is deleted');
		}else{
			$questioncat = $this->em()->getRepository('AppBundle:QuestionCat')->find($eid);
		}
		if ($questioncat==null) {
			$this->get('session')->set('admin_question_cat_ok', 'Nothing is deleted');
		}else{
			$quizcat = $questioncat->getQuizcat();
			$questioncat->setQuizcat(null);
			$quizcat->setQuestioncat(null);
			$this->em()->flush ();
			$this->em()->remove($questioncat);
			$this->em()->remove($quizcat);
			$this->em()->flush ();
			$this->get('session')->set('admin_question_cat_ok', 'Question-Cat deletion is OK');
		}

		return $this->redirect ( $this->generateUrl ( 'admin_question_cat'));

	}



	/**
	 * @Route("admin/questiontag", name="admin_question_tag")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questiontagAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$questiontags = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagsAll();
		//$questiontags = array();

		return $this->render ( 'admin/question.tag.html.twig', array (
				'questiontags' => $questiontags,
		) );
	}

	/**
	 * @Route("admin/questiontag/{eid}", name="admin_question_tag_ne")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questiontagneAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$questiontag = new QuestionTag();
		}else{
			$questiontag = $this->em()->getRepository('AppBundle:QuestionTag')->find($eid);
		}
		if ($questiontag==null) $questiontag = new QuestionTag();

		$form = $this->createForm ( QuestionTagType::class, $questiontag );
		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				//------------------edit questiontag
				//dump($questiontag);
				$this->em()->persist ( $questiontag );
				$this->em()->flush ();
				$this->get('session')->set('admin_question_tag_ok', 'QuestionTag is OK');
				return $this->redirect ( $this->generateUrl ( 'admin_question_tag') );
				//$questiontags = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagsByCountry($coid);
			}
		}

		return $this->render ( 'admin/question.tag.ne.html.twig', array (
				'questiontag' => $questiontag,
				'form'=>$form->createView()
		) );
	}


	/**
	 * @Route("admin/questiontag/-/{eid}", name="admin_question_tag_d", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questiontagdAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_question_tag_ok', 'Nothing is deleted');
		}else{
			$questiontag = $this->em()->getRepository('AppBundle:QuestionTag')->find($eid);
		}
		if ($questiontag==null) {
			$this->get('session')->set('admin_question_tag_ok', 'Nothing is deleted');
		}else{
			$this->em()->remove($questiontag);
			$this->em()->flush ();
			$this->get('session')->set('admin_question_tag_ok', 'Question-Tag deletion is OK');
		}


		return $this->redirect ( $this->generateUrl ( 'admin_question_tag'));

		$questiontags = $this->em()->getRepository('AppBundle:QuestionTag')->getQuestionTagsAll();
		return $this->render ( 'admin/question.tag.html.twig', array (
				'questiontags' => $questiontags,
		) );
	}


	/**
	 * @Route("admin/question", name="admin_question")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questionAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$questions = $this->em()->getRepository('AppBundle:Question')->findBy(array(), array('title' => 'ASC'));
		$ql_active = $this->em()->getRepository('AppBundle:Question')->getQuestionsStatusCount();
		$quizs = $this->em()->getRepository('AppBundle:Quiz')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/question.html.twig', array (
				'quizs' => $quizs,
				'questions' => $questions,
				'ql_active' => $ql_active,
		) );
	}

	/**
	 * @Route("admin/question/{eid}", name="admin_question_ne")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questionneAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++
		if ($eid==null || $eid==0){$eid=0;}
		$question = $this->getQuestionForm($eid);
		//dump("aaa ".$question->getAnswers()->count());

		$form = $this->createForm ( QuestionType::class, $question, array('em' => $this->em()) );
		//$form->get('answercount')->setData($answercount);
		//$form2 = clone $form;

		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			//dump($question->cats);
			if ($form->isValid ()) {
				//dump($question->cats);
				$index = 1;$itrue=0;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") {
						$question->removeAnswer($answer);
					}else{
						if ($answer->getStatus()==1) $itrue++;
					}
					$index++;
				}
				//add count of true answers
				$question->setTruecount($itrue);
				
// 				if ($question->cat!=""){
// 					$c = new QuestionCat();
// 					$c->setTitle($question->cat);
// 					$c2 = new QuizCat();
// 					$c2->setTitle($question->cat);
// 					$c->setQuizcat($c2);
// 					$this->em()->persist ( $c );
// 					$this->em()->persist ( $c2 );
// 					$question->addCat($c);
// 				}

// 				if ($question->tag!=""){
// 					$c = new QuestionTag();
// 					$c->setTitle($question->tag);
// 					$this->em()->persist ( $c );
// 					$question->addTag($c);
// 				}

				$p = $request->get ( 'question' ); 
				//var_dump($p);
				// crop & resize the avatar image, if image is selected
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if (!Ses::imgCrop (
							Ses::getUpDirTmp ( 'q-'.$question->getId() ) . "/" . $p ['avatar'],
							$p ['avatar_x'],
							$p ['avatar_y'],
							$p ['avatar_w'],
							$p ['avatar_h'] )) {
								$this->get('session')->set ( "admin_question", 'The Image-Crop-Function return false, check logs' );
							}

							$img_constraint = array (
									'constraint' => array (
											'width' => 100,
											'height' => 100
									)
							);
							$avatar=Ses::after('tmp_', $p ['avatar']);
							//dump($avatar);
							Ses::imgResize ( Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $p ['avatar'], Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $avatar, $img_constraint );
							$question->setAvatar($avatar);
				}

				$this->em()->persist ( $question );
				$this->em()->flush ();
				//move new created image with id=0 to its real id-folder,  if image is selected
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if ($eid==0){
						if (!is_dir(Ses::getUpDirTmp ('q-'.$question->getId()))) {
							mkdir(Ses::getUpDirTmp ('q-'.$question->getId()));
						}
						rename(Ses::getUpDirTmp ('q-0') . "/" . $p ['avatar'], Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $p ['avatar']);
						rename(Ses::getUpDirTmp ('q-0') . "/" . $avatar, Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $avatar);
					}
				}
				$this->get('session')->set('admin_question', 'Question is OK');
				//dump($question);
				//dump($eid);

				if ($form->get('save_add')->isClicked()) {
					return $this->redirect ( $this->generateUrl ( 'admin_question_ne',array(
							'eid'=>'+'
					)) );
				}
				//redirect for full data submit
				return $this->redirect ( $this->generateUrl ( 'admin_question',array(
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

		return $this->render ( 'admin/question.ne.html.twig', array (
				'question' => $question,
				'form'=>$form->createView(),
				'answercount'=>$question->answercount
		) );
	}

	/**
	 * @Route("admin/question/-/{eid}", name="admin_question_d", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function questiondAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_question', 'Nothing is deleted');
		}else{
			$question = $this->em()->getRepository('AppBundle:Question')->find($eid);
		}
		if ($question==null) {
			$this->get('session')->set('admin_question', 'Nothing is deleted');
		}else{
			$this->em()->remove($question);
			$this->em()->flush ();
			$this->get('session')->set('admin_question', 'Question- deletion is OK');
		}

		return $this->redirect ( $this->generateUrl ( 'admin_question'));

		$questions = $this->em()->getRepository('AppBundle:Question')->getQuestionsAll();
		return $this->render ( 'admin/question.html.twig', array (
				'questions' => $questions,
		) );
	}


	/**
	 *	questionAvatarAction
	 * @Route("aj/question/avatar", name="aj_question_avatar")
	 *
	 * @param
	 *
	 */
	public function questionAvatarAction(Request $request) {
		$ret1 = null;
		if (is_object(parent::upFotoAction1($ret1))) return $ret1;

		//TODO check username if already exist, then return validation-message, check only by new users

		$fd = $request->files->get('question');
		$un = 'q-'.$request->get('question_id');
		$ffoto = $fd['avatar_f'];//$request->files->get('user[avatar_f]', array(), true);
		//var_dump($request->get('question_id'));
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
	 * @param unknown $eid
	 * @return \AppBundle\Entity\Question|object
	 */
	private function getQuestionForm($eid=null){
		if ($eid==null){
			$question = new Question();
			$question->setId(0);
			$question->setUser($this->u());
		}else{
			$question = $this->em()->getRepository('AppBundle:Question')->find($eid);
		}
		if ($question==null) {
			$question = new Question();
			$question->setId(0);
			$question->setUser($this->u());
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
			$answercount=2;
			for ($x = 1; $x <= 6; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<4){
			$answercount=3;
			for ($x = 1; $x <= 5; $x++) {
				//answer - 1
				$answer1 = new Answer();
				$answer1->setTitle('');
				$question->addAnswer($answer1);
			}
		}else if ($question->getAnswers()->count()<5){
			$answercount=4;
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
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function quizAction(Request $request) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$quizs = $this->em()->getRepository('AppBundle:Quiz')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/quiz.html.twig', array (
				'quizs' => $quizs,
		) );
	}

	/**
	 * @Route("admin/quiz/{eid}", name="admin_quiz_ne")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function quizneAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$quiz = new Quiz();
			$quiz->setUser($this->u());
		}else{
			$quiz = $this->em()->getRepository('AppBundle:Quiz')->find($eid);
		}
		if ($quiz==null) {
			$quiz = new Quiz();
			$quiz->setUser($this->u());
		}
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
				return $this->redirect ( $this->generateUrl ( 'admin_quiz_ne',array(
						'eid' => $quiz->getId(),
						'_'=>'y'
				)) );
			}else{
				$this->get('session')->set('admin_quiz_ok', 'Nothing is created');
			}
		}
		return $this->render ( 'admin/quiz.ne.html.twig', array (
				'quiz' => $quiz,
				'form'=>$form->createView()
		) );
	}


	/**
	 * @Route("admin/quiz/-/{eid}", name="admin_quiz_d", requirements={"eid": "\d+"})
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function quizdAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		if ($eid==null || $eid==0){
			$this->get('session')->set('admin_quiz_ok', 'Nothing is deleted');
		}else{
			$quiz = $this->em()->getRepository('AppBundle:Quiz')->find($eid);
			$this->em()->remove($quiz);
			$this->em()->flush ();
			$this->get('session')->set('admin_quiz_ok', 'Quiz deletion is OK');
		}
		if ($quiz==null) $this->get('session')->set('admin_quiz_ok', 'Nothing is deleted');


		$quizs = $this->em()->getRepository('AppBundle:Quiz')->findBy(array(), array('title' => 'ASC'));
		return $this->render ( 'admin/quiz.html.twig', array (
				'quizs' => $quizs,
		) );
	}

	/**
	 * @Route("admin/quizquestion/all/{eid}", name="admin_quizquestion_all")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function quizquestionallAction(Request $request,$eid=null) {
		//+++++++++++++++++++++++ADMINS ONLY++++++++++++++++++++++++++++++++++

		$quiz = $this->em()->getRepository('AppBundle:Quiz')->find($eid);

// 		$titles = array();
		$questions = array();
		foreach ($quiz->getCats() as $c){
			//dump($c->getQuestioncat()->getQuestions());
			if (!is_null($c->getQuestioncat()->getQuestions())) {
				$questions = $questions + $c->getQuestioncat()->getQuestions()->toArray();
				//$questions = array_unique (array_merge ($questions, $c->getQuestioncat()->getQuestions()->toArray()));
			}
// 			$titles[]=strtolower($c->getTitle());
		}

// 		$questions = $this->em()->getRepository('AppBundle:Question')
// 		->createQueryBuilder('q')
// 		->select(array('q', 'c'))
// 		->innerJoin('q.cats', 'c')
// 		->andWhere('lower(c.title) IN (:titles)')
// 		->setParameter('titles', $titles)
// 		->getQuery()->getResult();

		//$questions = $this->em()->getRepository('AppBundle:Question')->getQuestionsAll();
		foreach ($quiz->getQuizquestions() as $qq) {
			foreach ($questions as $question) {
				if($qq->getQuestion()->getId()==$question->getId()){
		            $question->quizin=1;
		            break 1; //go to next entity $qq
		        }
			}
		}

		$question = $this->getQuestionForm();
		foreach ($quiz->getCats() as $cat) {
			$question->addCat($cat->getQuestioncat());
		}
		$form = $this->createForm ( QuizQuestionType::class, $question );

		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$index = 1;$itrue=0;
				foreach ($question->getAnswers() as $answer){
					if ($index > $question->answercount || $answer->getTitle()=="") {
						$question->removeAnswer($answer);
					}else{
						if ($answer->getStatus()==1) $itrue++;
					}
					$index++;
				}
				//add count of true answers
				$question->setTruecount($itrue);
				//add new tag if neccessary
// 				if ($question->tag!=""){
// 					$c = new QuestionTag();
// 					$c->setTitle($question->tag);
// 					$this->em()->persist ( $c );
// 					$question->addTag($c);
// 				}
				
				$p = $request->get ( 'question' );
				//var_dump($p);
				// crop & resize the avatar image
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if (!Ses::imgCrop (
							Ses::getUpDirTmp ( 'q-'.$question->getId() ) . "/" . $p ['avatar'],
							$p ['avatar_x'],
							$p ['avatar_y'],
							$p ['avatar_w'],
							$p ['avatar_h'] )) {
								$this->get('session')->set ( "admin_question", 'The Image-Crop-Function return false, check logs' );
							}
				
							$img_constraint = array (
									'constraint' => array (
											'width' => 100,
											'height' => 100
									)
							);
							$avatar=Ses::after('tmp_', $p ['avatar']);
							//dump($avatar);
							Ses::imgResize ( Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $p ['avatar'], Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $avatar, $img_constraint );
							$question->setAvatar($avatar);
				}
				
				$this->em()->persist ( $question );
				$this->em()->flush ();
				//move new created image with id=0 to its real id-folder,  if image is selected
				if ($p ['avatar_x'] != null && $p ['avatar_x'] != '') {
					if (!is_dir(Ses::getUpDirTmp ('q-'.$question->getId()))) {
						mkdir(Ses::getUpDirTmp ('q-'.$question->getId()));
					}
					rename(Ses::getUpDirTmp ('q-0') . "/" . $p ['avatar'], Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $p ['avatar']);
					rename(Ses::getUpDirTmp ('q-0') . "/" . $avatar, Ses::getUpDirTmp ('q-'.$question->getId()) . "/" . $avatar);
				}
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
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
	        throw $this->createAccessDeniedException();
	    }
		//*************RIGHTS************************************

		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}

		$id1 = $request->query->get('id1');
		$id2 = $request->query->get('id2');
		$act = $request->query->get('act');
		//init current user
		//TODO check if Quiz-Cat is equal to Question-Cat, if not then Exception
		$quiz=null;$question=null;
		$quiz = $this->em()->getRepository ( 'AppBundle:Quiz' )->find ( $id1 );
		if (!$quiz) throw $this->createNotFoundException ( 'Quiz is not found, id='. $id1 );
		if ($act=="add"){
			$question = $this->em()->getRepository ( 'AppBundle:Question' )->find ( $id2 );
			if (!$question) throw $this->createNotFoundException ( 'Question is not found, id='. $id2 );
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
		}elseif ($act=="addall"){
			$ids = explode(',', $id2);
			foreach ($ids as $id) {
				$question = $this->em()->getRepository ( 'AppBundle:Question' )->find ( $id );
				if (!$question) throw $this->createNotFoundException ( 'Question is not found, id='. $id );
				$qq = new QuizQuestion($quiz,$question);
				$this->em()->persist($qq);
			}
			$this->em()->flush();
		}elseif ($act=="remall"){
			$ids = explode(',', $id2);
			foreach ($ids as $id) {
				$qqs = $quiz->getQuizquestions();
				foreach ($qqs as $qq) {
					if ($qq->getQuestion()->getId()==$id) {
						//$quiz->removeQuizquestion($qq);
						$this->em()->remove($qq);
					}
				}
			}
			//$this->em()->persist($qq);
			$this->em()->flush();
		}


		return new JsonResponse([]);
	}
}

