<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuestionCatType extends AbstractType{

	function __construct()
	{
	}

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$status = [
    			 "Aktiv" => "1",
    			"Inaktiv" => "0"
    	];
        $builder
       		->add('title',TextType::class, array('required'=>true))
       		->add('status', ChoiceType::class, array(
       				'choices' => $status,
       				'data' => "1",
       				'multiple' => false,
       				'expanded' => true,
       				'required' => true
       		))
       		->add('save', SubmitType::class)
       		->add('save_add', SubmitType::class)
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
            'data_class' => 'AppBundle\Entity\QuestionCat'
        ));
    }

    public function getName() {
    	return 'questioncat';
    }

    /**
     * @return string
     */
}
