<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Post;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\CustomField;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class PostContentField extends MarkupField
{
    use CustomField;

    public function getMarkup(
        ViewData $acfViewData,
        string $fieldId,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        int $tabsNumber,
        bool $isWithFieldWrapper,
        bool $isWithRowWrapper
    ): string {
        return sprintf(
            "{{ %s.value|raw }}",
            esc_html($fieldId),
        );
    }

    public function getTwigArgs(
        ViewData $acfViewData,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        $notFormattedValue,
        $formattedValue
    ): array {
        $args = [
            'value' => '',
        ];

        $post = $this->getPost($notFormattedValue);

        if (!$post) {
            return $args;
        }

        $content = $post->post_content;
        // to avoid double escaping
        $content = html_entity_decode($content, ENT_QUOTES);


        $args['value'] = $content;

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return true;
    }
}
