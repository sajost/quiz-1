<?php
// src/AppBundle/Entity/QuestionCat.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\QuestionCat")
 * @ORM\Table(name="questioncat")
 * @ORM\HasLifecycleCallbacks
 */
class QuestionCat
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
     * @ORM\Column(type="smallint", nullable=true, options={"default":0})
     */
    public $status = 1;
    
    
    /**
     * @ORM\ManyToMany(targetEntity="Question", mappedBy="cats")
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
    
    public $typ = 'questioncat';
    
    
    public function __construct()
    {
    	$this->questions = new \Doctrine\Common\Collections\ArrayCollection();
    	
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
