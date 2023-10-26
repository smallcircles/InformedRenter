<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cards\CardMarkup;
use org\wplake\acf_views\Cards\QueryBuilder;
use org\wplake\acf_views\Cpt\SaveActions;
use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Plugin;
use org\wplake\acf_views\Shortcodes;

defined('ABSPATH') || exit;

class CardsSaveActions extends SaveActions
{
    protected CardMarkup $cardMarkup;
    protected QueryBuilder $queryBuilder;
    protected Html $html;
    protected CardsMetaBoxes $cardsMetaBoxes;

    public function __construct(
        Cache $cache,
        Plugin $plugin,
        CardMarkup $cardMarkup,
        QueryBuilder $queryBuilder,
        Html $html,
        CardsMetaBoxes $cardsMetaBoxes,
        CardData $cardData
    ) {
        parent::__construct($cache, $plugin, $cardData);

        $this->cardMarkup = $cardMarkup;
        $this->queryBuilder = $queryBuilder;
        $this->html = $html;
        $this->cardsMetaBoxes = $cardsMetaBoxes;
    }

    protected function getCptName(): string
    {
        return CardsCpt::NAME;
    }

    /**
     * @param CardData $cptData
     * @return array
     */
    protected function getTranslatableLabels($cptData): array
    {
        $labels = [];

        if ($cptData->noPostsFoundMessage) {
            $labels[] = $cptData->noPostsFoundMessage;
        }

        if ($cptData->loadMoreButtonLabel) {
            $labels[] = $cptData->loadMoreButtonLabel;
        }

        return $labels ?
            [
                $this->plugin->getThemeTextDomain() => $labels,
            ] :
            [];
    }

    protected function updateQueryPreview(CardData $acfCardData): void
    {
        $acfCardData->queryPreview = print_r($this->queryBuilder->getQueryArgs($acfCardData, 1), true);
    }

    protected function updateMarkup(CardData $acfCardData): void
    {
        $acfCardData->markup = $this->cardMarkup->getMarkup($acfCardData, false, true);
    }

    protected function addLayoutCSS(CardData $acfCardData): void
    {
        $layoutCSS = $this->cardMarkup->getLayoutCSS($acfCardData);

        if (!$layoutCSS) {
            return;
        }

        $acfCardData->cssCode = false === strpos($acfCardData->cssCode, '/*BEGIN LAYOUT_RULES*/') ?
            ($acfCardData->cssCode . "\n" . $layoutCSS . "\n") :
            preg_replace(
                '|\/\*BEGIN LAYOUT_RULES\*\/(.*\s)+\/\*END LAYOUT_RULES\*\/|',
                $layoutCSS,
                $acfCardData->cssCode
            );
    }

    /**
     * @param int|string $postId
     *
     * @return void
     */
    public function performSaveActions($postId): void
    {
        if (!$this->isMyPost($postId)) {
            return;
        }

        $acfCardData = $this->cache->getAcfCardData($postId);

        $this->updateQueryPreview($acfCardData);
        $this->updateMarkup($acfCardData);
        $this->addLayoutCSS($acfCardData);
        $this->updateTranslationsFile($acfCardData);
        $this->maybeSetUniqueId($acfCardData, 'card_');

        $acfCardData->saveToPostContent();
    }

    public function refreshAjax(): void
    {
        $cardId = (int)($_POST['_postId'] ?? 0);
        $isWithShortcode = isset($_POST['_withShortcode']);

        $postType = get_post($cardId)->post_type ?? '';

        if ($this->getCptName() !== $postType) {
            echo "Post id is wrong";
            exit;
        }

        $response = '';

        $acfCardData = $this->cache->getAcfCardData($cardId);

        // ignore customMarkup (we need the preview)
        $markup = $this->cardMarkup->getMarkup($acfCardData, false, true);

        $response .= sprintf('<div class="markup">%s</div>', $markup);

        if ($isWithShortcode) {
            $shortcodes = $this->html->postboxShortcodes(
                $acfCardData->getUniqueId(true),
                false,
                Shortcodes::SHORTCODE_CARDS,
                get_the_title($cardId),
                true
            );
            $response .= sprintf('<div class="shortcode">%s</div>', $shortcodes);
        }

        $response .= '<div class="elements">';
        $response .= sprintf(
            '<div data-selector="#acf-cards_related_view .inside">%s</div>',
            $this->cardsMetaBoxes->printRelatedAcfViewMetaBox(get_post($cardId), true)
        );
        $response .= '</div>';

        echo $response;

        exit;
    }

    public function setHooks(): void
    {
        parent::setHooks();

        add_action('wp_ajax_acf_views__card_refresh', [$this, 'refreshAjax',]);
    }
}