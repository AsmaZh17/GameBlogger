<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setEmail('asma@gmail.com')
            ->setNom('asma')
            ->setPrenom('zh')
            ->setPassword('123')
            ->setPhone('5025')
            ->setAbout('bla bla')
            ->setInstagram('insta');

        $this->assertTrue($user->getEmail() === 'asma@gmail.com');
        $this->assertTrue($user->getNom() === 'asma');
        $this->assertTrue($user->getPrenom() === 'zh');
        $this->assertTrue($user->getPassword() === '123');
        $this->assertTrue($user->getPhone() === '5025');
        $this->assertTrue($user->getAbout() === 'bla bla');
        $this->assertTrue($user->getInstagram() === 'insta');
    }

    public function testIsFalse() : void 
    {
        $user = new User();

        $user->setEmail('asma@gmail.com')
            ->setNom('asma')
            ->setPrenom('zh')
            ->setPassword('123')
            ->setPhone('5025')
            ->setAbout('bla bla')
            ->setInstagram('insta');

        $this->assertFalse($user->getEmail() === 'asmfdgha@gmail.com');
        $this->assertFalse($user->getNom() === 'FALSE');
        $this->assertFalse($user->getPrenom() === 'flase');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getPhone() === 'false');
        $this->assertFalse($user->getAbout() === 'blatd bla');
        $this->assertFalse($user->getInstagram() === 'intsfbgdja');
    }

    public function testIsEmpty() : void {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getNom());
        $this->assertEmpty($user->getPrenom());
        $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getPhone());
        $this->assertEmpty($user->getAbout());
        $this->assertEmpty($user->getInstagram());
    }
}
