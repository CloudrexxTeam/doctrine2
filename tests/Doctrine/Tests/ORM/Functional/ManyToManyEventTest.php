<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Functional;

use Doctrine\Tests\Models\CMS\CmsUser;
use Doctrine\Tests\Models\CMS\CmsGroup;
use Doctrine\ORM\Events;
use Doctrine\Tests\OrmFunctionalTestCase;

/**
 * ManyToManyEventTest
 *
 * @author Francisco Facioni <fran6co@gmail.com>
 */
class ManyToManyEventTest extends OrmFunctionalTestCase
{
    /**
     * @var PostUpdateListener
     */
    private $listener;

    protected function setUp()
    {
        $this->useModelSet('cms');
        parent::setUp();
        $this->listener = new PostUpdateListener();
        $evm = $this->em->getEventManager();
        $evm->addEventListener(Events::postUpdate, $this->listener);
    }

    public function testListenerShouldBeNotifiedOnlyWhenUpdating()
    {
        $user = $this->createNewValidUser();
        $this->em->persist($user);
        $this->em->flush();
        self::assertFalse($this->listener->wasNotified);

        $group = new CmsGroup();
        $group->name = "admins";
        $user->addGroup($group);
        $this->em->persist($user);
        $this->em->flush();

        self::assertTrue($this->listener->wasNotified);
    }

    /**
     * @return CmsUser
     */
    private function createNewValidUser()
    {
        $user = new CmsUser();
        $user->username = 'fran6co';
        $user->name = 'Francisco Facioni';
        $group = new CmsGroup();
        $group->name = "users";
        $user->addGroup($group);
        return $user;
    }
}

class PostUpdateListener
{
    /**
     * @var bool
     */
    public $wasNotified = false;

    /**
     * @param $args
     */
    public function postUpdate($args)
    {
        $this->wasNotified = true;
    }
}
