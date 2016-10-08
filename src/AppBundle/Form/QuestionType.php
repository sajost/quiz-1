<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType{
	
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
    	$status = [
    			"geprüft" => "1",
    			"nicht geprüft" => "0",
    			"pausiert" => "2",
    			"aktiv aktualisiert" => "3",
    	];
    	$yn = [
    			"Ja" => "1",
    			"Nein" => "0"
    	];
        $builder
       		->add('title',TextareaType::class, array('required'=>true))
       		->add('status', ChoiceType::class, array(
       				'choices' => $status,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
       		))
       		->add('truecount', ChoiceType::class, array(
       				'choices' => $yn,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
       		))
       		->add('published',DateTimeType::class, array(
       				'widget' => 'single_text',
       				'data' => new \DateTime("now"),
       				'format' => 'dd.MM.yyyy hh:mm:ss',
       				'html5' => true,
       				'required'=>true
       		))
       		->add('source',TextType::class, array('required'=>true))
       		->add('difficulty', RangeType::class, array (
				'required' => false,
       			'attr' => array(
       					'min' => 0,
       					'max' => 10
       			)
			))
            ->add('cats', EntityType::class, array(
			    // query choices from this entity
			    'class' => 'AppBundle:QuestionCat',
			    'choice_label' => 'title',
			    'multiple' => true,
			    'expanded' => false,
			))
			->add('tags', EntityType::class, array(
					// query choices from this entity
				'class' => 'AppBundle:QuestionTag',
				'choice_label' => 'title',
				'multiple' => true,
				'expanded' => false,
			))
			->add('answers', CollectionType::class, array(
					'entry_type' => AnswerType::class,
					'required'=>true
			))
			->add('answercount',IntegerType::class, array(
					'required'=>false
			))
			;
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
            'data_class' => 'AppBundle\Entity\Question'
        ));
    }
    
    public function getName() {
    	return 'question';
    }

    /**
     * @return string
     */
}
