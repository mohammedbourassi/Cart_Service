<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $product1 = new Product();
        $product1->setName('Product 1');
        $product1->setPrice(100);
        $product1->setStock(10);
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Product 2');
        $product2->setPrice(20);
        $product2->setStock(5);
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Product 3');
        $product3->setPrice(30);
        $product3->setStock(3);
        $manager->persist($product3);

        $product4 = new Product();
        $product4->setName('Product 4');
        $product4->setPrice(400);
        $product4->setStock(2);
        $manager->persist($product4);

        $product5 = new Product();
        $product5->setName('Product 5');
        $product5->setPrice(50);
        $product5->setStock(1);
        $manager->persist($product5);

        $product6 = new Product();
        $product6->setName('Product 6');
        $product6->setPrice(60);
        $product6->setStock(0);
        $manager->persist($product6);

        $product7 = new Product();
        $product7->setName('Product 7');
        $product7->setPrice(70);
        $product7->setStock(50);
        $manager->persist($product7);

        $product8 = new Product();
        $product8->setName('Product 8');
        $product8->setPrice(80);
        $product8->setStock(40);
        $manager->persist($product8);

        $manager->flush();
    }
}
