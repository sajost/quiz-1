<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class QuizType extends AbstractType{
	
	protected $em;
	protected $mod;
	
	function __construct(EntityManager $em=null, $mod='edit')
	{
		$this->em = $em;
		$this->mod = $mod;
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$answers = [
    			"Alle" => "0",
    			"Entscheidungsfragen" => "1",
    			"Multiple-Choice mit mehreren richtigen Antworten" => "2",
    	];
    	$yn = [
    			"Ja" => "1",
    			"Nein" => "0"
    	];
        $builder
       		->add('title',TextareaType::class, array('required'=>true))
       		->add('loginrequired', ChoiceType::class, array(
       				'choices' => $yn,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
       		))
       		->add('roundperday', RangeType::class, array (
				'required' => false,
       			'attr' => array(
       					'min' => 0,
       					'max' => 10
       			)
       		))
       		->add('hsnumber',RangeType::class, array (
				'required' => false,
       			'attr' => array(
       					'min' => 0,
       					'max' => 20
       			)
       		))
       		->add('analysis',ChoiceType::class, array(
       				'choices' => $yn,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
       		))
       		->add('reward', ChoiceType::class, array(
       				'choices' => $yn,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
			))
			->add('sharehs', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('shareanalysis', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('sharereward', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('joker5050', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('jokerpause', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('jokerskip', ChoiceType::class, array(
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('difficulty', RangeType::class, array (
					'required' => false,
					'attr' => array(
							'min' => 0,
							'max' => 10
					)
			))
			->add('timelimit', IntegerType::class, array (
					'required' => false,
			))
			->add('random', ChoiceType::class, array (
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('repeat', ChoiceType::class, array (
					'choices' => $yn,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('trueanswer', ChoiceType::class, array (
					'choices' => $answers,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('cats', EntityType::class, array(
					// query choices from this entity
					'class' => 'AppBundle:QuestionCat',
					'choice_label' => 'title',
					'multiple' => true,
					'expanded' => false,
			))
//             ->add('questions', EntityType::class, array(
// 			    // query choices from this entity
// 			    'class' => 'AppBundle:QuizQuestion',
// 			    'choice_label' => 'question.title',
// 			    'multiple' => true,
// 			    'expanded' => false,
// 			))
        ;
            
//             $builder->get('hidename')->addModelTransformer(
//             		new CallbackTransformer( function ($v) { return $v==1?true:false; },function ($v) { return $v?1:0; }));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver  $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Quiz'
        ));
    }
    
    public function getName() {
    	return 'quiz';
    }

    /**
     * @return string
     */
}
