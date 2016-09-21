<?php
// src/AppBundle/Entity/QuestionTag.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionTag")
 * @ORM\Table(name="questiontag")
 * @ORM\HasLifecycleCallbacks
 */
class QuestionTag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    
    /**
     * @ORM\Column(type="string", length=20)
     */
    public $title;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Question", mappedBy="tags")
     */
    public $questions;
    

    /**
     * @ORM\Column(type="datetime")
     */
    public $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    public $updated;
    
    public $typ = 'questiontag';
    
    public function __construct()
    {
    	$this->setCreated(new \DateTime());
    	$this->setUpdated(new \DateTime());
    }
    
    
    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
    	$this->setUpdated(new \DateTime());
    }

    public function addQuestion(Question $question)
    {
    	$this->questions[] = $question;
    }

}