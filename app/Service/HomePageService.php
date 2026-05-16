<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PageDTO\HomePageDataDTO;
use App\Repository\HomePageRepository;

final class HomePageService
{
    /** Лимит последних статей на категорию на главной */
    private const int LATEST_ARTICLES_PER_CATEGORY = 3;

    public function __construct(
        private readonly HomePageRepository $homePageRepository,
    ) {
    }

    /**
     * Возвращает данные для главной страницы
     */
    public function getPageData(): HomePageDataDTO
    {
        return new HomePageDataDTO(
            $this->homePageRepository->findCategoriesWithLatestArticles(
                self::LATEST_ARTICLES_PER_CATEGORY,
            ),
        );
    }
}
