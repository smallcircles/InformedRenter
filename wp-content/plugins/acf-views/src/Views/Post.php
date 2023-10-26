<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views;

use org\wplake\acf_views\Views\Fields\Fields;

defined('ABSPATH') || exit;

class Post
{
    /**
     * @var int|string Can be string in case with 'options' or 'user_x'
     */
    private $id;
    private array $fieldsCache;
    private bool $isBlock;
    private int $userId;

    /**
     * @param int|string $id
     * @param array $fieldsCache
     * @param bool $isBlock
     * @param int $userId
     */
    public function __construct($id, array $fieldsCache = [], bool $isBlock = false, int $userId = 0)
    {
        $this->id = $id;
        $this->fieldsCache = $fieldsCache;
        $this->isBlock = $isBlock;
        $this->userId = $userId;
    }

    public function isOptions(): bool
    {
        return 'options' === $this->id;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getFieldValue(string $fieldName, bool $isSkipCache = false): array
    {
        if (isset($this->fieldsCache[$fieldName]) &&
            !$isSkipCache) {
            return $this->fieldsCache[$fieldName];
        }

        $value = [
            '',
            '',
        ];

        $isCustomFieldType = 0 === strpos($fieldName, '_');

        if ($isCustomFieldType) {
            $isPostGroup = 0 === strpos($fieldName, Fields::POST_GROUP_PREFIX);
            $isTaxonomyGroup = 0 === strpos($fieldName, Fields::TAXONOMY_PREFIX);
            $isUserGroup = 0 === strpos($fieldName, Fields::USER_GROUP_PREFIX);
            $isWooGroup = 0 === strpos($fieldName, Fields::WOO_GROUP_PREFIX);

            if (!$this->isOptions() &&
                ($isPostGroup || $isTaxonomyGroup || $isWooGroup)) {
                $value[0] = $this->id;
            }

            if ($isUserGroup) {
                $value[0] = $this->userId;
            }

            return $value;
        }

        if (function_exists('get_field')) {
            $notFormattedValue = !$this->isBlock ?
                get_field($fieldName, $this->id, false) :
                get_field($fieldName, false, false);
            $formattedValue = !$this->isBlock ?
                get_field($fieldName, $this->id) :
                get_field($fieldName, false);

            $value = [
                $notFormattedValue,
                $formattedValue
            ];
        }

        $this->fieldsCache[$fieldName] = $value;

        return $value;
    }
}
