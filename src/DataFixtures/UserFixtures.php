<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    private $passwordHasher;

    private static array $licenseType = ['Pro','Plus'];

    private static $modules = [
        [
            'title' => '',
            'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraph }}&lt;/p&gt;',
        ],
        [
            'title' => '',
            'code' => '&lt;p class="text-right"&gt;{{ paragraph }}&lt;/p&gt;',
        ],
        [
            'title' => '',
            'code' => '&lt;div class="row"&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;/div&gt;',
        ],
    ];

    private static $themes = [
        'Про еду',
        'Про PHP',
        'Про женщин',
    ];

    private static $users = [
        [
            'name' => 'Илья',
            'email' => 'ilya@ya.ru',
        ],
        [
            'name' => 'Иван',
            'email' => 'ivan@ya.ru',
        ],
        [
            'name' => 'Саша',
            'email' => 'sasha@ya.ru',
        ],
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadData(ObjectManager $manager)
    {
        foreach ( self::$users as $item ) {
            $this->createMany(User::class, 1, function (User $user) use ($manager, $item) {
                $user
                    ->setName($item['name'])
                    ->setEmail($item['email'])
                    ->setRoles(['ROLE_USER'])
                    ->setPassword($this->passwordHasher->hashPassword($user, '123123'))
                    ->setCreatedAt(new \DateTime())
                    ->setUpdatedAt(new \DateTime());
//            $manager->persist(new ApiToken($user));

                $this->addPayment($user, $manager);
                for ($i = 0; $i < 3; $i++) {
                    $this->addModule($user, $manager, $i);
                }
                for ($i = 0; $i < $this->faker->numberBetween(2, 5); $i++) {
                    $this->addArticle($user, $manager);
                }
            });
        }
    }

    private function addPayment($user, $manager)
    {
        $payment = (new Payment())
                ->setLicenseType($this->faker->randomElement(self::$licenseType))
                ->setUser($user)
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setFinishedAt($this->faker->dateTimeBetween('-10 days', '10 day'))
        ;
        $manager->persist($payment);
    }

    private function addModule($user, $manager, $i)
    {
        $module = (new Module())
            ->setTitle(self::$modules[1]['title'])
            ->setUser($user)
            ->setCode(self::$modules[1]['code'])
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;
        $manager->persist($module);
    }

    private function addArticle($user, $manager)
    {
        $keyWord = $this->faker->word;
        $article = (new Article())
            ->setUser($user)
            ->setTitle($this->faker->word)
            ->setTheme($this->faker->randomElement(self::$themes))
            ->setKeyWord($keyWord)
            ->setKeyWordDist($keyWord . 'd')
            ->setKeyWordMany($keyWord . 's')
            ->setMinSize($this->faker->numberBetween(150, 200))
            ->setMaxSize($this->faker->numberBetween(350, 400))
            ->setPromWord1($this->faker->word)
            ->setPromWord2($this->faker->word)
            ->setContent($this->faker->realText($this->faker->numberBetween(200, 350)))
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
        ;
        $manager->persist($article);
    }
}
