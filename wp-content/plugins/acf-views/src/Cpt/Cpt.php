<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cards\Cpt\CardsCpt;
use org\wplake\acf_views\CptData;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use WP_Post;
use WP_Query;

defined('ABSPATH') || exit;

abstract class Cpt
{
    const NAME = '';

    protected Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    protected function getActionClone(): string
    {
        return static::NAME . '_clone';
    }

    protected function getActionCloned(): string
    {
        return static::NAME . '_cloned';
    }

    protected function getInstanceData(int $postId): CptData
    {
        return static::NAME === ViewsCpt::NAME ?
            $this->cache->getAcfViewData($postId) :
            $this->cache->getAcfCardData($postId);
    }


    public function insertIntoArrayAfterKey(array $array, string $key, array $newItems): array
    {
        $keys = array_keys($array);
        $index = array_search($key, $keys);

        $pos = false === $index ?
            count($array) :
            $index + 1;

        return array_merge(array_slice($array, 0, $pos), $newItems, array_slice($array, $pos));
    }

    // Gutenberg will try to update the content by the presented value, which is empty, so ignore it
    public function avoidOverridePostContentByGutenberg(array $data): array
    {
        if (!key_exists('post_type', $data) ||
            !in_array($data['post_type'], [ViewsCpt::NAME, CardsCpt::NAME], true)) {
            return $data;
        }

        // avoid any attempts, even not empty (we use direct DB query, so it's safe)
        if (key_exists('post_content', $data)) {
            unset($data['post_content']);
        }

        return $data;
    }

    // add the ACF's class to the body to have a nice look of the list table
    public function maybeAddAcfClassToBody(string $classes): string
    {
        $currentScreen = get_current_screen();

        $isOurPost = in_array($currentScreen->post_type, [ViewsCpt::NAME, CardsCpt::NAME], true);

        if ($isOurPost &&
            'edit' === $currentScreen->base) {
            $classes .= ' acf-internal-post-type';
        }

        return $classes;
    }

    public function addPostNameToSearch(WP_Query $query): void
    {
        $postType = $query->query_vars['post_type'] ?? '';

        if (!is_admin() ||
            !in_array($postType, [ViewsCpt::NAME, CardsCpt::NAME,], true) ||
            !$query->is_main_query() ||
            !$query->is_search()) {
            return;
        }

        $search = $query->query_vars['s'];

        if (13 !== strlen($search) ||
            !preg_match('/^[a-z0-9]+$/', $search)) {
            return;
        }

        $prefix = $postType === ViewsCpt::NAME ?
            'view_' :
            'card_';

        $query->set('s', '');
        $query->set('name', $prefix . $search);
    }

    public function cloneItemAction(): void
    {
        if (!isset($_GET[$this->getActionClone()])) {
            return;
        }

        $postId = (int)$_GET[$this->getActionClone()];
        $post = get_post($postId);

        if (!$post ||
            static::NAME !== $post->post_type) {
            return;
        }

        check_admin_referer('bulk-posts');

        $args = [
            'post_type' => $post->post_type,
            'post_status' => 'draft',
            'post_title' => $post->post_title . ' ' . __('Clone', 'acf-views'),
            'post_author' => $post->post_author,
        ];

        $newPostId = wp_insert_post($args);

        // something went wrong
        if (is_wp_error($newPostId)) {
            return;
        }

        $instanceData = $this->getInstanceData($postId)->getDeepClone();
        $instanceData->setSource($newPostId);

        $prefix = static::NAME === ViewsCpt::NAME ?
            'view_' :
            'card_';
        $this->maybeSetUniqueId($instanceData, $prefix);
        // save JSON to the post_content (also will save POST_FIELD_MOUNT_POINTS and others)
        $instanceData->saveToPostContent();

        $targetUrl = get_admin_url(null, '/edit.php?post_type=' . static::NAME);
        $targetUrl .= '&' . $this->getActionCloned() . '=1';

        wp_redirect($targetUrl);
        exit;
    }

    public function showItemClonedMessage(): void
    {
        if (!isset($_GET[$this->getActionCloned()])) {
            return;
        }

        echo '<div class="notice notice-success">' .
            sprintf('<p>%s</p>', __('Item success cloned.', 'acf-views')) .
            '</div>';
    }

    public function getRowActions(array $actions, WP_Post $view): array
    {
        if (static::NAME !== $view->post_type) {
            return $actions;
        }

        $trash = str_replace(
            '>Trash<',
            sprintf('>%s<', __('Delete', 'acf-views')),
            $actions['trash'] ?? ''
        );

        // quick edit
        unset($actions['inline hide-if-no-js']);
        unset($actions['trash']);

        $cloneLink = get_admin_url(null, '/edit.php?post_type=' . static::NAME);
        $cloneLink .= '&' . $this->getActionClone() . '=' . $view->ID . '&_wpnonce=' . wp_create_nonce(
                'bulk-posts'
            );
        $actions['clone'] = sprintf("<a href='%s'>%s</a>", $cloneLink, __('Clone', 'acf-views'));
        $actions['trash'] = $trash;

        return $actions;
    }

    public function printPostTypeDescription($views)
    {
        $screen = get_current_screen();
        $postType = get_post_type_object($screen->post_type);

        if ($postType->description) {
            // don't use esc_html as it contains links
            printf('<p>%s</p>', $postType->description);
        }

        return $views; // return original input unchanged
    }

    /**
     * Otherwise in case editing fields (without saving) and reloading a page,
     * then the fields have these unsaved values, it's wrong and breaks logic (e.g. of group-field selects)
     */
    public function disableAutocompleteForPostEdit(WP_Post $post): void
    {
        if (static::NAME !== $post->post_type) {
            return;
        }

        echo ' autocomplete="off"';
    }

    public function setHooks(): void
    {
        add_action('admin_init', [$this, 'cloneItemAction']);
        add_action('admin_notices', [$this, 'showItemClonedMessage']);

        add_action('post_edit_form_tag', [$this, 'disableAutocompleteForPostEdit']);
        add_action('pre_get_posts', [$this, 'addPostNameToSearch']);

        add_filter('views_edit-' . static::NAME, [$this, 'printPostTypeDescription',]);
        add_filter('post_row_actions', [$this, 'getRowActions',], 10, 2);

        add_filter('wp_insert_post_data', [$this, 'avoidOverridePostContentByGutenberg']);
        add_filter('admin_body_class', [$this, 'maybeAddAcfClassToBody']);
    }
}