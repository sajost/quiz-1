<?php

namespace AppBundle\Form;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserActivateType extends AbstractType 
{
	private $em = null;
	
	public function __construct() {
		//$this->em = $em;
	}
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$this->em = $options['em'];
        $builder
            //->add('email','email', array('attr' => array('maxlength'=>64,'label'=>'E-Mail')))
        	->add('username',TextType::class, array(
        			'attr' => array('maxlength'=>64,),
        			'required' => true,
        	))
            ->add('token', TextType::class, array(
            		'attr' => array('maxlength'=>32,),
            		'required' => true,
            		//'constraints' => new Callback(array($this, 'validateActivate'))
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\UserActivate',
        	'em' => null
        ));
    }
    
    public function validateActivate(Event $event, ExecutionContextInterface $context)
    {
	    $form = $context->getRoot();
	    $data = $form->getData();
	    $user = $this->em->getRepository('AppBundle:User')->getUserByActivation($data['username'],$data['token']);
	    if (null === $user) {
	        $context->buildViolation('Entweder Benutzer oder Code falsch ist')->addViolation();
	    }
    }

    /**
     * @return string
     */
}
