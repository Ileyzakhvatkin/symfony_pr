<?php

namespace App\Services;

use App\Repository\UserRepository;

class UserRegValidator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($request): array
    {
        $errors = [];
        $userName = $request->request->get('name');
        $userEmail = $request->request->get('email');
        $userPass = $request->request->get('password1');
        if (!$userName) {
            $errors[] = 'Введите Ваше имя';
        } elseif (strlen($userName) < 4) {
            $errors[] = 'Имя должно быть больше 4 символов';
        }
        if (!$userEmail) {
            $errors[] = 'Введите Ваш email';
        } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Неверный формат email';
        } elseif (count($this->userRepository->getUserByEmail($userEmail)) > 0) {
            $errors[] = 'Пользователь с таким email уже зарегистрирован';
        }
        if (!$userPass) {
            $errors[] = 'Введите Ваш пароль';
        } elseif (strlen($userPass) < 6) {
            $errors[] = 'Пароль должен быть больше 6 символов';
        } elseif ($userPass !== $request->request->get('password2')) {
            $errors[] = 'Пароли не совпадают';
        }

        return $errors;
    }
}