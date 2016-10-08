<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegType extends AbstractType{
	
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
            ->add('username',TextType::class, array('attr' => array('maxlength'=>18,)))
			->add('email',EmailType::class, array('attr' => array('maxlength'=>32,)))
			->add('password', RepeatedType::class, array(
			    'type' => PasswordType::class,
			    'invalid_message' => 'The password fields must match.',
			    'required' => true,
			))  		
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
            'data_class' => 'AppBundle\Entity\User'
        ));
    }
    
    public function getBlockPrefix() {
    	return 'user';
    }

    /**
     * @return string
     */
}
