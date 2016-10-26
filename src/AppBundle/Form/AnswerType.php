<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class AnswerType extends AbstractType{

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
				"richtig" => "1",
				"falsch" => "0",
		];

		$builder
			   ->add('title',TextareaType::class, array('required'=>false))
			   ->add('status', ChoiceType::class, array(
					   'choices' => $status,
					   'multiple' => false,
					   'expanded' => true,
					   'required' => true,
			   ))

		;

//             $builder->get('status')->addModelTransformer(
//             		new CallbackTransformer( function ($v) { return $v==1?true:false; },function ($v) { return $v?1:0; }));
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver  $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Answer'
		));
	}

	public function getName() {
		return 'answer';
	}

	/**
	 * @return string
	 */
}
