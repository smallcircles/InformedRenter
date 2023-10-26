<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Groups;

use org\wplake\acf_views\Group;
use org\wplake\acf_views\SettingsPage;

defined('ABSPATH') || exit;

class SettingsData extends Group
{
    // to fix the group name in case class name changes
    const CUSTOM_GROUP_NAME = self::GROUP_NAME_PREFIX . 'settings-data';

    const FIELD_IS_DEV_MODE = 'isDevMode';
    const FIELD_IS_NOT_COLLAPSED_FIELDS_BY_DEFAULT = 'isNotCollapsedFieldsByDefault';
    const FIELD_IS_WITHOUT_FIELDS_COLLAPSE_CURSOR = 'isWithoutFieldsCollapseCursor';


    /**
     * @a-type tab
     * @label General
     */
    public bool $general;
    /**
     * @label Development mode
     * @instructions Enable to display quick access links on the front and make error messages more detailed (for admins only).
     */
    public bool $isDevMode;

    /**
     * @a-type tab
     * @label Preferences
     */
    public bool $preferences;
    /**
     * @label Do not collapse fields in View by default
     */
    public bool $isNotCollapsedFieldsByDefault;
    /**
     * @label Do not show collapse fields cursor
     */
    public bool $isWithoutFieldsCollapseCursor;

    protected static function getLocationRules(): array
    {
        return [
            [
                'options_page == ' . SettingsPage::SLUG,
            ]
        ];
    }

    public static function getGroupInfo(): array
    {
        $groupInfo = parent::getGroupInfo();

        return array_merge($groupInfo, [
            'title' => __('Settings', 'acf-views'),
            'style' => 'seamless',
        ]);
    }
}
