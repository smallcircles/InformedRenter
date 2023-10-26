<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class TaxonomyField extends MarkupField
{
    protected LinkField $linkField;

    public function __construct(LinkField $linkField)
    {
        $this->linkField = $linkField;
    }

    protected function getTermInfo(int $id): array
    {
        $postInfo = [
            'url' => '',
            'title' => '',
        ];

        $term = get_term($id);

        if (!$term ||
            is_wp_error($term)) {
            return $postInfo;
        }

        return [
            'url' => (string)get_term_link($term),
            // decode to avoid double encoding in Twig
            'title' => html_entity_decode($term->name, ENT_QUOTES),
        ];
    }

    protected function isMultiple(FieldMeta $fieldMeta): bool
    {
        return in_array($fieldMeta->getAppearance(), ['checkbox', 'multi_select',], true);
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
        $markup = '';

        if ($this->isMultiple($fieldMeta)) {
            $markup .= "\r\n" . str_repeat("\t", $tabsNumber);
            $markup .= sprintf("{%% for term_item in %s.value %%}", esc_html($fieldId));

            $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 1);

            if ($field->optionsDelimiter) {
                $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 1);
                $markup .= "{% if true != loop.first %}";

                $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 2);

                $markup .= sprintf(
                    '<span class="%s">',
                    esc_html($this->getItemClass('delimiter', $acfViewData, $field))
                );
                $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 3);
                $markup .= sprintf("{{ %s.options_delimiter }}", esc_html($fieldId));
                $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 2);
                $markup .= "</span>";

                $markup .= "\r\n" . str_repeat("\t", $tabsNumber + 1);

                $markup .= "{% endif %}\r\n\r\n" . str_repeat("\t", $tabsNumber + 1);
            }
        }

        $fieldTabsNumber = $this->isMultiple($fieldMeta) ?
            $tabsNumber + 1 :
            $tabsNumber;

        $markup .= $this->linkField->getMarkup(
            $acfViewData,
            $this->isMultiple($fieldMeta) ?
                'term_item' :
                $fieldId,
            $item,
            $field,
            $fieldMeta,
            $fieldTabsNumber,
            $this->isMultiple($fieldMeta) || $isWithFieldWrapper,
            $isWithRowWrapper
        );

        $markup .= str_repeat("\t", $tabsNumber);

        if ($this->isMultiple($fieldMeta)) {
            $markup .= "\r\n";
            $markup .= str_repeat("\t", $tabsNumber);
            $markup .= "{% endfor %}\r\n";
        }

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
            'options_delimiter' => $field->optionsDelimiter,
        ];

        if ($notFormattedValue) {
            $notFormattedValue = $this->isMultiple($fieldMeta) ?
                (array)$notFormattedValue :
                (int)$notFormattedValue;
        }

        if (!$notFormattedValue) {
            // it's a single item, so merge, not assign to the 'value' key
            if (!$this->isMultiple($fieldMeta)) {
                $args = array_merge(
                    $args,
                    $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, [], [])
                );
            }

            return $args;
        }

        if ($this->isMultiple($fieldMeta)) {
            foreach ($notFormattedValue as $value) {
                $linkArgs = $this->getTermInfo((int)$value);
                $args['value'][] = $this->linkField->getTwigArgs(
                    $acfViewData,
                    $item,
                    $field,
                    $fieldMeta,
                    $linkArgs,
                    $linkArgs
                );
            }
        } else {
            $linkArgs = $this->getTermInfo($notFormattedValue);
            // it's a single item, so merge, not assign to the 'value' key
            $args = array_merge(
                $args,
                $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, $linkArgs, $linkArgs)
            );
        }

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $this->isMultiple($fieldMeta) ||
            $this->linkField->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
    }
}
