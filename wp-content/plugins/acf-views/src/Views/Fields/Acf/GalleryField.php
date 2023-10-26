<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class GalleryField extends MarkupField
{
    protected ImageField $imageField;

    public function __construct(ImageField $imageField)
    {
        $this->imageField = $imageField;
    }

    protected function getItemMarkup(
        ViewData $acfViewData,
        string $fieldId,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        int $tabsNumber,
        bool $isWithFieldWrapper,
        bool $isWithRowWrapper
    ): string {
        return $this->imageField->getMarkup(
            $acfViewData,
            'image_item',
            $item,
            $field,
            $fieldMeta,
            $tabsNumber + 1,
            true,
            $isWithRowWrapper
        );
    }

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
        $markup = "\r\n" . str_repeat("\t", $tabsNumber);
        $markup .= sprintf("{%% for image_item in %s.value %%}", esc_html($fieldId));
        $markup .= "\r\n";
        $markup .= str_repeat("\t", $tabsNumber + 1);
        $markup .= $this->getItemMarkup(
            $acfViewData,
            $fieldId,
            $item,
            $field,
            $fieldMeta,
            $tabsNumber,
            $isWithFieldWrapper,
            $isWithRowWrapper
        );
        $markup .= "\r\n";
        $markup .= str_repeat("\t", $tabsNumber);
        $markup .= "{% endfor %}\r\n";

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
            'value' => [],
        ];

        $notFormattedValue = $notFormattedValue ?
            (array)$notFormattedValue :
            [];

        if (!$notFormattedValue) {
            return $args;
        }

        foreach ($notFormattedValue as $image) {
            $args['value'][] = $this->imageField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, $image, $image);
        }

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return true;
    }
}
