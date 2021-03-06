<?php
// src/AppBundle/Repository/QuizQuestionRepository.php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * QuizQuestionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QuizQuestionRepository extends EntityRepository
{
	public function getQuizQuestionsAll() {
	
		$qb = $this->createQueryBuilder ( 'e' )
		->select ( 'e' )
		;
	
		return $qb->getQuery ()->getResult ();
	}
	
	public function getQuizQuestionsAllQB() {
	
		$qb = $this->createQueryBuilder ( 'e' )
		->select ( 'e' )
		;
	
		return $qb;
	}
}