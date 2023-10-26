<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;

defined('ABSPATH') || exit;

abstract class MarkupField
{
    abstract public function getMarkup(
        ViewData $acfViewData,
        string $fieldId,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        int $tabsNumber,
        bool $isWithFieldWrapper,
        bool $isWithRowWrapper
    ): string;

    abstract public function getTwigArgs(
        ViewData $acfViewData,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        $notFormattedValue,
        $formattedValue
    ): array;

    abstract public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool;

    public function isWithRowWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $field->label;
    }

    protected function getFieldClass(
        string $suffix,
        ViewData $acfViewData,
        FieldData $field,
        bool $isWithFieldWrapper,
        bool $isWithRowWrapper
    ): string {
        $classes = [];
        $isFirstTag = !$isWithRowWrapper &&
            !$isWithFieldWrapper;

        if ($isFirstTag) {
            $classes[] = $acfViewData->getBemName() . '__' . $field->id;

            if (!$acfViewData->isWithCommonClasses) {
                return implode(' ', $classes);
            }
        }

        $classes[] = $this->getItemClass($suffix, $acfViewData, $field);

        if (!$isWithFieldWrapper &&
            $acfViewData->isWithCommonClasses) {
            $classes[] = $acfViewData->getBemName() . '__field';
        }

        return implode(' ', $classes);
    }

    protected function getItemClass(string $suffix, ViewData $acfViewData, FieldData $field): string
    {
        $classes = [];

        $classes[] = $acfViewData->getBemName() . '__' . $field->id . '-' . $suffix;

        if ($acfViewData->isWithCommonClasses) {
            $classes[] = $acfViewData->getBemName() . '__' . $suffix;
        }

        return implode(' ', $classes);
    }
}
