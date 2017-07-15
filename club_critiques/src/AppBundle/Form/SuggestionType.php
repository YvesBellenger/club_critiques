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

class SuggestionType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', TextType::class, array('attr' => array('class' => 'col-md-3','placeholder' =>'Livre, film...'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                )
            ))
            ->add('titre', TextType::class, array('attr' => array('class' => 'col-md-3','placeholder' =>'Harry Potter, Orange Mécanique...'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                )
            ))
            ->add('auteur', EmailType::class, array('attr' => array('class' => 'col-md-3','placeholder' =>'J.K. Rowling, Stanley Kubric..'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                    new Email(array("message" => "Le format de l'email n'est pas valide")),
                )
            ))
            ->add('genre', EmailType::class, array('attr' => array('class' => 'col-md-3','placeholder' =>'Thriller,Comédie..'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                    new Email(array("message" => "Le format de l'email n'est pas valide")),
                )
            ))
            ->add('publication', EmailType::class, array('attr' => array('class' => 'col-md-3','placeholder' =>'1997, 1972...'),
                'constraints' => array(
                    new NotBlank(array("message" => "Merci de ne pas laisser ce champ vide")),
                    new Email(array("message" => "Le format de l'email n'est pas valide")),
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
        return 'suggestion_form';
    }
}