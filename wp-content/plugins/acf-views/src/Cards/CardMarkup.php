<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards;

use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Groups\CardLayoutData;
use org\wplake\acf_views\Shortcodes;

defined('ABSPATH') || exit;

class CardMarkup
{
    protected QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    protected function getExtraMarkup(CardData $acfCardData): string
    {
        return '';
    }

    public function getMarkup(
        CardData $acfCardData,
        bool $isLoadMore = false,
        bool $isIgnoreCustomMarkup = false
    ): string {
        if (!$isIgnoreCustomMarkup &&
            $acfCardData->customMarkup &&
            !$isLoadMore) {
            $customMarkup = trim($acfCardData->customMarkup);

            if ($customMarkup) {
                return $customMarkup;
            }
        }

        $markup = '';

        if (!$isLoadMore) {
            $markup .= sprintf(
                '<div class="{{ _card.classes }}%s %s--id--{{ _card.id }}">',
                esc_html($acfCardData->getBemName()),
                esc_html($acfCardData->getBemName())
            );
            $markup .= "\r\n";
            $markup .= "\r\n\t{% if _card.post_ids %}\r\n";
            $markup .= "\t\t";
            $markup .= sprintf('<div class="%s__items">', esc_html($acfCardData->getBemName()));
            $markup .= "\r\n";
        }

        $markup .= "\t\t\t{% for post_id in _card.post_ids %}\r\n";
        $markup .= sprintf(
            "\t\t\t\t[%s view-id='{{ _card.view_id }}' object-id='{{ post_id }}']\r\n",
            Shortcodes::SHORTCODE_VIEWS,
        );
        $markup .= "\t\t\t{% endfor %}\r\n";

        if (!$isLoadMore) {
            $markup .= "\t\t" . '</div>' . "\r\n";

            if ($acfCardData->noPostsFoundMessage) {
                $markup .= "\t{% else %}\r\n";
                $markup .= "\t\t";
                $markup .= sprintf(
                    '<div class="%s__no-posts-message">{{ _card.no_posts_found_message }}</div>',
                    esc_html($acfCardData->getBemName())
                );
                $markup .= "\r\n";
            }

            // endif in any case
            $markup .= "\t{% endif %}\r\n";

            $markup .= $this->getExtraMarkup($acfCardData);

            $markup .= "\r\n" . '</div>' . "\r\n";
        }

        return $markup;
    }


    public function getLayoutCSS(CardData $acfCardData): string
    {
        if (!$acfCardData->isUseLayoutCss) {
            return '';
        }

        $message = __(
            "Manually edit these rules by disabling Layout Rules, otherwise these rules are updated every time you press the 'Update' button",
            'acf-views'
        );

        $css = "/*BEGIN LAYOUT_RULES*/\n";
        $css .= sprintf("/*%s*/\n", $message);

        $rules = [];

        foreach ($acfCardData->layoutRules as $layoutRule) {
            $screen = 0;
            switch ($layoutRule->screen) {
                case CardLayoutData::SCREEN_TABLET:
                    $screen = 576;
                    break;
                case CardLayoutData::SCREEN_DESKTOP:
                    $screen = 992;
                    break;
                case CardLayoutData::SCREEN_LARGE_DESKTOP:
                    $screen = 1400;
                    break;
            }

            $rule = [];

            $rule[] = ' display:grid;';

            switch ($layoutRule->layout) {
                case CardLayoutData::LAYOUT_ROW:
                    $rule[] = ' grid-auto-flow:column;';
                    $rule[] = sprintf(' grid-column-gap:%s;', $layoutRule->horizontalGap);
                    break;
                case CardLayoutData::LAYOUT_COLUMN:
                    // the right way is 1fr, but use "1fr" because CodeMirror doesn't recognize it, "1fr" should be replaced with 1fr on the output
                    $rule[] = ' grid-template-columns:"1fr";';
                    $rule[] = sprintf(' grid-row-gap:%s;', $layoutRule->verticalGap);
                    break;
                case CardLayoutData::LAYOUT_GRID:
                    $rule[] = sprintf(' grid-template-columns:repeat(%s, "1fr");', $layoutRule->amountOfColumns);
                    $rule[] = sprintf(' grid-column-gap:%s;', $layoutRule->horizontalGap);
                    $rule[] = sprintf(' grid-row-gap:%s;', $layoutRule->verticalGap);
                    break;
            }

            $rules[$screen] = $rule;
        }

        // order is important in media rules
        ksort($rules);

        foreach ($rules as $screen => $rule) {
            if ($screen) {
                $css .= sprintf("\n@media screen and (min-width:%spx) {", $screen);
            }

            $css .= "\n#card .acf-card__items {\n";
            $css .= join("\n", $rule);
            $css .= "\n}\n";

            if ($screen) {
                $css .= "}\n";
            }
        }

        $css .= "\n/*END LAYOUT_RULES*/";

        return $css;
    }
}
