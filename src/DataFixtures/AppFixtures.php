<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Store;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $slugger;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('user@gmail.com')
            ->setUsername('Imad Najmi')
            ->setPassword($this->passwordEncoder->encodePassword($user, '123456'))
            ->setFullName('Imad Najmi')
        ;

        $manager->persist($user);

        $store = new Store();

        $store->setName('Morocco Store')
            ->setOwner($user)
            ->setLocation([31.791702, -7.09262])
        ;

        $manager->persist($store);

        $store = new Store();
        $store->setName('Egybt Store')
            ->setOwner($user)
            ->setLocation([26.820553, 30.802498])
        ;

        $manager->persist($store);

        $store = new Store();
        $store->setName('India Store')
            ->setOwner($user)
            ->setLocation([20.593684, 78.96288])
        ;

        $manager->persist($store);

        $store = new Store();
        $store->setName('Algeria Store')
            ->setOwner($user)
            ->setLocation([28.033886, 1.659626])
        ;

        $manager->persist($store);

        $store = new Store();
        $store->setName('Argentina Store')
            ->setOwner($user)
            ->setLocation([-38.416097, -63.616672])
        ;

        $manager->persist($store);

        $manager->flush();
    }
}
