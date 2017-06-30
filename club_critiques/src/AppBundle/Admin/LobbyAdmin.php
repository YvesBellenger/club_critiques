<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LobbyAdmin extends AbstractAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('content', 'entity', ['label' => 'Contenu', 'required' => true, 'class' => 'AppBundle\Entity\Content']);
        $formMapper->add('max_participants', null, ['label' => 'Nombre maximum de participants', 'required' => true]);
        $formMapper->add('date_start', null, ['label' => 'Début']);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('content');
        $datagridMapper->add('date_start', null, ['label' => 'Début']);
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('content', null, ['label' => 'Contenu'])
            ->add('date_start', null, ['label' => 'Début'])
            ->add('max_participants', null, ['label' => 'Nombre maximum de participants', 'editable' => true])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}