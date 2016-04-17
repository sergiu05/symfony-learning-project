<?php 

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserUpdateStatusType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		
		$builder 
			->add('isAdmin', ChoiceType::class, array(
				'choices' => array(
					'Yes' => true, 
					'No' => false
				),
				'expanded' => true,
				'multiple' => false
			))
			->add('isActive', ChoiceType::class, array(
				'choices' => array(
					'Yes' => true,
					'No' => false
				),
				'expanded' => true,
				'multiple' => false
			));
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\User',
			'validation_groups' => array('admin_update')
		));
	}

}