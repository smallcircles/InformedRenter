<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\vendors\LightSource\AcfGroups\Interfaces\AcfGroupInterface;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use WP_Query;

defined('ABSPATH') || exit;

/**
 * Avoid querying and parsing View/Card's fields multiple times
 * (e.g. one Card can call View's shortcode 10 times, it's better to save than create objects every time
 * (parsing json + objects (its fields) creation))
 *
 * There are more internal cache in the plugin:
 * 1. FieldsMeta (AcfViewData class; avoid calling 'get_field_object()' for every field multiple times)
 * 2. ViewMarkup (ViewMarkup class; save time for processing)
 */
class Cache
{
    private ViewData $acfViewData;
    private CardData $acfCardData;

    /**
     * @var AcfGroupInterface[]
     */
    private array $posts;

    public function __construct(ViewData $acfViewData, CardData $acfCardData)
    {
        $this->acfViewData = $acfViewData->getDeepClone();
        $this->acfCardData = $acfCardData->getDeepClone();

        $this->posts = [];
    }

    public function getPostIdByUniqueId(string $uniqueId, string $postType): ?int
    {
        if (!is_numeric($uniqueId)) {
            if (false === strpos($uniqueId, '_')) {
                $prefix = ViewsCpt::NAME === $postType ?
                    'view_' :
                    'card_';
                $uniqueId = $prefix . $uniqueId;
            }

            $query = new WP_Query([
                'post_type' => $postType,
                'post_name__in' => [$uniqueId],
                'posts_per_page' => 1,
            ]);
            $post = $query->get_posts()[0] ?? null;
        } // keep back compatibility for direct postIds (for shortcodes that were already pasted)
        else {
            $post = get_post($uniqueId);
        }

        if ($post &&
            in_array($post->post_type, [$postType,], true) &&
            'publish' === $post->post_status
        ) {
            return $post->ID;
        }

        return null;
    }

    public function getAcfViewData(int $viewId): ViewData
    {
        if (key_exists($viewId, $this->posts)) {
            return $this->posts[$viewId];
        }

        $acfViewData = $this->acfViewData->getDeepClone();
        $acfViewData->loadFromPostContent($viewId);

        $this->posts[$viewId] = $acfViewData;

        return $acfViewData;
    }

    public function getAcfCardData(int $cardId): CardData
    {
        if (key_exists($cardId, $this->posts)) {
            return $this->posts[$cardId];
        }

        $acfCardData = $this->acfCardData->getDeepClone();
        $acfCardData->loadFromPostContent($cardId);

        $this->posts[$cardId] = $acfCardData;

        return $acfCardData;
    }
}
