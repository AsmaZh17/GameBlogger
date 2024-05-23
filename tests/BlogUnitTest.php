<?php

namespace App\Tests;

use App\Entity\Blog;
use DateTime;
use PHPUnit\Framework\TestCase;

class BlogUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $blog = new Blog();
        $datetime = new DateTime();

        $blog->setTitre('asma@gmail.com')
            ->setImage('asma')
            ->setContenu('zh')
            ->setCreateAt($datetime);

        $this->assertTrue($blog->getTitre() === 'asma@gmail.com');
        $this->assertTrue($blog->getImage() === 'asma');
        $this->assertTrue($blog->getContenu() === 'zh');
        $this->assertTrue($blog->getCreateAt() === $datetime);
    }

    public function testIsFalse() : void 
    {
        $blog = new Blog();
        $datetime = new DateTime();

        $blog->setTitre('asma@gmail.com')
            ->setImage('asma')
            ->setContenu('zh')
            ->setCreateAt($datetime);

            $this->assertFalse($blog->getTitre() === 'asfghma@gmail.com');
            $this->assertFalse($blog->getImage() === 'asvghjma');
            $this->assertFalse($blog->getContenu() === 'zhfgh');
            $this->assertFalse($blog->getCreateAt() === new DateTime());
    }

    public function testIsEmpty() : void {
        $blog = new Blog();

        $this->assertEmpty($blog->getTitre());
        $this->assertEmpty($blog->getImage());
        $this->assertEmpty($blog->getContenu());
        $this->assertEmpty($blog->getCreateAt());
    }
}
