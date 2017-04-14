<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class AuthorAdmin extends AbstractAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name', null, ['label' => 'Nom'])
            ->add('dateAdd', 'date', ['label' => 'Date d\'ajout'])
            ->add('dateUpdate', 'date', ['label' => 'Date de modification'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}