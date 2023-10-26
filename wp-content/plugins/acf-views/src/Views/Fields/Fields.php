<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\Fields\Acf\ColorPickerField;
use org\wplake\acf_views\Views\Fields\Acf\DatePickerField;
use org\wplake\acf_views\Views\Fields\Acf\FileField;
use org\wplake\acf_views\Views\Fields\Acf\GalleryField;
use org\wplake\acf_views\Views\Fields\Acf\GoogleMapField;
use org\wplake\acf_views\Views\Fields\Acf\ImageField;
use org\wplake\acf_views\Views\Fields\Acf\LinkField;
use org\wplake\acf_views\Views\Fields\Acf\PageLinkField;
use org\wplake\acf_views\Views\Fields\Acf\PostObjectField;
use org\wplake\acf_views\Views\Fields\Acf\SelectField;
use org\wplake\acf_views\Views\Fields\Acf\TaxonomyField;
use org\wplake\acf_views\Views\Fields\Acf\TrueFalseField;
use org\wplake\acf_views\Views\Fields\Acf\UrlField;
use org\wplake\acf_views\Views\Fields\Acf\UserField;
use org\wplake\acf_views\Views\Fields\Post\PostAuthorField;
use org\wplake\acf_views\Views\Fields\Post\PostContentField;
use org\wplake\acf_views\Views\Fields\Post\PostDateField;
use org\wplake\acf_views\Views\Fields\Post\PostExcerptField;
use org\wplake\acf_views\Views\Fields\Post\PostModifiedField;
use org\wplake\acf_views\Views\Fields\Post\PostTaxonomyField;
use org\wplake\acf_views\Views\Fields\Post\PostThumbnailField;
use org\wplake\acf_views\Views\Fields\Post\PostThumbnailLinkField;
use org\wplake\acf_views\Views\Fields\Post\PostTitleField;
use org\wplake\acf_views\Views\Fields\Post\PostTitleLinkField;
use org\wplake\acf_views\Views\Fields\User\UserAuthorLinkField;
use org\wplake\acf_views\Views\Fields\User\UserBioField;
use org\wplake\acf_views\Views\Fields\User\UserDisplayNameField;
use org\wplake\acf_views\Views\Fields\User\UserEmailField;
use org\wplake\acf_views\Views\Fields\User\UserFirstNameField;
use org\wplake\acf_views\Views\Fields\User\UserLastNameField;
use org\wplake\acf_views\Views\Fields\Woo\WooGalleryField;
use org\wplake\acf_views\Views\Fields\Woo\WooHeightField;
use org\wplake\acf_views\Views\Fields\Woo\WooLengthField;
use org\wplake\acf_views\Views\Fields\Woo\WooPriceField;
use org\wplake\acf_views\Views\Fields\Woo\WooRegularPriceField;
use org\wplake\acf_views\Views\Fields\Woo\WooSalePriceField;
use org\wplake\acf_views\Views\Fields\Woo\WooSkuField;
use org\wplake\acf_views\Views\Fields\Woo\WooStockStatusField;
use org\wplake\acf_views\Views\Fields\Woo\WooWeightField;
use org\wplake\acf_views\Views\Fields\Woo\WooWidthField;

defined('ABSPATH') || exit;

class Fields
{
    // all fields have ids like 'field_x', so no conflicts possible
    const GROUP_POST = '$post$';
    const GROUP_USER = '$user$';
    const GROUP_WOO = '$woo$';
    const GROUP_TAXONOMY = '$taxonomy$';

    // all fields have ids like 'field_x', so no conflicts possible
    const POST_GROUP_PREFIX = '_post_';
    const FIELD_POST_TITLE = '_post_title';
    const FIELD_POST_TITLE_LINK = '_post_title_link';
    const FIELD_POST_THUMBNAIL = '_post_thumbnail';
    const FIELD_POST_THUMBNAIL_LINK = '_post_thumbnail_link';
    const FIELD_POST_AUTHOR = '_post_author';
    const FIELD_POST_DATE = '_post_date';
    const FIELD_POST_MODIFIED = '_post_modified';
    const FIELD_POST_CONTENT = '_post_content';
    const FIELD_POST_EXCERPT = '_post_excerpt';
    const FIELD_POST_TAXONOMY = '_post_taxonomy';

