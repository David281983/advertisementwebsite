<?php

namespace App\DataFixtures;

use App\Entity\Advertisement;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    
public function __construct(
    private UserPasswordHasherInterface $userPasswordHasher){}

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test@test.com');
        $user1->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user1,
                '12345678'
            )
        );
        $manager->persist($user1);
        $user2 = new User();
        $user2->setEmail('john@test.com');
        $user2->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user2,
                '12345678'
            )
        );
        $manager->persist($user2);

        $advertisement1 = new Advertisement();
        $advertisement1->setTitle('Vand Laptop');
        $advertisement1->setDescription('Abia folosit');
        $advertisement1->setPrice(2500);
        $advertisement1->setCreated(new DateTime());
        $advertisement1->setAuthor($user1);
        $manager->persist($advertisement1);

        $advertisement2 = new Advertisement();
        $advertisement2->setTitle('Vand camera foto');
        $advertisement2->setDescription('Stare deteriorata');
        $advertisement2->setPrice(250);
        $advertisement2->setCreated(new DateTime());
        $advertisement2->setAuthor($user2);
        $manager->persist($advertisement2);

        $advertisement3 = new Advertisement();
        $advertisement3->setTitle('Dau chirie garsoniera');
        $advertisement3->setDescription('30 metri patrati, se afla in centru langa profi');
        $advertisement3->setPrice(1700);
        $advertisement3->setCreated(new DateTime());
        $advertisement3->setAuthor($user1);
        $manager->persist($advertisement3);

        $manager->flush();
    }
}
