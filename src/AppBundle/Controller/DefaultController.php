<?php
// src/AppBundle/Controller/PageController.php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends QController {

	/**
	 * Main-start of the Webportal
	 * @Route("/", name="home")
	 * @Template("default/index.html.twig")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction(Request $request) {
		//TODO - what Quiz need to find
		$quiz_last = $this->r('Quiz')->findOneBy(array(),array('created' => 'DESC'));
		$this->get('session')->set('quiz_id', $quiz_last->getId());
		$this->get('session')->set('question_ids_done', array());
		$this->get('session')->set('quiz_score', 0);
		return array('quiz_last' => $quiz_last);
	}


	/**
	 * @Route("start0", name="start0")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function start0Action(Request $request) {
		if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
			//  authenticated (NON anonymous)
			//$this->get('session')->set('quizz', $user['user_id']);
			return $this->render ( 'default/counter.html.twig', array (
			) );
		}else{
			$session = $request->getSession ();
			$authenticationUtils = $this->get('security.authentication_utils');
			// get the login error if there is one
			$error = $authenticationUtils->getLastAuthenticationError();
			// last username entered by the user
			$lastUsername = $authenticationUtils->getLastUsername();
			$activate = $session->get ( "me_acktivate_ok" );
			return $this->render ( 'security/login0.html.twig', array (
					'last_username' => $lastUsername,
					'error' => $error,
					'activate' => $activate,
			) );
		}
	}

	/**
	 * @Route("start1", name="start1")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function start1Action(Request $request) {
		return $this->render ( 'default/counter.html.twig', array (
		) );
	}


	/**
	 * @Route("questionx", name="questionx")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function questionXAction(Request $request) {
		//here goes the logic
		$quiz_id = $this->get('session')->get('quiz_id');
		$question_ids_done = $this->get('session')->get('question_ids_done');
		$quiz = $this->r('Quiz')->findOneBy(array('id'=>$quiz_id));
		//if all questions are done, then hiscore
		if($quiz->getQuizquestions()->count()==count($question_ids_done)){
			return $this->redirect ( $this->generateUrl ( 'highscore') );
		}
		$question=null;
		foreach ($quiz->getQuizquestions() as $qq){
			if (!in_array($qq->getQuestion()->getId(), $question_ids_done)) {//if NOT question_id in question_ids_done
				$question=$qq->getQuestion();
				break;
			}
		}
		if ($question==null){
			throw $this->createNotFoundException ( 'The Logic of the next question-id is out of array range: '.join($question_ids_done));
		}

		$choices = array();
		foreach ($question->getAnswers() as $answer){
			array_push($choices, array($answer->getTitle()=>$answer->getId()));
		}
		$dd = array('x' => 'y');
		$form = $this->createFormBuilder($dd)
		->add('answers', ChoiceType::class, array (
				'choices' => $choices,
				'multiple' => false,
				'expanded' => true,
				'required' => true
		))
		->add('save', SubmitType::class, array('label' => 'Weiter'))
		->getForm();

		if ($request->isMethod ( 'POST' )) {
			$form->handleRequest ( $request );
			if ($form->isValid ()) {
				$answer_ids = $form->get('answers')->getData();
				if(!is_array($answer_ids)){
					$answer_ids = (array)$answer_ids;
				}
				foreach($answer_ids as $answer_id) {
					$answer=$this->r('Answer')->findOneBy(array('id'=>$answer_id));
					if ($answer) {
						if ($answer->getStatus()==1){//user has anwered correctly
							$quiz_score = $this->get('session')->get('quiz_score');
							$quiz_score++;//simple formula to count the user score :))))
							$this->get('session')->set('quiz_score',$quiz_score);
						}
					} else {
						// no such answer is found, then the answer is false
					}
				}
				array_push($question_ids_done,$question->getId());
				$this->get('session')->set('question_ids_done', $question_ids_done);
				return $this->redirect ( $this->generateUrl ( 'questionx') );
			}
		}

		return $this->render ( 'default/question.x.html.twig', array (
				'quiz' => $quiz,
				'question' => $question,
				'form'=>$form->createView(),
		) );
	}

	/**
	 * @Route("questionx/aj", name="questionx_aj")
	 *
	 * @param Request $request
	 * @throws NotFoundHttpException
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function questionXAjAction(Request $request)
	{
		//*************RIGHTS************************************
		//*************RIGHTS************************************

		if (! $request->isXmlHttpRequest()) {
			throw new NotFoundHttpException();
		}
		$id = $request->query->get('q');
		//here goes the logic
		$this->get('session')->set('question_ids_done_aj',$id);

		return new JsonResponse([]);
	}

	/**
	 * @Route("hs", name="highscore")
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function highscoreAction(Request $request) {
		if ($this->u()==null){
			return $this->render ( 'default/highscore.anonymous.html.twig', array (
			) );
		}else{
			return $this->render ( 'default/highscore.user.html.twig', array (
			) );
		}
	}



}