    // all fields have ids like 'field_x', so no conflicts possible
    const USER_GROUP_PREFIX = '_user_';
    const FIELD_USER_FIRST_NAME = '_user_first_name';
    const FIELD_USER_LAST_NAME = '_user_last_name';
    const FIELD_USER_DISPLAY_NAME = '_user_display_name';
    const FIELD_USER_EMAIL = '_user_email';
    const FIELD_USER_BIO = '_user_bio';
    const FIELD_USER_AUTHOR_LINK = '_user_author_link';

    // all fields have ids like 'field_x', so no conflicts possible
    const WOO_GROUP_PREFIX = '_woo_';
    const FIELD_WOO_GALLERY = '_woo_gallery';
    const FIELD_WOO_PRICE = '_woo_price';
    const FIELD_WOO_REGULAR_PRICE = '_woo_regular_price';
    const FIELD_WOO_SALE_PRICE = '_woo_sale_price';
    const FIELD_WOO_SKU = '_woo_sku';
    const FIELD_WOO_STOCK_STATUS = '_woo_stock_status';
    const FIELD_WOO_WEIGHT = '_woo_weight';
    const FIELD_WOO_LENGTH = '_woo_length';
    const FIELD_WOO_WIDTH = '_woo_width';
    const FIELD_WOO_HEIGHT = '_woo_height';


    const TAXONOMY_PREFIX = '_taxonomy_';

    /**
     * @var MarkupField[]
     */
    protected array $fields;

    public function __construct()
    {
        $imageField = new ImageField();
        $selectField = new SelectField();
        $linkField = new LinkField();
        $postObjectField = new PostObjectField($linkField);
        $datePickerField = new DatePickerField();
        $taxonomyField = new TaxonomyField($linkField);
        $postContent = new PostContentField();

        $this->fields = [
            //// basic
            'url' => new UrlField($linkField),

            //// content types
            'image' => $imageField,
            'file' => new FileField($linkField),
            'gallery' => new GalleryField($imageField),

            //// choice types
            'select' => $selectField,
            'checkbox' => $selectField,
            'radio' => $selectField,
            'button_group' => $selectField,
            'true_false' => new TrueFalseField(),

            //// relational types
            'link' => $linkField,
            'page_link' => new PageLinkField($linkField),
            'post_object' => $postObjectField,
            'relationship' => $postObjectField,
            'taxonomy' => $taxonomyField,
            'user' => new UserField($linkField),

            //// jquery types
            'google_map' => new GoogleMapField(),
            'date_picker' => $datePickerField,
            'date_time_picker' => $datePickerField,
            'time_picker' => $datePickerField,
            'color_picker' => new ColorPickerField(),

            //// post
            self::FIELD_POST_TITLE => new PostTitleField(),
            self::FIELD_POST_TITLE_LINK => new PostTitleLinkField($linkField),
            self::FIELD_POST_THUMBNAIL => new PostThumbnailField($imageField),
            self::FIELD_POST_THUMBNAIL_LINK => new PostThumbnailLinkField($imageField),
            self::FIELD_POST_AUTHOR => new PostAuthorField($linkField),
            self::FIELD_POST_DATE => new PostDateField(),
            self::FIELD_POST_MODIFIED => new PostModifiedField(),
            self::FIELD_POST_CONTENT => $postContent,
            self::FIELD_POST_EXCERPT => new PostExcerptField(),
            self::FIELD_POST_TAXONOMY => new PostTaxonomyField($taxonomyField),

            //// user
            self::FIELD_USER_FIRST_NAME => new UserFirstNameField(),
            self::FIELD_USER_LAST_NAME => new UserLastNameField(),
            self::FIELD_USER_DISPLAY_NAME => new UserDisplayNameField(),
            self::FIELD_USER_EMAIL => new UserEmailField(),
            self::FIELD_USER_BIO => new UserBioField(),
            self::FIELD_USER_AUTHOR_LINK => new UserAuthorLinkField($linkField),

            //// woo
            self::FIELD_WOO_PRICE => new WooPriceField(),
            self::FIELD_WOO_REGULAR_PRICE => new WooRegularPriceField(),
            self::FIELD_WOO_SALE_PRICE => new WooSalePriceField(),
            self::FIELD_WOO_SKU => new WooSkuField(),
            self::FIELD_WOO_STOCK_STATUS => new WooStockStatusField(),
            self::FIELD_WOO_GALLERY => new WooGalleryField($imageField),
            self::FIELD_WOO_WEIGHT => new WooWeightField(),
            self::FIELD_WOO_LENGTH => new WooLengthField(),
            self::FIELD_WOO_WIDTH => new WooWidthField(),
            self::FIELD_WOO_HEIGHT => new WooHeightField(),
        ];
    }

