<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 21.11.17
 * Time: 11:10
 */

namespace App\Model;
use Nette;

class AuthorizationFactory
{

    /**
     * @return Nette\Security\Permission
     */

    public static function create()
    {
        $acl = new Nette\Security\Permission;


        $acl->addRole('2');
        $acl->addRole('1', '2');
        $acl->addRole('0', '1');

        $acl->addResource('coop');
        $acl->addResource('training');
        $acl->addResource('keeper');
        $acl->addResource('animal');
        $acl->addResource('feed');
        $acl->addResource('clean');
        $acl->addResource('admin');
        $acl->addResource('addKind');

        $acl->allow('2', ['animal', 'training', 'coop', 'clean', 'feed'], 'view');

        $acl->allow('1' ,['animal'], 'add');
        $acl->allow('1' ,['animal'], 'view');

        $acl->allow('0', 'admin');
        $acl->allow('0', ['clean', 'feed'], 'add');
        $acl->allow('0', ['coop','addKind', 'training'], 'add');

        return $acl;

    }


}