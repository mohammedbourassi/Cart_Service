<?php

namespace App\DataFixtures;

use App\Entity\Products;
use App\Enum\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $product1 = new Products();
        $product1->setName('Product 1');
        $product1->setPrice(100);
        $product1->setStock(100);
        $product1->setType(ProductType::PHYSICAL);
        $product1->setSellerId(1);
        $product1->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product1);

        $product2 = new Products();
        $product2->setName('Product 2');
        $product2->setPrice(20);
        $product2->setStock(500);
        $product2->setType(ProductType::PHYSICAL);
        $product2->setSellerId(2);
        $product2->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product2);

        $product3 = new Products();
        $product3->setName('Product 3');
        $product3->setPrice(30);
        $product3->setStock(300);
        $product3->setType(ProductType::PHYSICAL);
        $product3->setSellerId(3);
        $product3->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product3);

        $product4 = new Products();
        $product4->setName('Product 4');
        $product4->setPrice(400);
        $product4->setStock(200);
        $product4->setType(ProductType::PHYSICAL);
        $product4->setSellerId(4);
        $product4->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product4);

        $product5 = new Products();
        $product5->setName('Product 5');
        $product5->setPrice(50);
        $product5->setStock(100 );
        $product5->setType(ProductType::PHYSICAL);
        $product5->setSellerId(5);
        $product5->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product5);

        $product6 = new Products();
        $product6->setName('Product 6');
        $product6->setPrice(60);
        $product6->setStock(0);
        $product6->setType(ProductType::PHYSICAL);
        $product6->setSellerId(6);
        $product6->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product6);

        $product7 = new Products();
        $product7->setName('Product 7');
        $product7->setPrice(70);
        $product7->setStock(50);
        $product7->setType(ProductType::DIGITAL);
        $product7->setSellerId(7);
        $product7->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product7);

        $product8 = new Products();
        $product8->setName('Product 8');
        $product8->setPrice(80);
        $product8->setStock(40);
        $product8->setType(ProductType::DIGITAL);
        $product8->setSellerId(8);
        $product8->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($product8);

        $manager->flush();
    }
}
