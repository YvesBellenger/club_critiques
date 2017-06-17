<?php

namespace AppBundle\Admin;

use AppBundle\Entity\CMS;
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
        $formMapper->add('name', 'text', ['label' => 'Nom', 'required' => true]);
        $formMapper->add('title', 'text', ['label' => 'Titre']);
        $formMapper->add('content', CKEditorType::class, ['label' => 'Contenu', 'required' => true]);
        $formMapper->add('nav', null, ['label' => 'Apparait dans le menu']);
        $formMapper->add('footer', null, ['label' => 'Apparait dans le footer']);
        $formMapper->add('column_footer', 'choice', ['label' => 'Colonne', 'choices' => CMS::$columns]);
        $formMapper->add('position', 'choice', ['label' => 'Position', 'choices' => array(1 => "1",2 => "2",3 => "3",4 => "4")]);
        $formMapper->add('status', null, ['label' => 'En ligne']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code');
        $datagridMapper->add('title');
        $datagridMapper->add('nav', null, ['label' => 'Apparait dans le menu']);
        $datagridMapper->add('footer', null, ['label' => 'Apparait dans le footer']);
        $datagridMapper->add('status');
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('code', null, ['label' => 'Code'])
            ->add('title', null, ['label' => 'Titre'])
            ->add('nav', null, ['label' => 'Apparait dans le menu', 'editable' => true])
            ->add('footer', null, ['label' => 'Apparait dans le footer', 'editable' => true])
            ->add('status', null, ['label' => 'En ligne', 'editable' => true]);
    }
}