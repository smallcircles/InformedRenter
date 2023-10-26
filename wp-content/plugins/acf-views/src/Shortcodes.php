<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use org\wplake\acf_views\Cards\CardFactory;
use org\wplake\acf_views\Cards\Cpt\CardsCpt;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use org\wplake\acf_views\Views\Post;
use org\wplake\acf_views\Views\ViewFactory;

defined('ABSPATH') || exit;

class Shortcodes
{
    const SHORTCODE_VIEWS = ViewsCpt::NAME;
    const SHORTCODE_CARDS = CardsCpt::NAME;

    protected ViewFactory $acfViewFactory;
    protected CardFactory $acfCardFactory;
    protected Cache $cache;
    protected Settings $settings;
    // used to avoid recursion with post_object/relationship fields
    protected array $displayingView;
    protected int $queryLoopPostId;

    public function __construct(
        ViewFactory $acfViewFactory,
        CardFactory $acfCardFactory,
        Cache $cache,
        Settings $settings
    ) {
        $this->acfViewFactory = $acfViewFactory;
        $this->acfCardFactory = $acfCardFactory;
        $this->cache = $cache;
        $this->settings = $settings;
        $this->displayingView = [];
        // don't use '0' as the default, because it can be 0 in the 'render_callback' hook
        $this->queryLoopPostId = -1;
    }

    protected function getErrorMarkup(string $shortcode, array $args, string $error): string
    {
        $attrs = [];
        foreach ($args as $name => $value) {
            $attrs[] = sprintf('%s="%s"', $name, $value);
        }
        return sprintf(
            "<p style='color:red;'>%s %s %s</p>",
            __('Shortcode error:', 'acf-views'),
            $error,
            sprintf('(%s %s)', $shortcode, implode(' ', $attrs))
        );
    }

    protected function isShortcodeAvailableForUser(array $userRoles, array $shortcodeArgs): bool
    {
        $userWithRoles = (string)($shortcodeArgs['user-with-roles'] ?? '');
        $userWithRoles = trim($userWithRoles);
        $userWithRoles = $userWithRoles ?
            explode(',', $userWithRoles) :
            [];

        $userWithoutRoles = (string)($shortcodeArgs['user-without-roles'] ?? '');
        $userWithoutRoles = trim($userWithoutRoles);
        $userWithoutRoles = $userWithoutRoles ?
            explode(',', $userWithoutRoles) :
            [];

        if (!$userWithRoles &&
            !$userWithoutRoles) {
            return true;
        }

        $userHasAllowedRoles = !!array_intersect($userWithRoles, $userRoles);
        $userHasDeniedRoles = !!array_intersect($userWithoutRoles, $userRoles);

        if (($userWithRoles && !$userHasAllowedRoles) ||
            ($userWithoutRoles && $userHasDeniedRoles)) {
            return false;
        }

        return true;
    }

    /**
     * block theme: skip execution for the Gutenberg common call, as query-loop may be used, and the post id won't be available yet
     * Exceptions:
     * 1. if the object-id is set, e.g. as part of the Card shortcode
     * 2. If mount-point is set, e.g. as part of the MountPoint functionality
     */
    protected function maybeSkipShortcode(string $objectId, array $attrs): string
    {
        $isMountPoint = isset($attrs['mount-point']);

        if (!wp_is_block_theme() ||
            -1 !== $this->queryLoopPostId ||
            $objectId ||
            $isMountPoint) {
            return '';
        }

        $stringAttrs = array_map(
            function ($key, $value) {
                return sprintf('%s="%s"', $key, $value);
            },
            array_keys($attrs),
            array_values($attrs)
        );

        return sprintf('[%s %s]', self::SHORTCODE_VIEWS, implode(' ', $stringAttrs));
    }

    protected function getDataPostId(string $objectId, int $currentPageId, int $userId): string
    {
        if (in_array($objectId, ['$user$', 'options',], true)) {
            return '$user$' === $objectId ?
                'user_' . $userId :
                $objectId;
        }

        global $post;

        // a. dataPostId from the shortcode argument

        $dataPostId = (int)$objectId;

        // b. from the Gutenberg query loop

        if (!in_array($this->queryLoopPostId, [-1, 0,], true)) {
            $dataPostId = $dataPostId ?: $this->queryLoopPostId;
        }

        // c. dataPostId from the current loop (WordPress posts, WooCommerce products...)

        $dataPostId = $dataPostId ?: ($post->ID ?? 0);

        // d. dataPostId from the current page

        $dataPostId = $dataPostId ?: $currentPageId;

        // validate the ID

        return (string)(get_post($dataPostId) ?
            $dataPostId :
            0);
    }

    protected function maybeAddQuickLink(string $html, int $postId): string
    {
        $roles = wp_get_current_user()->roles;

        if (!$this->settings->isDevMode() ||
            !in_array('administrator', $roles, true)) {
            return $html;
        }

        preg_match('/^<div([^>]+)>/', $html, $matches);

        if (!$matches) {
            return $html;
        }

        $label = __('Edit', 'acf-views');
        $label .= sprintf(' "%s"', get_the_title($postId));

        $tag = $matches[0];
        $tagContent = $matches[1];
        $tagContent .= ' style="position:relative;"';

        $newTag = sprintf('<div%s>', $tagContent);
        $newTag .= sprintf(
            '<a href="%s" target="_blank" class="acf-views__quick-link" style="position: absolute;bottom:0;left:0;color:#008BB7;z-index: 2;opacity: .7;background: white;transition: all .3s ease;border-radius: 5px;padding: 2px 10px;text-decoration: none;font-size: 13px;" onMouseOver="this.style.opacity=\'1\';this.style.textDecoration=\'underline\'" onMouseOut="this.style.opacity=\'.7\';this.style.textDecoration=\'none\'">%s</a>',
            get_edit_post_link($postId),
            $label
        );

        return substr_replace(
            $html,
            $newTag,
            0,
            strlen($tag)
        );
    }

