<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use org\wplake\acf_views\Groups\MountPointData;

defined('ABSPATH') || exit;

abstract class CptData extends Group
{
    const POST_FIELD_MOUNT_POINTS = 'post_excerpt';
    const POST_FIELD_USED_ITEMS = 'post_content_filtered';

    // fields have 'a-order' is 2 to be after current fields (they have '1' by default)

    /**
     * @a-type tab
     * @label Mount Points
     * @a-order 2
     * @a-pro The field must be not required or have default value!
     */
    public bool $mountPointsTab;
    /**
     * @item \org\wplake\acf_views\Groups\MountPointData
     * @var MountPointData[]
     * @label Mount Points
     * @instructions 'Mount' this View/Card to a location that doesn't support shortcodes. Mounting uses 'the_content' theme hook. <a target="_blank" href="https://docs.acfviews.com/guides/acf-views/features/mount-points-pro">Read more</a>
     * @button_label Add Mount Point
     * @a-no-tab 1
     * @a-order 2
     * @a-pro The field must be not required or have default value!
     */
    public array $mountPoints;
    // just define without any annotations, it'll be overwritten by children
    public bool $isMarkupWithDigitalId;

    abstract protected function getUsedItems(): array;

    public function saveToPostContent(array $postFields = [], bool $isSkipDefaults = false): bool
    {
        $commonMountPoints = [];

        foreach ($this->mountPoints as $mountPoint) {
            // both into one array, as IDs and postTypes are different and can't be mixed up
            $commonMountPoints = array_merge($commonMountPoints, $mountPoint->postTypes);
            $commonMountPoints = array_merge($commonMountPoints, $mountPoint->posts);
        }

        $commonMountPoints = array_values(array_unique($commonMountPoints));

        $postFields = array_merge($postFields, [
            static::POST_FIELD_MOUNT_POINTS => join(',', $commonMountPoints),
            static::POST_FIELD_USED_ITEMS => join(',', $this->getUsedItems()),
        ]);

        // skipDefaults. We won't need to save default values to the DB
        $result = parent::saveToPostContent($postFields, true);

        // we made a direct WP query, which means we need to clean the cache,
        // to make the changes available in the WP cache
        clean_post_cache($this->getSource());

        return $result;
    }

    /**
     * @param bool $isWithoutPrefix Set to true, when need short (abc3 in case of view_abc3)
     * @return string
     */
    public function getUniqueId(bool $isWithoutPrefix = false): string
    {
        $uniqueId = get_post($this->getSource())->post_name ?? '';

        return !$isWithoutPrefix ?
            $uniqueId :
            explode('_', $uniqueId)[1] ?? '';
    }

    public function getMarkupId(): string
    {
        return !$this->isMarkupWithDigitalId ?
            $this->getUniqueId(true) :
            (string)$this->getSource();
    }
}
