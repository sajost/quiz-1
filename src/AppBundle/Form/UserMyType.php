<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserMyType extends AbstractType{
	
	
	function __construct()
	{
	}
	
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$sexs = [
    			 "Junge" => "1" ,
    			  "Madchen" => "0"
    			];
//     	->add('username',TextType::class, array('attr' => array('maxlength'=>16,)))
//     	->add('email',EmailType::class, array('attr' => array('maxlength'=>64,)))
//     	->add('password',PasswordType::class, array('attr' => array('maxlength'=>16,)))
        $builder
       		->add('fname',TextType::class, array('attr' => array('maxlength'=>16,'label'=>'Vorname'),'required'=>false))
       		->add('lname',TextType::class, array('attr' => array('maxlength'=>16,'label'=>'Nachname'),'required'=>false))
       		->add('tel1',TextType::class, array('attr' => array('maxlength'=>16,'label'=>'Telefon'),'required'=>false))
       		->add('tel2',TextType::class, array('attr' => array('maxlength'=>16,'label'=>'Telefon'),'required'=>false))
       		->add('tel3',TextType::class, array('attr' => array('maxlength'=>16,'label'=>'Telefon'),'required'=>false))
            ->add('dborn',BirthdayType::class, array(
			    'format' => 'dd - MMMM - yyyy',
			    'widget' => 'single_text',
			    'years' => range(date('Y'), date('Y')-70),
            	'required'=>false
			))
			->add('sex', ChoiceType::class, array(
					'choices' => $sexs,
					'data' => "1",
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
            ->add('about', TextareaType::class, array('required'=>false))
            ->add('avatar_f',FileType::class,array('required'=>false,'mapped' => false,'attr' => array('accept'=>'.jpg,.jpe,.jpeg,.png,.gif')))
            ->add('avatar',TextType::class, array('required'=>false))
            ->add('avatar_x', TextType::class, array('required'=>false,'mapped' => false))
            ->add('avatar_y', TextType::class, array('required'=>false,'mapped' => false))
            ->add('avatar_h', TextType::class, array('required'=>false,'mapped' => false))
            ->add('avatar_w', TextType::class, array('required'=>false,'mapped' => false))
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
