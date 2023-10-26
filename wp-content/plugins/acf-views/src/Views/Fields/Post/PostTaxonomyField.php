<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields\Post;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\Acf\TaxonomyField;
use org\wplake\acf_views\Views\Fields\CustomField;
use org\wplake\acf_views\Views\Fields\Fields;
use org\wplake\acf_views\Views\Fields\MarkupField;

defined('ABSPATH') || exit;

class PostTaxonomyField extends MarkupField
{
    use CustomField;

    protected TaxonomyField $taxonomyField;

    public function __construct(TaxonomyField $taxonomyField)
    {
        $this->taxonomyField = $taxonomyField;
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
        return $this->taxonomyField->getMarkup(
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
        $post = $this->getPost($notFormattedValue);

        if (!$post) {
            return $this->taxonomyField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, [], []);
        }

        $taxonomyName = substr($fieldMeta->getFieldId(), strlen(Fields::TAXONOMY_PREFIX));
        $postTerms = get_the_terms($post, $taxonomyName);

        if (false === $postTerms ||
            is_wp_error($postTerms)) {
            return $this->taxonomyField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, [], []);
        }

        $termIds = array_column($postTerms, 'term_id');

        return $this->taxonomyField->getTwigArgs($acfViewData, $item, $field, $fieldMeta, $termIds, $termIds);
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $this->taxonomyField->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
    }
}
