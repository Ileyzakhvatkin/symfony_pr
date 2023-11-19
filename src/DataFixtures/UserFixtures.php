<?php

namespace App\DataFixtures;

use App\Entity\ApiToken;
use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Payment;
use App\Entity\User;
use App\Services\Constants\DemoModules;
use App\Services\Constants\DemoThemes;
use App\Services\Constants\DemoUsers;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    private $passwordHasher;

    private static array $licenseType = ['PRO','PLUS'];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadData(ObjectManager $manager)
    {
        foreach ( DemoUsers::getUsers() as $itemUser ) {
            $this->createMany(User::class, 1, function (User $user) use ($manager, $itemUser) {
                $date = $this->faker->dateTimeBetween('-50 days', '0 day');
                $user
                    ->setName($itemUser['name'])
                    ->setEmail($itemUser['email'])
                    ->setRoles($itemUser['roles'])
                    ->setRegLink(sha1(uniqid('token')))
                    ->setPassword($this->passwordHasher->hashPassword($user, '123123'))
                    ->setCreatedAt($date)
                    ->setUpdatedAt($date);

                $manager->persist(new ApiToken($user));

                $this->addPayment($user, $manager);
                for ($i = 0; $i < 3; $i++) {
                    $this->addModule($user, $manager, $i);
                }
                for ($i = 0; $i < 25; $i++) {
                    $this->addArticle($user, $manager);
                }
            });
        }
    }

    private function addPayment($user, $manager)
    {
        $date = $this->faker->dateTimeBetween('-50 days', '0 day');
        $payment = (new Payment())
                ->setLicenseType($this->faker->randomElement(self::$licenseType))
                ->setUser($user)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setFinishedAt($this->faker->dateTimeBetween('-10 days', '10 day'))
        ;
        $manager->persist($payment);
    }

    private function addModule($user, $manager, $i)
    {
        $date = $this->faker->dateTimeBetween('-50 days', '0 day');
        $module = (new Module())
            ->setTitle(DemoModules::getModules()[$i]['title'])
            ->setUser($user)
            ->setCode(DemoModules::getModules()[$i]['code'])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
        ;
        $manager->persist($module);
    }

    private function addArticle($user, $manager)
    {
        $keyword = $this->faker->city;
        $date = $this->faker->dateTimeBetween('-50 days', '0 day');
        $size = $this->faker->numberBetween(40, 50) * 10;
        $maxsize = $this->faker->numberBetween(50, 80) * 10;
        $article = (new Article())
            ->setUser($user)
            ->setTitle($this->faker->streetName)
            ->setTheme($this->faker->randomElement(DemoThemes::getThemes()))
            ->setKeyword(['0' => $keyword, '1' => $keyword, '2' => $keyword, '3' => $keyword, '4' => $keyword,  '5' => $keyword, '6' => $keyword . 's'])
            ->setSize($size)
            ->setMaxSize($maxsize)
            ->setContent($this->faker->realText($this->faker->numberBetween($size, $maxsize)))
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
        ;
        $manager->persist($article);
    }
}
