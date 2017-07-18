<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', null, array('label' => 'Prénom', 'attr' => array('pattern' => "[A-Za-z]", 'title' => 'Seulement des caractères alphabétiques')));
        $builder->add('lastName', null, array('label' => 'Nom', 'attr' => array('pattern' => "[A-Za-z]", 'title' => 'Seulement des caractères alphabétiques')));
        $builder->add('description');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'fos_user_profile_edit';
    }

    public function getName()
    {
        return 'app_user_profile_edit';
    }
}