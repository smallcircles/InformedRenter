<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class UrlField extends MarkupField
{
    protected LinkField $linkField;

    public function __construct(LinkField $linkField)
    {
        $this->linkField = $linkField;
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
        return $this->linkField->getMarkup(
            $acfViewData,
            $fieldId,
            $item,
            $field,
            $fieldMeta,
            $tabsNumber,
            $isWithFieldWrapper,
            $isWithRowWrapper
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
        $notFormattedValue = $notFormattedValue ?
            (string)$notFormattedValue :
            '';

        if (!$notFormattedValue) {
            return $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, [], []);
        }

        $fieldArgs = [
            'url' => $notFormattedValue,
            'title' => $field->getLabelTranslation(),
        ];

        return $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, $fieldArgs, $fieldArgs);
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $this->linkField->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
    }
}
