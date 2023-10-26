<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Groups\Integration;

use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\RepeaterFieldData;
use org\wplake\acf_views\Views\Fields\Fields;

defined('ABSPATH') || exit;

class FieldDataIntegration extends AcfIntegration
{
    protected function setConditionalRulesForField(
        array $field,
        string $targetField,
        array $notEqualValues
    ): array {
        // multiple calls of this method are allowed
        if (!isset($field['conditional_logic']) ||
            !is_array($field['conditional_logic'])) {
            $field['conditional_logic'] = [];
        }

        foreach ($notEqualValues as $notEqualValue) {
            // using exactly AND rule (so all rules in one array) and '!=' comparison,
            // otherwise if there are no such fields the field will be visible
            $field['conditional_logic'][] = [
                'field' => $targetField,
                'operator' => '!=',
                'value' => $notEqualValue,
            ];
        }

        return $field;
    }

    protected function addConditionalFilter(
        string $fieldName,
        array $notFieldTypes,
        bool $isSubField = false,
        array $includeFields = []
    ): void {
        $acfFieldName = !$isSubField ?
            FieldData::getAcfFieldName($fieldName) :
            RepeaterFieldData::getAcfFieldName($fieldName);
        $acfKey = !$isSubField ?
            FieldData::getAcfFieldName(FieldData::FIELD_KEY) :
            RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_KEY);

