<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cpt\MetaBoxes;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Shortcodes;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use WP_Post;

defined('ABSPATH') || exit;

class CardsMetaBoxes extends MetaBoxes
{
    protected Cache $cache;

    public function __construct(Html $html, Cache $cache)
    {
        parent::__construct($html);

        $this->cache = $cache;
    }

    protected function getCptName(): string
    {
        return CardsCpt::NAME;
    }

    public function printRelatedAcfViewMetaBox(
        WP_Post $post,
        bool $isIgnorePrint = false,
        bool $isSkipNotFoundMessage = false
    ): string {
        $message = __('No related ACF View.', 'acf-views');

        if (!$post->post_content_filtered) {
            if (!$isIgnorePrint &&
                !$isSkipNotFoundMessage) {
                echo $message;
            }

            return $message;
        }

        $acfViewUniqueId = $post->post_content_filtered;

        $acfViewId = $this->cache->getPostIdByUniqueId($acfViewUniqueId, ViewsCpt::NAME);

        if (!$acfViewId) {
            if (!$isIgnorePrint &&
                !$isSkipNotFoundMessage) {
                echo $message;
            }

            return $message;
        }

        $content = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            get_edit_post_link($acfViewId),
            get_the_title($acfViewId)
        );

        if (!$isIgnorePrint) {
            echo $content;
        }

        return $content;
    }

    public function addMetaboxes(): void
    {
        add_meta_box(
            'acf-cards_shortcode_cpt',
            __('Shortcode', 'acf-views'),
            function ($post, $meta) {
                if (!$post ||
                    'publish' !== $post->post_status) {
                    echo __('Your Card shortcode is available after publishing.', 'acf-views');

                    return;
                }

                $cardUniqueId = $this->cache->getAcfCardData($post->ID)->getUniqueId(true);
                echo $this->html->postboxShortcodes(
                    $cardUniqueId,
                    false,
                    Shortcodes::SHORTCODE_CARDS,
                    get_the_title($post),
                    true
                );
            },
            [
                CardsCpt::NAME,
            ],
            'side',
            // right after the publish button
            'core'
        );

        add_meta_box(
            'acf-cards_related_view',
            __('Related View', 'acf-views'),
            function (WP_Post $post) {
                $this->printRelatedAcfViewMetaBox($post);
            },
            [
                CardsCpt::NAME,
            ],
            'side',
            'core'
        );

        parent::addMetaboxes();
    }
}
