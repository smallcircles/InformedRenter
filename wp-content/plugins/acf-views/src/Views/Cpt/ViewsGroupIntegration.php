<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Groups\Integration\FieldDataIntegration;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\ViewData;
use WP_Post;

defined('ABSPATH') || exit;

class ViewsGroupIntegration
{
    protected ItemData $item;
    protected Cache $cache;
    protected FieldDataIntegration $fieldIntegration;
    protected ViewsSaveActions $acfViewsSaveActions;

    public function __construct(
        ItemData $item,
        Cache $cache,
        FieldDataIntegration $fieldIntegration,
        ViewsSaveActions $acfViewsSaveActions
    ) {
        $this->item = $item;
        $this->cache = $cache;
        $this->fieldIntegration = $fieldIntegration;
        $this->acfViewsSaveActions = $acfViewsSaveActions;
    }

    protected function getJsHover(): string
    {
        return 'onMouseOver="this.style.filter=\'brightness(30%)\'" onMouseOut="this.style.filter=\'brightness(100%)\'"';
    }

    protected function addItemToView(
        string $groupKey,
        array $field,
        ViewData $acfViewData,
        array $supportedFieldTypes
    ): ?ItemData {
        $fieldType = $field['type'];

        if (!in_array($fieldType, $supportedFieldTypes, true)) {
            return null;
        }

        $item = $this->item->getDeepClone();
        $item->group = $groupKey;
        $item->field->key = $item->field->createKey($groupKey, $field['key']);

        $acfViewData->items[] = $item;

        return $item;
    }

    protected function printRelatedAcfViews(WP_Post $group, bool $isListLook = false): void
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT * from {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'
                      AND FIND_IN_SET(%s,post_content_filtered) > 0",
            ViewsCpt::NAME,
            $group->post_name
        );
        $relatedViews = $wpdb->get_results($query);

        $label = $relatedViews ?
            __('Assigned to ACF Views:', 'acf-views') . ' ' :
            __('Not assigned to any ACF Views.', 'acf-views');

        if (!$isListLook) {
            echo $label;
        }

        $links = [];

        foreach ($relatedViews as $relatedView) {
            $links[] = sprintf(
                '<a href="%s" target="_blank" style="transition:all .3s ease;" %s>%s</a>',
                get_edit_post_link($relatedView),
                $this->getJsHover(),
                get_the_title($relatedView)
            );
        }

        echo implode(', ', $links);

        // ignore on the creation page
        if ('publish' !== $group->post_status) {
            return;
        }

        if (!$relatedViews &&
            $isListLook) {
            echo '';
        }

        echo '<br><br>';


        $label = __('Add new', 'acf-views');

        $style = 'min-height: 0;line-height: 1.2;padding: 3px 7px;font-size:11px;height:auto;transition:all .3s ease;';
        printf(
            '<a href="%s" target="_blank" class="button" style="%s" onmouseover="this.style.color=\'#044767\'" onmouseout="this.style.color=\'#0783BE\'">%s</a>',
            admin_url('/post-new.php?post_type=acf_views&_from=' . $group->ID),
            $style,
            $label
        );
    }

    public function addRelatedViewsToAcfGroupsList(array $columns): array
    {
        return array_merge($columns, [
            'relatedAcfViews' => __('Assigned to View', 'acf-views'),
        ]);
    }

    public function addAcfViewsTabToAcfGroup(array $tabs): array
    {
        return array_merge($tabs, [
            'acf_views' => __('ACF Views', 'acf-views'),
        ]);
    }

    public function printAcfViewsTabOnAcfGroup(array $fieldGroup): void
    {
        $this->printRelatedAcfViews(get_post($fieldGroup['ID']));
    }

    public function maybeCreateViewForGroup(): void
    {
        $screen = get_current_screen();
        $from = (int)($_GET['_from'] ?? 0);
        $fromPost = $from ?
            get_post($from) :
            null;

        $isAddScreen = 'post' === $screen->base &&
            'add' === $screen->action;

        if (ViewsCpt::NAME !== $screen->post_type ||
            !$isAddScreen ||
            !$fromPost ||
            'acf-field-group' !== $fromPost->post_type ||
            'publish' !== $fromPost->post_status) {
            return;
        }
        $viewId = wp_insert_post([
            'post_type' => ViewsCpt::NAME,
            'post_status' => 'publish',
            'post_title' => $fromPost->post_title,
        ]);

        if (is_wp_error($viewId)) {
            return;
        }

        $acfViewData = $this->cache->getAcfViewData($viewId);

        $fields = acf_get_fields($fromPost->ID);
        $supportedFieldTypes = $this->fieldIntegration->getFieldTypes();

        foreach ($fields as $field) {
            $this->addItemToView($fromPost->post_name, $field, $acfViewData, $supportedFieldTypes);
        }

        $this->acfViewsSaveActions->performSaveActions($viewId);

        wp_redirect(get_edit_post_link($viewId, 'redirect'));
        exit;
    }

    public function printRelatedViewsColumnOnAcfGroupList(string $column, int $postId): void
    {
        if ('relatedAcfViews' !== $column) {
            return;
        }

        $this->printRelatedAcfViews(get_post($postId), true);
    }

    public function setHooks(): void
    {
        add_filter('acf/field_group/additional_group_settings_tabs', [$this, 'addAcfViewsTabToAcfGroup']);
        // higher priority, to run after ACF's listener (they don't use 'merge')
        add_filter('manage_acf-field-group_posts_columns', [$this, 'addRelatedViewsToAcfGroupsList',], 20);
        add_action(
            'acf/field_group/render_group_settings_tab/acf_views',
            [$this, 'printAcfViewsTabOnAcfGroup',],
            10,
            2
        );
        add_action('current_screen', [$this, 'maybeCreateViewForGroup']);
        add_action(
            'manage_acf-field-group_posts_custom_column',
            [$this, 'printRelatedViewsColumnOnAcfGroupList',],
            10,
            2
        );
    }
}
