<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Acf;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class PostObjectField extends MarkupField
{
    protected LinkField $linkField;

    public function __construct(LinkField $linkField)
    {
        $this->linkField = $linkField;
    }

    protected function getPostInfo(int $id): array
    {
        $postInfo = [
            'url' => '',
            'title' => '',
        ];

        $post = get_post($id);

        if (!$post) {
            return $postInfo;
        }

        $title = get_the_title($post);

        return [
            'url' => (string)get_permalink($post->ID),
            // avoid double encoding in Twig
            'title' => html_entity_decode($title, ENT_QUOTES),
        ];
    }

    protected function isMultiple(FieldMeta $fieldMeta): bool
    {
        return 'relationship' === $fieldMeta->getType() || $fieldMeta->isMultiple();
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
        return $this->linkField->getMarkup(
            $acfViewData,
            $this->isMultiple($fieldMeta) ?
                'post_item' :
                $fieldId,
            $item,
            $field,
            $fieldMeta,
            $tabsNumber,
            $this->isMultiple($fieldMeta) || $isWithFieldWrapper,
            $isWithRowWrapper
        );
    }

    protected function getItemTwigArgs(
        ViewData $acfViewData,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        $notFormattedValue
    ): array {
        $linkArgs = $this->getPostInfo((int)$notFormattedValue);
        return $this->linkField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, $linkArgs, $linkArgs);
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
            $markup .= sprintf("{%% for post_item in %s.value %%}", esc_html($fieldId));

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

        $itemTabsNumber = $this->isMultiple($fieldMeta) ?
            $tabsNumber + 1 :
            $tabsNumber;
        $markup .= $this->getItemMarkup(
            $acfViewData,
            $fieldId,
            $item,
            $field,
            $fieldMeta,
            $itemTabsNumber,
            $isWithFieldWrapper,
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
                $args['value'][] = $this->getItemTwigArgs($acfViewData, $item, $field, $fieldMeta, $value);
            }
        } else {
            // it's a single item, so merge, not assign to the 'value' key
            $args = array_merge(
                $args,
                $this->getItemTwigArgs($acfViewData, $item, $field, $fieldMeta, $notFormattedValue)
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
