<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class ImageField extends MarkupField
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
            '<img class="%s" src="{{ %s.value }}" width="{{ %s.width }}" height="{{ %s.height }}" alt="{{ %s.alt }}" decoding="{{ %s.decoding }}" loading="{{ %s.loading }}" srcset="{{ %s.srcset }}" sizes="{{ %s.sizes }}"',
            esc_html(
                $this->getFieldClass(
                    'image',
                    $acfViewData,
                    $field,
                    $isWithFieldWrapper,
                    $isWithRowWrapper
                )
            ),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
            esc_html($fieldId),
        );

        if ($fieldMeta->getCustomArg('isWithFullSizeInData')) {
            $markup .= sprintf(' data-full-size="{{ %s.full_size }}"', esc_html($fieldId));
        }

        $markup .= '>';

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
            'width' => 0,
            'height' => 0,
            'value' => '',
            'alt' => '',
            'srcset' => '',
            'sizes' => '',
            'decoding' => 'async',
            'loading' => 'lazy',
            'full_size' => '',
        ];
        $imageSize = $field->imageSize ?: 'full';

        $notFormattedValue = $notFormattedValue ?
            (int)$notFormattedValue :
            0;

        if (!$notFormattedValue) {
            return $args;
        }

        $imageData = (array)(wp_get_attachment_image_src($notFormattedValue, $imageSize) ?: []);
        $imageSrc = (string)($imageData[0] ?? '');
        $width = (int)($imageData[1] ?? 0);
        $height = (int)($imageData[2] ?? 0);

        if (!$imageSrc) {
            return $args;
        }

        $args['width'] = $width;
        $args['height'] = $height;
        $args['value'] = $imageSrc;
        $args['alt'] = (string)get_post_meta($notFormattedValue, '_wp_attachment_image_alt', true);

        if ($fieldMeta->getCustomArg('isWithFullSizeInData')) {
            $args['full_size'] = (string)wp_get_attachment_image_url($notFormattedValue, 'full');
        }

        $imageMeta = wp_get_attachment_metadata($notFormattedValue);

        if (!is_array($imageMeta)) {
            return $args;
        }

        $sizesArray = [absint($width), absint($height)];
        $srcSet = (string)wp_calculate_image_srcset($sizesArray, $imageSrc, $imageMeta, $notFormattedValue);
        $sizes = (string)wp_calculate_image_sizes($sizesArray, $imageSrc, $imageMeta, $notFormattedValue);

        if (!$srcSet ||
            !$sizes) {
            return $args;
        }

        $args['srcset'] = $srcSet;
        $args['sizes'] = $sizes;

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers;
    }
}
