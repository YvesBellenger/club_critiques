<?php

namespace AppBundle\Form;

use AppBundle\Repository\ContentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class LobbyType extends  AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('content','entity', array(
                'class'=>'AppBundle:Content',
                'label'=>'Contenu',
                'query_builder' => function(ContentRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.status = :status')
                        ->setParameter('status', 1)
                        ->orderBy('c.title', 'ASC');
                }
            ))
            ->add('date_start', DateTimeType::class, array('label' => 'Date de début', 'attr' => array('class' => 'form-group row')))
            ->add('date_end', DateTimeType::class, array('label' => 'Date de fin'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Lobby'
        ));
    }
}