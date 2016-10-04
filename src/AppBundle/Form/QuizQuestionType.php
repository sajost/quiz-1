<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class QuizQuestionType extends AbstractType{
	
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
    	
        $builder
       		->add('info',TextType::class, array('required'=>true))
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
            'data_class' => 'AppBundle\Entity\QuizQuestion'
        ));
    }
    
    public function getName() {
    	return 'quizquestion';
    }

    /**
     * @return string
     */
}
