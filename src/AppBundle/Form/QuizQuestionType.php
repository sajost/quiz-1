<?php

namespace AppBundle\Form;

use AppBundle\Entity\Question;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
       		->add('questions', EntityType::class, array(
       				'class' => 'AppBundle:Question',
       				'query_builder' => function (EntityRepository $er) {
            			return $er->getQuestionsAllQB();
            		},
       				'choice_label' => 'title',
       				'multiple' => true,
       				'expanded' => true,
       		))
        ;
       		
       		$fmQuestion = function (FormInterface $form, Question $question = null) {
       			$answers = null === $question | ''==$question ? array() : $question->getAnswers();
       			$form->add('answers', EntityType::class, array(
       					'class'       => 'AppBundle:Answer',
       					'choice_label' => 'title',
       					'choices'     => $answers,
       					'multiple' => true,
       					'expanded' => true,
       			));
       		};
       		
       		
       		$builder->addEventListener(
       				FormEvents::PRE_SET_DATA,
       				function (FormEvent $event) use ($fmQuestion) {
       					// this would be your entity, i.e. Question
       					$data = $event->getData();
       					$fmQuestion($event->getForm(), $data->getQuestion());
       				}
       				);
            
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
