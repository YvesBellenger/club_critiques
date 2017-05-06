<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ContentAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $entity = new Category();
        $query = $this->modelManager->getEntityManager($entity)->createQuery('SELECT * FROM \AppBundle\Entity\Category c WHERE parentCategory NOT NULL ORDER BY c.nameASC');

        $formMapper->add('title', 'text', ['label' => 'Titre', 'required' => true]);
        $formMapper->add('category', 'entity', ['label' => 'Catégorie', 'required' => true, 'class' => 'AppBundle\Entity\Category']);
        $formMapper->add('author', null, ['label' => 'Auteur', 'required' => true]);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
        $datagridMapper->add('category');
        $datagridMapper->add('author');
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('title', null, ['label' => 'Titre'])
            ->add('category', null, ['label' => 'Catégorie'])
            ->add('author', null, ['label' => 'Auteur'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}