    protected function applyFieldMarkupFilter(
        string $fieldMarkup,
        FieldMeta $fieldMeta,
        string $shortUniqueViewId
    ): string {
        $fieldMarkup = (string)apply_filters(
            'acf_views/view/field_markup',
            $fieldMarkup,
            $fieldMeta,
            $shortUniqueViewId
        );
        $fieldMarkup = (string)apply_filters(
            'acf_views/view/field_markup/name=' . $fieldMeta->getName(),
            $fieldMarkup,
            $fieldMeta,
            $shortUniqueViewId
        );

        if (!$fieldMeta->isCustomType()) {
            $fieldMarkup = (string)apply_filters(
                'acf_views/view/field_markup/type=' . $fieldMeta->getType(),
                $fieldMarkup,
                $fieldMeta,
                $shortUniqueViewId
            );
        }

        return (string)apply_filters(
            'acf_views/view/field_markup/view_id=' . $shortUniqueViewId,
            $fieldMarkup,
            $fieldMeta,
            $shortUniqueViewId
        );
    }

    protected function applyFieldDataFilter(array $fieldData, FieldMeta $fieldMeta, string $shortUniqueViewId): array
    {
        $fieldData = (array)apply_filters(
            'acf_views/view/field_data',
            $fieldData,
            $fieldMeta,
            $shortUniqueViewId
        );

        if (!$fieldMeta->isCustomType()) {
            $fieldData = (array)apply_filters(
                'acf_views/view/field_data/type=' . $fieldMeta->getType(),
                $fieldData,
                $fieldMeta,
                $shortUniqueViewId
            );
        }

        $fieldData = (array)apply_filters(
            'acf_views/view/field_data/name=' . $fieldMeta->getName(),
            $fieldData,
            $fieldMeta,
            $shortUniqueViewId
        );

        return (array)apply_filters(
            'acf_views/view/field_data/view_id=' . $shortUniqueViewId,
            $fieldData,
            $fieldMeta,
            $shortUniqueViewId
        );
    }

    public function getFieldMarkup(
        ViewData $acfViewData,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        int $tabsNumber,
        string $fieldIdPrefix = ''
    ): string {
        $fieldId = $fieldIdPrefix . $field->getTwigFieldId();
        $fieldType = $fieldMeta->getType();

        if (!$fieldMeta->isFieldExist()) {
            return '';
        }

        $fieldMarkup = '';
        $isWithWrapper = $this->isWithFieldWrapper($acfViewData, $field, $fieldMeta);

        if (!isset($this->fields[$fieldType]) ||
            !$this->fields[$fieldType] instanceof MarkupField) {
            // disable Twig escaping for wysiwyg, oembed. HTML is expected there. For textarea it's '<br>'
            $filter = in_array($fieldType, ['wysiwyg', 'oembed', 'textarea',], true) ?
                '|raw' :
                '';

            $fieldMarkup .= "\r\n";
            $fieldMarkup .= str_repeat("\t", $tabsNumber);
            $fieldMarkup .= sprintf('{{ %s.value%s }}', esc_html($fieldId), esc_html($filter));
            $fieldMarkup .= "\r\n";
        } else {
            if ($isWithWrapper) {
                $fieldMarkup .= "\r\n";
            }

            $fieldMarkup .= str_repeat("\t", $tabsNumber) .
                $this->fields[$fieldType]->getMarkup(
                    $acfViewData,
                    $fieldId,
                    $item,
                    $field,
                    $fieldMeta,
                    $tabsNumber,
                    $isWithWrapper,
                    $this->isWithRowWrapper($acfViewData, $field, $fieldMeta)
                ) .
                "\r\n";
        }

        return $this->applyFieldMarkupFilter($fieldMarkup, $fieldMeta, $acfViewData->getUniqueId(true));
    }

    public function isWithFieldWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        $fieldType = $fieldMeta->getType();

        if (!$fieldMeta->isFieldExist()) {
            return false;
        }

        if (!isset($this->fields[$fieldType]) ||
            !$this->fields[$fieldType] instanceof MarkupField) {
            return true;
        }

