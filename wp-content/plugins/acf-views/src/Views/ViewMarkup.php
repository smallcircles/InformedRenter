<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views;

use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Views\Fields\Fields;

defined('ABSPATH') || exit;

class ViewMarkup
{
    // cache
    protected array $markups;
    protected Html $html;
    protected Fields $fields;

    public function __construct(Html $html, Fields $fields)
    {
        $this->html = $html;
        $this->fields = $fields;
        $this->markups = [];
    }

    protected function getRowMarkup(ViewData $acfViewData, FieldMeta $fieldMeta, ItemData $item, int $viewId): string
    {
        if (in_array($fieldMeta->getType(), ['repeater', 'group',], true) &&
            !$this->fields->isFieldInstancePresent($fieldMeta->getType())) {
            return '';
        }

        $fieldId = $item->field->getTwigFieldId();
        $orCondition = $item->field->isVisibleWhenEmpty || 'true_false' === $fieldMeta->getType() ?
            ' or true' :
            '';
        $isWithRow = $this->fields->isWithRowWrapper($acfViewData, $item->field, $fieldMeta);
        $isWithFieldWrapper = $this->fields->isWithFieldWrapper($acfViewData, $item->field, $fieldMeta);

        $rowTabsNumber = 2;
        $fieldTabsNumber = 3;

        if ($isWithFieldWrapper) {
            $fieldTabsNumber++;
        }

        if (!$isWithRow) {
            $rowTabsNumber--;
            $fieldTabsNumber--;
        }

        $fieldMarkup = $this->fields->getFieldMarkup(
            $acfViewData,
            $item,
            $item->field,
            $fieldMeta,
            $fieldTabsNumber
        );

        $rowType = 'row';

        if (in_array($fieldMeta->getType(), ['repeater', 'group',], true)) {
            $rowType = $fieldMeta->getType();
        }

        return sprintf("\r\n\t{%% if %s.value%s %%}\r\n", esc_html($fieldId), $orCondition) .
            $this->fields->getRowMarkup(
                $rowType,
                '',
                $fieldMarkup,
                $acfViewData,
                $item->field,
                $fieldMeta,
                $rowTabsNumber,
                $fieldId
            ) .
            "\t{% endif %}\r\n\r\n";
    }

    protected function getMarkupFromCache(ViewData $view, bool $isSkipCache): string
    {
        if (key_exists($view->getSource(), $this->markups) &&
            !$isSkipCache) {
            return $this->markups[$view->getSource()];
        }

        $fieldsMeta = $view->getFieldsMeta();
        // e.g. already filled for cache/tests
        if (!$fieldsMeta) {
            $view->setFieldsMeta();
            $fieldsMeta = $view->getFieldsMeta();
        }

        $content = '';
        foreach ($view->items as $item) {
            $content .= $this->getRowMarkup(
                $view,
                $fieldsMeta[$item->field->getAcfFieldId()],
                $item,
                $view->getSource()
            );
        }

        return $this->html->view($view->getSource(), $view->cssClasses, $content, $view->getBemName());
    }

    public function getMarkup(
        ViewData $view,
        int $pageId,
        string $viewMarkup = '',
        bool $isSkipCache = false,
        bool $isIgnoreCustomMarkup = false
    ): string {
        $viewMarkup = ($viewMarkup ||
            $isIgnoreCustomMarkup) ?
            $viewMarkup :
            trim($view->customMarkup);

        $viewMarkup = $viewMarkup ?: $this->getMarkupFromCache($view, $isSkipCache);
        $this->markups[$view->getSource()] = $viewMarkup;

        return $viewMarkup;
    }
}
