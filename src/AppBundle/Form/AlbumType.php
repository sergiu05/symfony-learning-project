<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormInterface;

class AlbumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('price', MoneyType::class)            
            ->add('description')   
            ->add('file')         
            ->add('genre', EntityType::class, [
            	'class' => 'AppBundle:Genre',
            	'query_builder' => function(EntityRepository $er) {
            		return $er->createQueryBuilder('g')
            					->orderBy('g.name', 'ASC');
            	},
            	'choice_label' => 'name',
            	'placeholder' => 'Choose a genre'
            ])
            ->add('artist', EntityType::class, [
            	'class' => 'AppBundle:Artist',
            	'query_builder' => function(EntityRepository $er) {
            		return $er->createQueryBuilder('a')
            					->orderBy('a.name', 'ASC');
            	},
            	'choice_label' => 'name',
            	'placeholder' => 'Choose an artist'
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Album',
            'validation_groups' => function (FormInterface $form) {
            	$data = $form->getData();
            	/* different validation groups for new / existing entities (upload file is required for new entities; optional for updating existing entities) */
            	if ($data->getId()) {
            		return array('Default');
            	}
            	return array('Default', 'Create');
            }
        ));
    }
}
