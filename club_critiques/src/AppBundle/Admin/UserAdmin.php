<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class UserAdmin extends AbstractAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $roles = $container->getParameter('security.role_hierarchy.roles');
        $rolesChoices = self::flattenRoles($roles);

        $formMapper->add('username', null, ['label' => 'Surnom', 'required' => true]);
        $formMapper->add('firstName', null, ['label' => 'Prénom', 'required' => true]);
        $formMapper->add('lastName', null, ['label' => 'Nom', 'required' => true]);
        $formMapper->add('roles', 'choice', array(
                                        'choices'  => $rolesChoices,
                                        'multiple' => true
                                    )
                        );
        $formMapper->add('description', null, ['label' => 'Description']);
        $formMapper->add('nbReports', null, ['label' => 'Nb de fois signalé', 'disabled' => true]);
        $formMapper->add('contacts', 'entity', ['label' => 'Contacts',
                                                'multiple' => true,
                                                'class' => \AppBundle\Entity\User::class]);
        $formMapper->add('contentsToShare', 'entity', ['label' => 'Contenus à échanger',
                                                        'required' => false,
                                                        'multiple' => true,
                                                        'class' => \AppBundle\Entity\Content::class]);
        $formMapper->add('contentsWanted', 'entity', ['label' => 'Contenus recherchés',
                                                                'required' => false,
                                                                'multiple' => true,
                                                                'class' => \AppBundle\Entity\Content::class]);
        $formMapper->add('dateAdd', 'date', ['format' => 'd M y', 'label' => 'Date d\'inscription', 'disabled' => true, 'required' => false]);
        $formMapper->add('status', null, ['label' => 'Activé']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username', null, ['label' => 'Surnom']);
        $datagridMapper->add('firstName', null, ['label' => 'Prénom']);
        $datagridMapper->add('lastName', null, ['label' => 'Nom']);
        $datagridMapper->add('status', null, ['label' => 'Activé']);
    }

    public function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('username', null, ['label' => 'Surnom'])
            ->add('firstName', null, ['label' => 'Prénom'])
            ->add('lastName', null, ['label' => 'Nom'])
            ->add('status', null, ['label' => 'Activé', 'editable' => true])
            ->add('_action', null, array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                    'banIp' => array(
                        'template' => 'list_action_ban.html.twig'
                    )
                )));
    }


    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('banIp', $this->getRouterIdParameter().'/banIp');
    }

    protected static function flattenRoles($rolesHierarchy)
    {
        $flatRoles = array();
        foreach($rolesHierarchy as $roles) {

            if(empty($roles)) {
                continue;
            }

            foreach($roles as $role) {
                if(!isset($flatRoles[$role])) {
                    $flatRoles[$role] = $role;
                }
            }
        }

        return $flatRoles;
    }
}