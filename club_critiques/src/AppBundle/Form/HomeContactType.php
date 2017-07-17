<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomeContactType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                )
            ))
            ->add('subject', TextType::class, array('attr' => array('class' => 'form-control'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                )
            ))
            ->add('email', EmailType::class, array('attr' => array('class' => 'form-control'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                    new Email(array("message" => "Le format de l'email n'est pas valide")),
                )
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'rows' => 8),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true
        ));
    }

    public function getName()
    {
        return 'home_contact_form';
    }
}