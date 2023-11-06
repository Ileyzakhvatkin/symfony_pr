<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Module;
use App\Entity\Payment;
use App\Entity\User;
use App\Entity\Word;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    private $passwordHasher;

    private static array $licenseType = ['PRO','PLUS'];

    private static $modules = [
        [
            'title' => 'Заголовок - параграф',
            'code' => '&lt;h1&gt;{{ title }}&lt;/h1&gt; &lt;p&gt;{{ paragraph }}&lt;/p&gt;',
        ],
        [
            'title' => 'Текст слева - параграф',
            'code' => '&lt;p class="text-right"&gt;{{ paragraph }}&lt;/p&gt;',
        ],
        [
            'title' => 'Текст по столбцам',
            'code' => '&lt;div class="row"&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;div class="col-sm-6"&gt; {{ paragraphs }} &lt;/div&gt; &lt;/div&gt;',
        ],
    ];

    private static $themes = [
        'Про не здоровую еду',
        'Про PHP и всякое',
        'Про женщин и не только',
    ];

    private static $users = [
        [
            'name' => 'Илья Смирнов',
            'email' => 'ilya@ya.ru',
            'roles' => ['ROLE_USER']
        ],
        [
            'name' => 'Иван Рудин',
            'email' => 'ivan@ya.ru',
            'roles' => ['ROLE_USER']
        ],
        [
            'name' => 'Саша Агафонова',
            'email' => 'sasha@ya.ru',
            'roles' => ['ROLE_USER']
        ],
    ];

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadData(ObjectManager $manager)
    {
        foreach ( self::$users as $itemUser ) {
            $this->createMany(User::class, 1, function (User $user) use ($manager, $itemUser) {
                $date = $this->faker->dateTimeBetween('-50 days', '0 day');
                $user
                    ->setName($itemUser['name'])
                    ->setEmail($itemUser['email'])
                    ->setRoles($itemUser['roles'])
                    ->setActive(true)
                    ->setPassword($this->passwordHasher->hashPassword($user, '123123'))
                    ->setCreatedAt($date)
                    ->setUpdatedAt($date);
//            $manager->persist(new ApiToken($user));

                $this->addPayment($user, $manager);
                for ($i = 0; $i < 3; $i++) {
                    $this->addModule($user, $manager, $i);
                }
                for ($i = 0; $i < $this->faker->numberBetween(15, 25); $i++) {
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
            ->setTitle(self::$modules[rand(0,2)]['title'])
            ->setUser($user)
            ->setCode(self::$modules[1]['code'])
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
        ;
        $manager->persist($module);
    }

    private function addArticle($user, $manager)
    {
        $keyWord = $this->faker->city;
        $date = $this->faker->dateTimeBetween('-50 days', '0 day');
        $size = $this->faker->numberBetween(40, 50) * 10;
        $maxsize = $this->faker->numberBetween(50, 80) * 10;
        $article = (new Article())
            ->setUser($user)
            ->setTitle($this->faker->streetName)
            ->setTheme($this->faker->randomElement(self::$themes))
            ->setKeyword($keyWord)
            ->setKeywordDist($keyWord . 'd')
            ->setKeywordMany($keyWord . 's')
            ->setSize($size)
            ->setMaxSize($maxsize)
            ->setContent($this->faker->realText($this->faker->numberBetween($size, $maxsize)))
            ->setCreatedAt($date)
            ->setUpdatedAt($date)
        ;

        $manager->persist($article);
    }

    private function addWord($article, $manager)
    {
        $word = (new Word())
            ->setArticle($article)
            ->setTitle($this->faker->word)
            ->setCount($this->faker->numberBetween(2, 6))
        ;
        $manager->persist($word);
    }
}
