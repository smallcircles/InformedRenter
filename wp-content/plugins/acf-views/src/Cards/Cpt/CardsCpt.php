<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cpt\Cpt;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Shortcodes;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use WP_Query;

defined('ABSPATH') || exit;

class CardsCpt extends Cpt
{
    const NAME = 'acf_cards';

    const COLUMN_DESCRIPTION = self::NAME . '_description';
    const COLUMN_SHORTCODE = self::NAME . '_shortcode';
    const COLUMN_RELATED_VIEW = self::NAME . '_relatedView';
    const COLUMN_LAST_MODIFIED = self::NAME . '_lastModified';

    protected Html $html;
    protected CardsMetaBoxes $acfCardsMetaBoxes;

    public function __construct(Cache $cache, Html $html, CardsMetaBoxes $acfCardsMetaBoxes)
    {
        parent::__construct($cache);

        $this->html = $html;
        $this->acfCardsMetaBoxes = $acfCardsMetaBoxes;
    }

    public function addCPT(): void
    {
        $labels = [
            'name' => __('ACF Cards', 'acf-views'),
            'singular_name' => __('ACF Card', 'acf-views'),
            'menu_name' => __('ACF Cards', 'acf-views'),
            'parent_item_colon' => __('Parent ACF Card', 'acf-views'),
            'all_items' => __('ACF Cards', 'acf-views'),
            'view_item' => __('Browse ACF Card', 'acf-views'),
            'add_new_item' => __('Add New ACF Card', 'acf-views'),
            'add_new' => __('Add New', 'acf-views'),
            'item_updated' => __('ACF Card updated.', 'acf-views'),
            'edit_item' => __('Edit ACF Card', 'acf-views'),
            'update_item' => __('Update ACF Card', 'acf-views'),
            'search_items' => __('Search ACF Card', 'acf-views'),
            'not_found' => __('Not Found', 'acf-views'),
            'not_found_in_trash' => __('Not Found In Trash', 'acf-views'),
        ];

        $description = __(
                'Create ACF Card item to choose a set of posts (or CPT items) and paste the shortcode in a target place to display the posts with their ACF fields.',
                'acf-views'
            ) .
            '<br>'
            . __('(which fields are printed depending on a selected ACF View in the Card settings)', 'acf-views');

        $args = [
            'label' => __('ACF Cards', 'acf-views'),
            'description' => $description,
            'labels' => $labels,
            // shouldn't be presented in the sitemap and other places
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'delete_with_user' => false,
            'exclude_from_search' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'can_export' => false,
            'rewrite' => false,
            'query_var' => false,
            'menu_icon' => 'dashicons-format-gallery',
            'supports' => ['title', 'editor',],
            'show_in_graphql' => false,
        ];

        register_post_type(self::NAME, $args);
    }

    public function addPage(): void
    {
        $url = sprintf('edit.php?post_type=%s', ViewsCpt::NAME);

        global $submenu;

        if (!$submenu[$url]) {
            $submenu[$url] = [];
        }

        // 'ACF Views' has 5, so 6 is right after
        $submenu[$url][6] = [
            __('ACF Cards', 'acf-views'),
            'manage_options',
            sprintf(
                'edit.php?post_type=%s',
                self::NAME
            )
        ];

        ksort($submenu[$url]);
    }

    public function addSortableColumnsToRequest(WP_Query $query): void
    {
        if (!is_admin()) {
            return;
        }

        $orderBy = $query->get('orderby');

        switch ($orderBy) {
            case self::COLUMN_LAST_MODIFIED:
                $query->set('orderby', 'post_modified');
                break;
        }
    }

    public function printColumn(string $column, int $postId): void
    {
        switch ($column) {
            case self::COLUMN_DESCRIPTION:
                $view = $this->cache->getAcfCardData($postId);

                echo esc_html($view->description);
                break;
            case self::COLUMN_SHORTCODE:
                $cardUniqueId = $this->cache->getAcfCardData($postId)->getUniqueId(true);
                echo $this->html->postboxShortcodes(
                    $cardUniqueId,
                    true,
                    Shortcodes::SHORTCODE_CARDS,
                    get_the_title($postId),
                    true
                );
                break;
            case self::COLUMN_LAST_MODIFIED:
                echo esc_html(explode(' ', get_post($postId)->post_modified)[0]);
                break;
            case self::COLUMN_RELATED_VIEW:
                // without the not found message
                $this->acfCardsMetaBoxes->printRelatedAcfViewMetaBox(get_post($postId), false, true);
                break;
        }
    }

    public function getTitlePlaceholder(string $title): string
    {
        $screen = get_current_screen()->post_type ?? '';
        if (self::NAME !== $screen) {
            return $title;
        }

        return __('Name your card', 'acf-views');
    }

    public function getColumns(array $columns): array
    {
        unset($columns['date']);

        return array_merge($columns, [
            self::COLUMN_DESCRIPTION => __('Description', 'acf-views'),
            self::COLUMN_SHORTCODE => __('Shortcode', 'acf-views'),
            self::COLUMN_RELATED_VIEW => __('Related View', 'acf-views'),
            self::COLUMN_LAST_MODIFIED => __('Last modified', 'acf-views'),
        ]);
    }

    public function getSortableColumns(array $columns): array
    {
        return array_merge($columns, [
            self::COLUMN_LAST_MODIFIED => self::COLUMN_LAST_MODIFIED,
        ]);
    }

    public function replacePostUpdatedMessage(array $messages): array
    {
        global $post;

        $restoredMessage = false;
        $scheduledMessage = __('ACF Card scheduled for:', 'acf-views');
        $scheduledMessage .= sprintf(
            ' <strong>%1$s</strong>',
            date_i18n('M j, Y @ G:i', strtotime($post->post_date))
        );

        if (isset($_GET['revision'])) {
            $restoredMessage = __('ACF Card restored to revision from', 'acf-views');
            $restoredMessage .= ' ' . wp_post_revision_title((int)$_GET['revision'], false);
        }

        $messages[self::NAME] = [
            0 => '', // Unused. Messages start at index 1.
            1 => __('ACF Card updated.', 'acf-views'),
            2 => __('Custom field updated.', 'acf-views'),
            3 => __('Custom field deleted.', 'acf-views'),
            4 => __('ACF Card updated.', 'acf-views'),
            5 => $restoredMessage,
            6 => __('ACF Card published.', 'acf-views'),
            7 => __('ACF Card saved.', 'acf-views'),
            8 => __('ACF Card submitted.', 'acf-views'),
            9 => $scheduledMessage,
            10 => __('ACF Card draft updated.', 'acf-views'),
        ];

        return $messages;
    }

    public function setHooks(): void
    {
        parent::setHooks();

        add_action('init', [$this, 'addCPT']);
        add_action('admin_menu', [$this, 'addPage']);
        add_action(
            'manage_' . self::NAME . '_posts_custom_column',
            [
                $this,
                'printColumn',
            ],
            10,
            2
        );
        add_action('pre_get_posts', [$this, 'addSortableColumnsToRequest',]);

        add_filter('manage_' . self::NAME . '_posts_columns', [$this, 'getColumns',]);
        add_filter('manage_edit-' . self::NAME . '_sortable_columns', [$this, 'getSortableColumns',]);
        add_filter('enter_title_here', [$this, 'getTitlePlaceholder',]);
        add_filter('post_updated_messages', [$this, 'replacePostUpdatedMessage']);
    }
}
