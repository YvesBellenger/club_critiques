<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Content;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlockContentAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('code', 'text', ['label' => 'Code', 'required' => true]);
        $formMapper->add('contents', 'entity', ['required' => true,
                        'label' => 'Contenus',
                        'multiple' => true,
                        'class' => Content::class]);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('code');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('title', null, ['label' => 'Titre'])
            ->add('code', null, ['label' => 'Code'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}