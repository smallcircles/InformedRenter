<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards;

use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Twig;

defined('ABSPATH') || exit;

class Card
{
    protected string $html;
    protected CardData $acfCardData;
    protected QueryBuilder $queryBuilder;
    protected CardMarkup $cardMarkup;
    protected Twig $twig;
    protected int $pagesAmount;
    protected array $postIds;

    public function __construct(
        CardData $acfCardData,
        QueryBuilder $queryBuilder,
        CardMarkup $cardMarkup,
        Twig $twig
    ) {
        $this->html = '';
        $this->acfCardData = $acfCardData;
        $this->queryBuilder = $queryBuilder;
        $this->cardMarkup = $cardMarkup;
        $this->twig = $twig;
        $this->pagesAmount = 0;
        $this->postIds = [];
    }

    protected function getTwigArgs(): array
    {
        return [
            '_card' => [
                'id' => $this->acfCardData->getMarkupId(),
                'view_id' => $this->acfCardData->acfViewId,
                'no_posts_found_message' => $this->acfCardData->getNoPostsFoundMessageTranslation(),
                'post_ids' => $this->postIds,
                'classes' => $this->acfCardData->cssClasses ?
                    $this->acfCardData->cssClasses . ' ' :
                    '',
            ],
        ];
    }

    public function queryPostsAndInsertData(
        int $pageNumber,
        bool $isMinifyMarkup = true,
        bool $isLoadMore = false
    ): void {
        if ($isMinifyMarkup) {
            // remove special symbols that used in the markup for a preview
            // exactly here, before the fields are inserted, to avoid affecting them
            $this->html = str_replace(["\t", "\n", "\r"], '', $this->html);
        }

        $postsData = $this->queryBuilder->getPostsData($this->acfCardData, $pageNumber);
        $this->pagesAmount = $postsData['pagesAmount'];
        $this->postIds = $postsData['postIds'];

        // don't use the 'AcfCardData->markup' field, as user can override it (and it shouldn't be supported)
        $this->html = $this->cardMarkup->getMarkup($this->acfCardData, $isLoadMore);

        $this->html = $this->twig->render($this->acfCardData->getSource(), $this->html, $this->getTwigArgs());
        // render the shortcodes
        $this->html = do_shortcode($this->html);
    }

    public function getHTML(): string
    {
        return $this->html;
    }

    public function getCardData(): CardData
    {
        return $this->acfCardData;
    }
}