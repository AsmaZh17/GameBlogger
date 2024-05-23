<?php

namespace App\Tests;

use App\Entity\Categorie;
use PHPUnit\Framework\TestCase;

class CategorieUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $categorie = new Categorie();

        $categorie->setNom('asma@gmail.com')
            ->setDescription('asma');

        $this->assertTrue($categorie->getNom() === 'asma@gmail.com');
        $this->assertTrue($categorie->getDescription() === 'asma');
    }

    public function testIsFalse() : void 
    {
        $categorie = new Categorie();

        $categorie->setNom('asma@gmail.com')
            ->setDescription('asma');

        $this->assertFalse($categorie->getNom() === 'asmfdgha@gmail.com');
        $this->assertFalse($categorie->getDescription() === 'FALSE');
    }

    public function testIsEmpty() : void {
        $categorie = new Categorie();

        $this->assertEmpty($categorie->getNom());
        $this->assertEmpty($categorie->getDescription());
    }
}
