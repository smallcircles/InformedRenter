<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards;

use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Twig;

defined('ABSPATH') || exit;

class CardFactory
{
    protected QueryBuilder $queryBuilder;
    protected CardMarkup $cardMarkup;
    protected Twig $twig;
    /**
     * @var CardData[]
     */
    protected array $renderedCards;

    public function __construct(QueryBuilder $queryBuilder, CardMarkup $cardMarkup, Twig $twig)
    {
        $this->queryBuilder = $queryBuilder;
        $this->cardMarkup = $cardMarkup;
        $this->twig = $twig;
        $this->renderedCards = [];
    }

    protected function getAcfCard(CardData $acfCardData): Card
    {
        return new Card($acfCardData, $this->queryBuilder, $this->cardMarkup, $this->twig);
    }

    protected function markCardAsRendered(CardData $acfCardData): void
    {
        $this->renderedCards[$acfCardData->getSource()] = $acfCardData;
    }

    public function createAndGetHtml(
        CardData $acfCardData,
        int $pageNumber,
        bool $isMinifyMarkup = true,
        bool $isLoadMore = false
    ): string {
        $acfCard = $this->getAcfCard($acfCardData);
        $acfCard->queryPostsAndInsertData($pageNumber, $isMinifyMarkup, $isLoadMore);

        $cardData = $acfCard->getCardData();

        $this->markCardAsRendered($cardData);

        return $acfCard->getHTML();
    }

    /**
     * @return CardData[]
     */
    public function getRenderedCards(): array
    {
        return $this->renderedCards;
    }
}
