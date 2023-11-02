<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;

class DashboardMenu
{
    public function createMenu(Request $request)
    {
        $items = [
            'dashboard' => 'Рабочий стол',
            'create-article' => 'Создать статью',
            'history' => 'История статей',
            'subscription' => 'Подписка',
            'profile' => 'Профиль',
            'modules' => 'Модули генератора'
        ];

        $menuItems = [];

        foreach ($items as $key => &$item) {
            $active = str_replace('/', '', $request->getRequestUri()) === $key;
            $menuItems[] = [
                'item' => $item,
                'active' => $active,
                'url' => $key
            ];
        }
        return $menuItems;
    }
}