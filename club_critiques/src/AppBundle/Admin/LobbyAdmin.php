<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Lobby;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LobbyAdmin extends AbstractAdmin
{

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdByUser'
    );


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('content', 'entity', ['label' => 'Contenu', 'required' => true, 'class' => 'AppBundle\Entity\Content']);
        $formMapper->add('date_start', null, ['label' => 'Début']);
        $formMapper->add('date_end', null, ['label' => 'Fin']);
        $formMapper->add('createdByUser', null, ['label' => 'Proposé par un utilisateur']);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('content');
        $datagridMapper->add('date_start', null, ['label' => 'Début']);
        $datagridMapper->add('date_end', null, ['label' => 'Fin']);
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('content', null, ['label' => 'Contenu'])
            ->add('date_start', null, ['label' => 'Début'])
            ->add('date_end', null, ['label' => 'Fin'])
            ->add('createdByUser', null, ['label' => 'Proposé par un utilisateur'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}