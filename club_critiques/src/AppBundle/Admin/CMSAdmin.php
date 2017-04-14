<?php

namespace AppBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CMSAdmin extends AbstractAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text', ['label' => 'Code', 'required' => true]);
        $formMapper->add('title', 'text', ['label' => 'Titre']);
        $formMapper->add('content', CKEditorType::class, ['label' => 'Contenu', 'required' => true]);
        $formMapper->add('position', 'integer', ['label' => 'Position dans le footer', 'required' => false]);
        $formMapper->add('nav', null, ['label' => 'Apparait dans le menu']);
        $formMapper->add('footer', null, ['label' => 'Apparait dans le footer']);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('code', null, ['label' => 'Code'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}