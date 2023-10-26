<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class UserField extends MarkupField
{
    protected LinkField $linkField;

    public function __construct(LinkField $linkField)
    {
        $this->linkField = $linkField;
    }

    protected function getUserInfo(int $id): array
    {
        $postInfo = [
            'url' => '',
            'title' => '',
        ];

        $user = get_user_by('ID', $id);

        if (!$user) {
            return $postInfo;
        }

        return [
            'url' => (string)get_author_posts_url($user->ID),
            'title' => $user->display_name,
        ];
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

        if ($fieldMeta->isMultiple()) {
            $markup .= "\r\n" . str_repeat("\t", $tabsNumber);
            $markup .= sprintf("{%% for user_item in %s.value %%}", esc_html($fieldId));

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

        $fieldTabsNumber = $fieldMeta->isMultiple() ?
            $tabsNumber + 1 :
            $tabsNumber;

        $markup .= $this->linkField->getMarkup(
            $acfViewData,
            $fieldMeta->isMultiple() ?
                'user_item' :
                $fieldId,
            $item,
            $field,
            $fieldMeta,
            $fieldTabsNumber,
            $isWithFieldWrapper,
            $isWithRowWrapper
        );

        $markup .= str_repeat("\t", $tabsNumber);

        if ($fieldMeta->isMultiple()) {
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
            $notFormattedValue = $fieldMeta->isMultiple() ?
                (array)$notFormattedValue :
                (int)$notFormattedValue;
        }

        if (!$notFormattedValue) {
            // it's a single item, so merge, not assign to the 'value' key
            if (!$fieldMeta->isMultiple()) {
                $args = array_merge(
                    $args,
                    $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, [], [])
                );
            }

            return $args;
        }

        if ($fieldMeta->isMultiple()) {
            foreach ($notFormattedValue as $value) {
                $linkArgs = $this->getUserInfo((int)$value);
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
            $linkArgs = $this->getUserInfo($notFormattedValue);
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
            $fieldMeta->isMultiple() ||
            $this->linkField->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
    }
}
