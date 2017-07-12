<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Proxies\__CG__\AppBundle\Entity\Author;
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

        $formMapper->add('category', 'entity', ['label' => 'Catégorie', 'required' => true, 'class' => 'AppBundle\Entity\Category']);
        $formMapper->add('authors', 'entity', ['required' => true,
                                                'label' => 'Auteurs',
                                                'multiple' => true,
                                                'class' => \AppBundle\Entity\Author::class]);
        $formMapper->add('title', 'text', ['label' => 'Titre', 'required' => true]);
        $formMapper->add('publishedDate', 'text', ['label' => 'Année de publication', 'required' => true]);
        $formMapper->add('description', CKEditorType::class, ['label' => 'Description', 'required' => true]);
        $formMapper->add('image', 'text', ['label' => 'Lien de l\'image']);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title', null, ['label' => 'Titre']);
        $datagridMapper->add('category', null, ['label' => 'Catégorie']);
        $datagridMapper->add('authors', null, ['label' => 'Auteur']);
        $datagridMapper->add('status', null, ['label' => 'En ligne']);
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('title', null, ['label' => 'Titre'])
            ->add('category', null, ['label' => 'Catégorie'])
            ->add('authors', null, ['label' => 'Auteur'])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}