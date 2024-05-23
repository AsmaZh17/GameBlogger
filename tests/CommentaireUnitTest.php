<?php

namespace App\Tests;

use App\Entity\Commentaire;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class CommentaireUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $commentaire = new Commentaire();
        $datetime = new DateTime();
        $author = new User();

        $commentaire->setContenu('asma@gmail.com')
            ->setCreateAt($datetime)
            ->setAuthor($author);

        $this->assertTrue($commentaire->getContenu() === 'asma@gmail.com');
        $this->assertTrue($commentaire->getCreateAt() === $datetime);
        $this->assertTrue($commentaire->getAuthor() === $author);
    }

    public function testIsFalse() : void 
    {
        $commentaire = new Commentaire();
        $datetime = new DateTime();

        $commentaire->setContenu('asma@gmail.com')
            ->setCreateAt($datetime);

        $this->assertFalse($commentaire->getContenu() === 'asmfdgha@gmail.com');
        $this->assertFalse($commentaire->getCreateAt() === new DateTime());
    }

    public function testIsEmpty() : void {
        $commentaire = new Commentaire();

        $this->assertEmpty($commentaire->getContenu());
        $this->assertEmpty($commentaire->getCreateAt());
        $this->assertEmpty($commentaire->getAuthor());
    }
}