    /**
     * The issue that for now (6.3), Gutenberg shortcode element doesn't support context.
     * So if you place shortcode in the Query Loop template, it's impossible to get the post ID.
     * Furthermore, it seems Gutenberg renders all the shortcodes at once, before blocks parsing.
     * Which means even hooking into 'register_block_type_args' won't work by default, because in the 'render_callback'
     * it'll receive already rendered shortcode's content. So having the postId is too late here.
     *
     * https://github.com/WordPress/gutenberg/issues/43053
     * https://support.advancedcustomfields.com/forums/topic/add-custom-field-to-query-loop/
     * https://wptavern.com/wordpress-6-2-2-restores-shortcode-support-in-block-templates-fixes-security-issue
     */
    public function extendGutenbergShortcode(array $args, string $name): array
    {
        if (!wp_is_block_theme() ||
            'core/shortcode' !== $name) {
            return $args;
        }

        $args['usesContext'] = $args['usesContext'] ?? [];
        $args['usesContext'][] = 'postId';
        $args['render_callback'] = function ($attributes, $content, $block) {
            // can be 0, if the shortcode is outside of the query loop
            $postId = (int)($block->context['postId'] ?? 0);

            if (false === strpos($content, '[' . self::SHORTCODE_VIEWS)) {
                return $content;
            }

            $this->queryLoopPostId = $postId;

            $content = do_shortcode($content);

            // don't use '0' as the default, because it can be 0 in the 'render_callback' hook
            $this->queryLoopPostId = -1;

            return $content;
        };

        return $args;
    }

    public function acfCardsShortcode($attrs): string
    {
        $attrs = $attrs ?
            (array)$attrs :
            [];

        if (!$this->isShortcodeAvailableForUser(wp_get_current_user()->roles, $attrs)) {
            return '';
        }

        $cardId = (string)($attrs['card-id'] ?? 0);
        $cardId = $this->cache->getPostIdByUniqueId($cardId, CardsCpt::NAME);

        if (!$cardId) {
            return $this->getErrorMarkup(
                self::SHORTCODE_CARDS,
                $attrs,
                __('card-id attribute is missing or wrong', 'acf-views')
            );
        }

        $acfCardData = $this->cache->getAcfCardData($cardId);

        $html = $this->acfCardFactory->createAndGetHtml($acfCardData, 1);

        return $this->maybeAddQuickLink($html, $cardId);
    }

    public function acfViewsShortcode($attrs): string
    {
        $attrs = $attrs ?
            (array)$attrs :
            [];

        $viewId = (string)($attrs['view-id'] ?? 0);
        $objectId = (string)($attrs['object-id'] ?? 0);

        $viewId = $this->cache->getPostIdByUniqueId($viewId, ViewsCpt::NAME);

        if (!$viewId) {
            return $this->getErrorMarkup(
                self::SHORTCODE_VIEWS,
                $attrs,
                __('view-id attribute is missing or wrong', 'acf-views')
            );
        }

        $skippedShortcode = $this->maybeSkipShortcode($objectId, $attrs);

        if ($skippedShortcode) {
            return $skippedShortcode;
        }

        if (!$this->isShortcodeAvailableForUser(wp_get_current_user()->roles, $attrs)) {
            return '';
        }

        // equals to 0 on WooCommerce Shop Page, but in this case pageID can't be gotten with built-in WP functions
        $currentPageId = get_queried_object_id();
        $userId = (string)($attrs['user-id'] ?? get_current_user_id());
        // validate
        $userId = get_user_by('id', $userId)->ID ?? 0;

        $dataPostId = $this->getDataPostId($objectId, $currentPageId, $userId);

        if (!$dataPostId) {
            return $this->getErrorMarkup(
                self::SHORTCODE_VIEWS,
                $attrs,
                __('object-id argument contains the wrong value', 'acf-views')
            );
        }

        // recursionKey must consist from both. It's allowed to use the same View for a post_object field, but with another id
        $recursionKey = $viewId . '-' . $dataPostId;

        /*
         * In case with post_object and relationship fields can be a recursion
         * e.g. There is a post_object field. PostA contains link to PostB. PostB contains link to postA. View displays PostA...
         * In this case just return empty string, without any error message (so user can display PostB in PostA without issues)
         */
        if (isset($this->displayingView[$recursionKey])) {
            return '';
        }

        $this->displayingView[$recursionKey] = true;

        $post = new Post($dataPostId, [], false, $userId);
        $html = $this->acfViewFactory->createAndGetHtml($post, $viewId, $currentPageId);

        unset($this->displayingView[$recursionKey]);

        return $this->maybeAddQuickLink($html, $viewId);
    }

    public function setHooks(): void
    {
        add_shortcode(self::SHORTCODE_VIEWS, [$this, 'acfViewsShortcode']);
        add_shortcode(self::SHORTCODE_CARDS, [$this, 'acfCardsShortcode']);

        add_filter('register_block_type_args', [$this, 'extendGutenbergShortcode'], 10, 2);
    }
}
