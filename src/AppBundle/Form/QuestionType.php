<?php

namespace AppBundle\Form;

use AppBundle\Entity\QuestionCat;
use AppBundle\Entity\QuestionTag;
use AppBundle\Entity\QuizCat;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType{

	private $em = null;

	function __construct()
	{
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$this->em = $options['em'];

		$status = [
				"aktiv" => "1",
				"inaktiv" => "0",
		];
		$statuscheck = [
				"geprüft" => "1",
				"nicht geprüft" => "0",
		];
		$builder
			->add('title',TextareaType::class, array('required'=>true))
			->add('avatar_f',FileType::class,array('required'=>false,'mapped' => false,'attr' => array('accept'=>'.jpg,.jpe,.jpeg,.png,.gif')))
			->add('avatar',TextType::class, array('required'=>false))
			->add('avatar_x', TextType::class, array('required'=>false,'mapped' => false))
			->add('avatar_y', TextType::class, array('required'=>false,'mapped' => false))
			->add('avatar_h', TextType::class, array('required'=>false,'mapped' => false))
			->add('avatar_w', TextType::class, array('required'=>false,'mapped' => false))
			->add('status', ChoiceType::class, array(
					'choices' => $status,
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('statuscheck', ChoiceType::class, array(
					'choices' => $statuscheck,
					'multiple' => false,
					'expanded' => true,
					'required' => true
			))
			->add('published',DateTimeType::class, array(
					'widget' => 'single_text',
					//'data' => new \DateTime("now"),
					'format' => 'dd.MM.yyyy HH:mm',
					'html5' => true,
					'required'=>true
			))
			->add('source',TextType::class, array('required'=>true))
			->add('difficulty', RangeType::class, array (
				'required' => false,
				'attr' => array(
						'min' => 1,
						'max' => 10
				)
			))
			->add('cats', EntityType::class, array(
				// query choices from this entity
				'class' => 'AppBundle:QuestionCat',
				'multiple' => true,
				'expanded' => false,
				'required'=>true,
			))
			->add('tags', EntityType::class, array(
				// query choices from this entity
				'class' => 'AppBundle:QuestionTag',
				'multiple' => true,
				'expanded' => false,
				'required'=>true,
			))
			->add('answers', CollectionType::class, array(
					'entry_type' => AnswerType::class,
					'required'=>true
			))
			->add('answercount',IntegerType::class, array(
					'required'=>false
			))
			->add('save', SubmitType::class)
			->add('save_add', SubmitType::class)
			;
		;

		$builder->addEventListener(
				FormEvents::PRE_SUBMIT,
				function (FormEvent $event) {
					// this would be your entity, i.e. SportMeetup
					$data = $event->getData();
					//$viewData = $event->getForm()->getViewData();
					//cats
					$r = new ArrayCollection();
					foreach ( $data['cats'] as $cat_id ) {
						$e = $this->em->getRepository ( 'AppBundle:QuestionCat' )->find ( $cat_id );
						if (! $e) {
							$c = new QuestionCat ();
							$c->setTitle ( $cat_id );
							$c2 = new QuizCat ();
							$c2->setTitle ( $cat_id );
							$c->setQuizcat ( $c2 );
							$this->em->persist ( $c );
							$this->em->persist ( $c2 );
							$this->em->flush ();
							$r->add ( ''.$c->getId() );
						} else {
							$r->add ( $cat_id );
						}
					}
					unset($data['cats']);
					$data['cats']=$r->toArray();
					//tags
					$r = new ArrayCollection();
					foreach ( $data['tags'] as $tag_id ) {
						$e = $this->em->getRepository ( 'AppBundle:QuestionTag' )->find ( $tag_id );
						if (! $e) {
							$c = new QuestionTag ();
							$c->setTitle ( $tag_id );
							$this->em->persist ( $c );
							$this->em->flush ();
							$r->add ( ''.$c->getId() );
						} else {
							$r->add ( $tag_id );
						}
					}
					unset($data['tags']);
					$data['tags']=$r->toArray();
					$event->setData($data);
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
			'data_class' => 'AppBundle\Entity\Question',
			'em' => null
		));
	}

	public function getBlockPrefix() {
		return 'question';
	}

	/**
	 * @return string
	 */
}
