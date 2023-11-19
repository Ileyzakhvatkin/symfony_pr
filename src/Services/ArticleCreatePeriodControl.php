<?php

namespace App\Services;

use App\Repository\ArticleRepository;
use Carbon\Carbon;

class ArticleCreatePeriodControl
{
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function checkBlock($authUser, $licenseInfo): bool
    {
        $isBlocked = true;
        if ($licenseInfo['type'] == 'PRO') {
            $isBlocked = false;
        } else {
            $parameters = [
                'val' => $authUser->getId(),
                'date' => Carbon::now()->subHour()->subHour()->toDateTimeString(),
            ];
            if ($this->articleRepository->getArticleCountFromPeriod($parameters)[0]['1'] < 2) {
                $isBlocked = false;
            }
        }

        return $isBlocked;
    }
}