<?php

namespace App\Services;

use App\Entity\User;
use Closure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendCheckRegistration(User $user, $link): void
    {
        $this->send(
            'email/check-registration.html.twig',
            'Подтверждение регистрации на сайте BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($link) {
                $email->context([
                    'link' => $link
                ]);
            }
        );
    }

    public function sendNewPayment(User $user, $level, $period): void
    {
        $this->send(
            'email/new-payment.html.twig',
            'Изменение профиля пользователя на сайте BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($level, $period) {
                $email->context([
                    'level' => $level,
                    'period' => $period,
                ]);
            }
        );
    }

    public function sendUpdateProfile(User $user, $password): void
    {
        $this->send(
            'email/update-profile.html.twig',
            'Изменение личного профиля на сайте BlaBlaArticle',
            $user,
            function (TemplatedEmail $email) use ($password) {
                $email->context([
                    'password' => $password
                ]);
            }
        );
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function send(string $template, string $subject, User $user, Closure $callback = null): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('noreply@zis-symfony.tw1.ru', 'BlaBlaArticle'))
            ->to(new Address($user->getEmail(), $user->getName()))
            ->htmlTemplate($template)
            ->subject($subject)
        ;

        if ($callback) {
            $callback($email);
        }

        $this->mailer->send($email);
    }
}