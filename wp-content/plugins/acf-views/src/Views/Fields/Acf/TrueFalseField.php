<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class TrueFalseField extends MarkupField
{
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
        $suffix = sprintf('true-false--state--{{ %s.state }}', esc_html($fieldId));

        return sprintf(
            '<div class="%s %s"></div>',
            esc_html(
                $this->getFieldClass(
                    'true-false',
                    $acfViewData,
                    $field,
                    $isWithFieldWrapper,
                    $isWithRowWrapper
                )
            ),
            esc_html($this->getItemClass($suffix, $acfViewData, $field)),
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
            'value' => !!$formattedValue,
            'state' => !!$formattedValue ?
                'checked' :
                'unchecked',
        ];

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers;
    }
}
