<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class SelectField extends MarkupField
{
    protected function isMultiple(FieldMeta $fieldMeta): bool
    {
        return $fieldMeta->isMultiple() ||
            'checkbox' === $fieldMeta->getType();
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
            $markup .= sprintf("{%% for choice_item in %s.value %%}", esc_html($fieldId));

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

        $twigName = $this->isMultiple($fieldMeta) ?
            'choice_item' :
            $fieldId;

        $choiceTabsNumber = $this->isMultiple($fieldMeta) ?
            $tabsNumber + 1 :
            $tabsNumber;

        $markup .= sprintf(
            '<div class="%s">',
            esc_html(
                $this->getFieldClass(
                    'choice',
                    $acfViewData,
                    $field,
                    $this->isMultiple($fieldMeta) || $isWithFieldWrapper,
                    $isWithRowWrapper
                )
            )
        );
        $markup .= "\r\n" . str_repeat("\t", $choiceTabsNumber + 1);
        $markup .= sprintf("{{ %s.title }}", esc_html($twigName));
        $markup .= "\r\n" . str_repeat("\t", $choiceTabsNumber);
        $markup .= "</div>";

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
            'value' => $this->isMultiple($fieldMeta) ?
                [] :
                '',
            'title' => '',
            'options_delimiter' => $field->optionsDelimiter,
        ];

        if ($this->isMultiple($fieldMeta)) {
            $notFormattedValue = $notFormattedValue ?
                (array)$notFormattedValue :
                [];
        } else {
            $notFormattedValue = $notFormattedValue ?
                (string)$notFormattedValue :
                '';
        }

        if (!$notFormattedValue) {
            return $args;
        }

        if ($this->isMultiple($fieldMeta)) {
            foreach ($notFormattedValue as $value) {
                $args['value'][] = [
                    'title' => (string)($fieldMeta->getChoices()[$value] ?? ''),
                    'value' => (string)$value,
                ];
            }
        } else {
            $args['title'] = (string)($fieldMeta->getChoices()[$notFormattedValue] ?? '');
            $args['value'] = $notFormattedValue;
        }

        return $args;
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $this->isMultiple($fieldMeta);
    }
}