        return $this->fields[$fieldType]->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
    }

    public function isWithRowWrapper(ViewData $acfViewData, FieldData $field, FieldMeta $fieldMeta): bool
    {
        return $acfViewData->isWithUnnecessaryWrappers ||
            $field->label ||
            in_array($fieldMeta->getType(), ['repeater', 'group',], true);
    }

    public function getRowMarkup(
        string $type,
        string $rowSuffix,
        string $fieldHtml,
        ViewData $acfViewData,
        FieldData $field,
        FieldMeta $fieldMeta,
        int $tabsNumber,
        string $fieldId
    ): string {
        $rowMarkup = '';
        $isWithRowWrapper = $this->isWithRowWrapper($acfViewData, $field, $fieldMeta);
        $isWithFieldWrapper = $this->isWithFieldWrapper($acfViewData, $field, $fieldMeta);
        $fieldNameClass = $acfViewData->getBemName() . '__' . $field->id . $rowSuffix;

        if ($isWithRowWrapper) {
            $rowClasses = $fieldNameClass;

            if ($acfViewData->isWithCommonClasses) {
                $rowClasses .= ' ' . $acfViewData->getBemName() . '__' . $type;
            }

            $rowMarkup .= str_repeat("\t", $tabsNumber);
            $rowMarkup .= sprintf("<div class=\"%s\">", esc_html($rowClasses));
            $rowMarkup .= "\r\n";
        }

        if ($field->label) {
            $rowMarkup .= str_repeat("\t", $tabsNumber + 1);

            $labelClass = $acfViewData->getBemName() . '__' . $field->id . '-label';

            $labelClass .= $acfViewData->isWithCommonClasses ?
                ' ' . $acfViewData->getBemName() . '__label' :
                '';

            $rowMarkup .= sprintf("<div class=\"%s\">", esc_html($labelClass));
            $rowMarkup .= "\r\n" . str_repeat("\t", $tabsNumber + 2);
            $rowMarkup .= sprintf('{{ %s.label }}', esc_html($fieldId));
            $rowMarkup .= "\r\n" . str_repeat("\t", $tabsNumber + 1);
            $rowMarkup .= "</div>";
            $rowMarkup .= "\r\n";
        }

        if ($isWithFieldWrapper) {
            $rowMarkup .= str_repeat("\t", $tabsNumber + 1);
            $fieldClasses = '';

            if ($isWithRowWrapper) {
                $fieldClasses .= $acfViewData->getBemName() . '__' . $field->id . '-field';
                $fieldClasses .= $acfViewData->isWithCommonClasses ?
                    ' ' . $acfViewData->getBemName() . '__field' :
                    '';
            } else {
                $fieldClasses .= $fieldNameClass;

                if ($acfViewData->isWithCommonClasses) {
                    $fieldClasses .= ' ' . $acfViewData->getBemName() . '__field';
                }
            }

            $rowMarkup .= sprintf(
                "<div class=\"%s\">",
                esc_html($fieldClasses),
            );
        }

        // no escaping for $field, because it's an HTML code (output that have already escaped variables)
        $rowMarkup .= $fieldHtml;

        if ($isWithFieldWrapper) {
            $rowMarkup .= str_repeat("\t", $tabsNumber + 1);
            $rowMarkup .= "</div>";
            $rowMarkup .= "\r\n";
        }

        if ($isWithRowWrapper) {
            $rowMarkup .= str_repeat("\t", $tabsNumber);
            $rowMarkup .= "</div>";
            $rowMarkup .= "\r\n";
        }

        return $rowMarkup;
    }

    public function getFieldTwigArgs(
        ViewData $acfViewData,
        ItemData $item,
        FieldData $field,
        FieldMeta $fieldMeta,
        $notFormattedValue,
        $formattedValue
    ): array {
        $fieldType = $fieldMeta->getType();

        if (!isset($this->fields[$fieldType]) ||
            !$this->fields[$fieldType] instanceof MarkupField) {
            $formattedValue = (string)$formattedValue;

            $formattedValue = 'textarea' === $fieldType ?
                str_replace("\n", "<br/>", $formattedValue) :
                $formattedValue;

            $fieldData = [
                'value' => $formattedValue,
            ];
        } else {
            $fieldData = $this->fields[$fieldType]->getTwigArgs(
                $acfViewData,
                $item,
                $field,
                $fieldMeta,
                $notFormattedValue,
                $formattedValue
            );
        }

        return $this->applyFieldDataFilter($fieldData, $fieldMeta, $acfViewData->getUniqueId(true));
    }

    public function isFieldInstancePresent(string $fieldType): bool
    {
        return key_exists($fieldType, $this->fields);
    }
}
