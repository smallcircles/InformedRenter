<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class GoogleMapField extends MarkupField
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
        $markup = sprintf(
            '<div class="%s" style="width:100%%;height:400px;" data-zoom="{{ %s.zoom }}">',
            esc_html(
                $this->getFieldClass('map', $acfViewData, $field, $isWithFieldWrapper, $isWithRowWrapper)
            ),
            esc_html($fieldId),
        );
        $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 1);
        $markup .= sprintf(
            '<div class="%s" data-lat="{{ %s.lat }}" data-lng="{{ %s.lng }}"></div>',
            esc_html($this->getItemClass('map-marker', $acfViewData, $field)),
            esc_html($fieldId),
            esc_html($fieldId),
        );
        $markup .= "\r\n" . str_repeat("\t", $tabsNumber);
        $markup .= '</div>';

        return $markup;
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
            'zoom' => 0,
            'lat' => 0,
            'lng' => 0,
        ];

        $notFormattedValue = $notFormattedValue ?
            (array)$notFormattedValue :
            [];

        if (!$notFormattedValue) {
            return $args;
        }

        $args['value'] = !!($notFormattedValue['lat'] ?? '');
        $args['zoom'] = (string)($notFormattedValue['zoom'] ?? '16');
        $args['lat'] = (string)($notFormattedValue['lat'] ?? '');
        $args['lng'] = (string)($notFormattedValue['lng'] ?? '');

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers;
    }
}
