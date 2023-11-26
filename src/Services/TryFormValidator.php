<?php

namespace App\Services;

use App\Services\Constants\RussianNouns;
use Symfony\Component\HttpFoundation\Request;

class TryFormValidator
{
    public function validate($request): array
    {
        $errors = [];
        $title = $request->request->get('title');
        $keyword = $request->request->get('keyword');

        if (!$title) {
            $errors[] = 'Введите заголовок статьи';
        } elseif(strlen($title) < 12 || strlen($keyword) > 30) {
            $errors[] = 'Длинна заголовка должно быть от 12 до 30 букв';
        }
        if (!$keyword) {
            $errors[] = 'Введите ключевое слово для статьи';
        } elseif (!in_array($keyword, (new RussianNouns())->getNouns())) {
            $errors[] = 'Продвигаемое слово не является существительным русского языка';
        }

        return $errors;
    }
}