        add_filter(
            'acf/load_field/name=' . $acfFieldName,
            function (array $field) use ($acfKey, $notFieldTypes, $includeFields, $isSubField) {
                // using exactly the negative (excludeTypes) filter,
                // otherwise if there are no such fields the field will be visible
                $notRightFields = !$isSubField ?
                    $this->getFieldChoices(true, $notFieldTypes) :
                    $this->getSubFieldChoices($notFieldTypes);

                foreach ($includeFields as $includeField) {
                    unset($notRightFields[$includeField]);
                }

                return $this->setConditionalRulesForField(
                    $field,
                    $acfKey,
                    array_keys($notRightFields)
                );
            }
        );
    }

    protected function getSubFieldChoices(array $excludeTypes = []): array
    {
        $subFieldChoices = [
            '' => 'Select',
        ];

        $supportedFieldTypes = $this->getFieldTypes();

        $groups = $this->getGroups();
        foreach ($groups as $group) {
            $fields = acf_get_fields($group);

            foreach ($fields as $groupField) {
                $subFields = (array)($groupField['sub_fields'] ?? []);

                if (!in_array($groupField['type'], ['repeater', 'group',], true) ||
                    !$subFields) {
                    continue;
                }

                foreach ($subFields as $subField) {
                    // inner complex types, like repeater or group aren't allowed
                    if (!in_array($subField['type'], $supportedFieldTypes, true) ||
                        in_array($subField['type'], ['repeater', 'group',], true) ||
                        ($excludeTypes && in_array($subField['type'], $excludeTypes, true))) {
                        continue;
                    }

                    $fullFieldId = FieldData::createKey(
                        $group['key'],
                        $groupField['key'],
                        $subField['key']
                    );
                    $subFieldChoices[$fullFieldId] = $subField['label'] . ' (' . $subField['type'] . ')';
                }
            }
        }

        return $subFieldChoices;
    }

    protected function getExtraFieldChoices(array $excludeTypes): array
    {
        $fieldChoices = [];

        $postFields = [
            Fields::FIELD_POST_TITLE => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_TITLE),
                __('Title', 'acf-views')
            ],
            Fields::FIELD_POST_TITLE_LINK => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_TITLE_LINK),
                __('Title with link', 'acf-views')
            ],
            Fields::FIELD_POST_CONTENT => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_CONTENT),
                __('Content', 'acf-views')
            ],
            Fields::FIELD_POST_EXCERPT => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_EXCERPT),
                __('Excerpt', 'acf-views')
            ],
            Fields::FIELD_POST_THUMBNAIL => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_THUMBNAIL),
                __('Featured Image', 'acf-views')
            ],
            Fields::FIELD_POST_THUMBNAIL_LINK => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_THUMBNAIL_LINK),
                __('Featured Image with link', 'acf-views')
            ],
            Fields::FIELD_POST_AUTHOR => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_AUTHOR),
                __('Author', 'acf-views')
            ],
            Fields::FIELD_POST_DATE => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_DATE),
                __('Published date', 'acf-views')
            ],
            Fields::FIELD_POST_MODIFIED => [
                FieldData::createKey(Fields::GROUP_POST, Fields::FIELD_POST_MODIFIED),
                __('Modified date', 'acf-views')
            ],
        ];

        $userFields = [
            Fields::FIELD_USER_FIRST_NAME => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_FIRST_NAME),
                __('First Name', 'acf-views')
            ],
            Fields::FIELD_USER_LAST_NAME => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_LAST_NAME),
                __('Last Name', 'acf-views')
            ],
            Fields::FIELD_USER_DISPLAY_NAME => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_DISPLAY_NAME),
                __('Display Name', 'acf-views')
            ],
            Fields::FIELD_USER_BIO => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_BIO),
                __('Bio', 'acf-views')
            ],
            Fields::FIELD_USER_EMAIL => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_EMAIL),
                __('Email', 'acf-views')
            ],
            Fields::FIELD_USER_AUTHOR_LINK => [
                FieldData::createKey(Fields::GROUP_USER, Fields::FIELD_USER_AUTHOR_LINK),
                __('Author link', 'acf-views')
            ],
        ];

        $wooFields = [
            Fields::FIELD_WOO_GALLERY => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_GALLERY),
                __('Gallery', 'acf-views')
            ],
            Fields::FIELD_WOO_PRICE => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_PRICE),
                __('Price', 'acf-views')
            ],
            Fields::FIELD_WOO_REGULAR_PRICE => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_REGULAR_PRICE),
                __('Regular price', 'acf-views')
            ],
            Fields::FIELD_WOO_SALE_PRICE => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_SALE_PRICE),
                __('Sale price', 'acf-views')
            ],
            Fields::FIELD_WOO_SKU => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_SKU),
                __('SKU', 'acf-views')
            ],
            Fields::FIELD_WOO_STOCK_STATUS => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_STOCK_STATUS),
                __('Stock status', 'acf-views')
            ],
            Fields::FIELD_WOO_WEIGHT => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_WEIGHT),
                __('Weight', 'acf-views')
            ],
            Fields::FIELD_WOO_LENGTH => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_LENGTH),
                __('Length', 'acf-views')
            ],
            Fields::FIELD_WOO_WIDTH => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_WIDTH),
                __('Width', 'acf-views')
            ],
            Fields::FIELD_WOO_HEIGHT => [
                FieldData::createKey(Fields::GROUP_WOO, Fields::FIELD_WOO_HEIGHT),
                __('Height', 'acf-views')
            ],
        ];

        $extraFields = array_merge($postFields, $userFields, $wooFields);

        foreach ($extraFields as $fieldName => $fieldInfo) {
            if (in_array($fieldName, $excludeTypes, true)) {
                continue;
            }

            $fieldChoices[$fieldInfo[0]] = $fieldInfo[1];
        }

        if (!in_array(Fields::GROUP_TAXONOMY, $excludeTypes, true)) {
            $taxonomies = get_taxonomies([], 'objects');

            foreach ($taxonomies as $taxonomy) {
                $itemName = FieldData::createKey(
                    Fields::GROUP_TAXONOMY,
                    Fields::TAXONOMY_PREFIX . $taxonomy->name
                );
                $fieldChoices[$itemName] = $taxonomy->label;
            }
        }

        return $fieldChoices;
    }

    protected function setConditionalFieldsRulesByValues(): void
    {
        //// Masonry fields

        $masonryFields = [
            FieldData::FIELD_MASONRY_ROW_MIN_HEIGHT,
            FieldData::FIELD_MASONRY_GUTTER,
            FieldData::FIELD_MASONRY_MOBILE_GUTTER,
        ];

        foreach ($masonryFields as $masonryField) {
            add_filter(
                'acf/load_field/name=' . FieldData::getAcfFieldName($masonryField),
                function (array $field) {
                    return $this->setConditionalRulesForField(
                        $field,
                        FieldData::getAcfFieldName(FieldData::FIELD_GALLERY_TYPE),
                        ['', 'plain',],
                    );
                }
            );
        }

        $masonryRepeaterFields = [
            RepeaterFieldData::FIELD_MASONRY_ROW_MIN_HEIGHT,
            RepeaterFieldData::FIELD_MASONRY_GUTTER,
            RepeaterFieldData::FIELD_MASONRY_MOBILE_GUTTER,
        ];

        foreach ($masonryRepeaterFields as $masonryRepeaterField) {
            add_filter(
                'acf/load_field/name=' . RepeaterFieldData::getAcfFieldName($masonryRepeaterField),
                function (array $field) {
                    return $this->setConditionalRulesForField(
                        $field,
                        RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_GALLERY_TYPE),
                        ['', 'plain',],
                    );
                }
            );
        }

        //// repeaterFields tab ('repeater' + 'group')

        add_filter(
            'acf/load_field/name=' . ItemData::getAcfFieldName(ItemData::FIELD_REPEATER_FIELDS_TAB),
            function (array $field) {
                // using exactly the negative (excludeTypes) filter,
                // otherwise if there are no such fields the field will be visible
                $notRepeaterFields = $this->getFieldChoices(true, ['repeater', 'group',]);

                return $this->setConditionalRulesForField(
                    $field,
                    FieldData::getAcfFieldName(FieldData::FIELD_KEY),
                    array_keys($notRepeaterFields)
                );
            }
        );
    }

    protected function setConditionalFieldRules(): void
    {
        $fieldRules = [
            FieldData::FIELD_LINK_LABEL => [
                'link',
                'page_link',
                'file',
                'post_object',
                'relationship',
                'taxonomy',
                'user',
            ],
            FieldData::FIELD_IMAGE_SIZE => [
                'image',
                'gallery',
                Fields::FIELD_POST_THUMBNAIL,
                Fields::FIELD_POST_THUMBNAIL_LINK,
                Fields::FIELD_WOO_GALLERY,
            ],
            FieldData::FIELD_ACF_VIEW_ID => [
                'post_object',
                'relationship',
            ],
            FieldData::FIELD_GALLERY_TYPE => [
                'gallery',
                Fields::FIELD_WOO_GALLERY,
            ],
            FieldData::FIELD_GALLERY_WITH_LIGHT_BOX => [
                'gallery',
                Fields::FIELD_WOO_GALLERY,
            ],
            FieldData::FIELD_MAP_ADDRESS_FORMAT => [
                'google_map',
            ],
            FieldData::FIELD_IS_MAP_WITH_ADDRESS => [
                'google_map',
            ],
            FieldData::FIELD_IS_MAP_WITHOUT_GOOGLE_MAP => [
                'google_map',
            ],
            FieldData::FIELD_OPTIONS_DELIMITER => [
                'select',
                'post_object',
                'page_link',
                'relationship',
                'taxonomy',
                'user',
                Fields::GROUP_TAXONOMY,
            ],
        ];

        foreach ($fieldRules as $fieldName => $conditionalFields) {
            $this->addConditionalFilter($fieldName, $conditionalFields);
            $this->addConditionalFilter($fieldName, $conditionalFields, true);
        }

        $this->setConditionalFieldsRulesByValues();
    }

    protected function getImageSizes(): array
    {
        $imageSizeChoices = [];
        $imageSizes = get_intermediate_image_sizes();

        foreach ($imageSizes as $imageSize) {
            $imageSizeChoices[$imageSize] = ucfirst($imageSize);
        }

        $imageSizeChoices['full'] = __('Full', 'acf-views');

        return $imageSizeChoices;
    }

    protected function setFieldChoices(): void
    {
        add_filter(
            'acf/load_field/name=' . FieldData::getAcfFieldName(FieldData::FIELD_KEY),
            function (array $field) {
                $field['choices'] = $this->getFieldChoices();

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_KEY),
            function (array $field) {
                $field['choices'] = $this->getSubFieldChoices();

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . FieldData::getAcfFieldName(FieldData::FIELD_IMAGE_SIZE),
            function (array $field) {
                $field['choices'] = $this->getImageSizes();

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_IMAGE_SIZE),
            function (array $field) {
                $field['choices'] = $this->getImageSizes();

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . FieldData::getAcfFieldName(FieldData::FIELD_ACF_VIEW_ID),
            function (array $field) {
                $field['choices'] = $this->getAcfViewChoices();

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_ACF_VIEW_ID),
            function (array $field) {
                $field['choices'] = $this->getAcfViewChoices();

                return $field;
            }
        );
    }

    public function getFieldChoices(bool $isWithExtra = true, array $excludeTypes = []): array
    {
        $fieldChoices = [];

        if (!function_exists('acf_get_fields')) {
            return $fieldChoices;
        }

        $fieldChoices = [
            '' => 'Select',
        ];

        if ($isWithExtra) {
            $fieldChoices = array_merge($fieldChoices, $this->getExtraFieldChoices($excludeTypes));
        }

        $supportedFieldTypes = $this->getFieldTypes();

        $groups = $this->getGroups();
        foreach ($groups as $group) {
            $fields = acf_get_fields($group);

            foreach ($fields as $groupField) {
                if (!in_array($groupField['type'], $supportedFieldTypes, true) ||
                    ($excludeTypes && in_array($groupField['type'], $excludeTypes, true))) {
                    continue;
                }

                $fullFieldId = FieldData::createKey($group['key'], $groupField['key']);
                $fieldChoices[$fullFieldId] = $groupField['label'] . ' (' . $groupField['type'] . ')';
            }
        }

        return $fieldChoices;
    }

    public function getFieldTypes(): array
    {
        $fieldTypes = [];
        $groupedFieldTypes = $this->getGroupedFieldTypes();
        foreach ($groupedFieldTypes as $group => $fields) {
            $fieldTypes = array_merge($fieldTypes, $fields);
        }

        return $fieldTypes;
    }
}