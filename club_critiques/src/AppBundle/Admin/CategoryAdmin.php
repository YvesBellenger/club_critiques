<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryAdmin extends AbstractAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('code', 'text', ['label' => 'Code', 'required' => true]);
        $formMapper->add('parentCategory', null, ['label' => 'Catégorie parent']);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code');
        $datagridMapper->add('name');
        $datagridMapper->add('parentCategory', null, ['label' => 'Catégorie parent']);
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name', null, ['label' => 'Nom'])
            ->add('code', null, ['label' => 'Code'])
            ->add('dateAdd', 'date', ['label' => 'Date d\'ajout'])
            ->add('dateUpdate', 'date', ['label' => 'Date de modification'])
            ->add('parentCategory', null, ['label' => 'Catégorie parent'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}