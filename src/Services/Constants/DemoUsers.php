<?php

namespace App\Services\Constants;

class DemoUsers
{
    static public function getUsers(): array
    {
        return [
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
    }
}