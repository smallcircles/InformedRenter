<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cards\Cpt\CardsCpt;
use org\wplake\acf_views\Cpt\MetaBoxes;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Shortcodes;
use WP_Post;

defined('ABSPATH') || exit;

class ViewsMetaBoxes extends MetaBoxes
{
    protected Cache $cache;

    public function __construct(Html $html, Cache $cache)
    {
        parent::__construct($html);

        $this->cache = $cache;
    }

    protected function getCptName(): string
    {
        return ViewsCpt::NAME;
    }

    protected function getJsHover(): string
    {
        return 'onMouseOver="this.style.filter=\'brightness(30%)\'" onMouseOut="this.style.filter=\'brightness(100%)\'"';
    }

    public function printRelatedAcfGroupsMetaBox(
        WP_Post $post,
        bool $isIgnorePrint = false,
        bool $isSkipNotFoundMessage = false
    ): string {
        if (!$post->post_content_filtered) {
            $message = __('No assigned ACF Groups.', 'acf-views');

            if (!$isIgnorePrint &&
                !$isSkipNotFoundMessage) {
                echo $message;
            }

            return $message;
        }

        $acfGroupKeys = explode(',', $post->post_content_filtered);
        $links = [];

        foreach ($acfGroupKeys as $acfGroupKey) {
            $acfGroupId = acf_get_field_group($acfGroupKey)['ID'] ?? '';

            if (!$acfGroupId) {
                continue;
            }

            $links[] = sprintf(
                '<a href="%s" target="_blank" style="transition: all .3s ease;" %s>%s</a>',
                get_edit_post_link($acfGroupId),
                $this->getJsHover(),
                get_the_title($acfGroupId)
            );
        }

        $content = implode(', ', $links);

        if (!$isIgnorePrint) {
            echo $content;
        }

        return $content;
    }

    public function getRelatedAcfCardsMetaBox(WP_Post $post, bool $isListLook = false): string
    {
        $content = '';
        $message = __('Not assigned to any ACF Cards.', 'acf-views');
        $message = '<p>' . $message . '</p>';

        if ('publish' !== $post->post_status) {
            if (!$isListLook) {
                $content .= $message;
            }

            return $content;
        }

        global $wpdb;

        $acfViewData = $this->cache->getAcfViewData($post->ID);

        $query = $wpdb->prepare(
            "SELECT * from {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'
                      AND FIND_IN_SET(%s,post_content_filtered) > 0",
            CardsCpt::NAME,
            $acfViewData->getUniqueId()
        );
        $relatedCards = $wpdb->get_results($query);

        if (!$relatedCards &&
            !$isListLook) {
            $content .= $message;
        }

        $links = [];

        foreach ($relatedCards as $relatedCard) {
            $links[] = sprintf(
                '<a href="%s" target="_blank" style="transition: all .3s ease;" %s>%s</a>',
                get_edit_post_link($relatedCard),
                $this->getJsHover(),
                get_the_title($relatedCard)
            );
        }

        $content .= $links ?
            implode(', ', $links) :
            '';

        if ($relatedCards ||
            $isListLook) {
            $content .= '<br><br>';
        }


        $label = __('Add new', 'acf-views');
        $style = 'min-height: 0;line-height: 1.2;padding: 3px 7px;font-size:11px;transition:all .3s ease;';
        $content .= sprintf(
            '<a href="%s" target="_blank" class="button" style="%s">%s</a>',
            admin_url('/post-new.php?post_type=acf_cards&_from=' . $post->ID),
            $style,
            $label
        );

        return $content;
    }

    public function addMetaboxes(): void
    {
        add_meta_box(
            'acf-views_shortcode',
            __('Shortcode', 'acf-views'),
            function ($post, $meta) {
                if (!$post ||
                    'publish' !== $post->post_status) {
                    echo __('Your View shortcode is available after publishing.', 'acf-views');

                    return;
                }

                $viewUniqueId = $this->cache->getAcfViewData($post->ID)->getUniqueId(true);
                echo $this->html->postboxShortcodes(
                    $viewUniqueId,
                    false,
                    Shortcodes::SHORTCODE_VIEWS,
                    get_the_title($post),
                    false
                );
            },
            [
                $this->getCptName(),
            ],
            'side'
        );

        add_meta_box(
            'acf-views_related_groups',
            __('Assigned Groups', 'acf-views'),
            function (WP_Post $post) {
                $this->printRelatedAcfGroupsMetaBox($post);
            },
            [
                $this->getCptName(),
            ],
            'side'
        );

        add_meta_box(
            'acf-views_related_cards',
            __('Assigned to Cards', 'acf-views'),
            function (WP_Post $post) {
                echo $this->getRelatedAcfCardsMetaBox($post);
            },
            [
                $this->getCptName(),
            ],
            'side'
        );

        parent::addMetaboxes();
    }

}
