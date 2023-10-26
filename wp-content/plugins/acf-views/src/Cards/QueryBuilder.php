<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards;

use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Views\FieldMeta;
use WP_Query;

defined('ABSPATH') || exit;

class QueryBuilder
{
    protected function filterPostsData(
        int $pagesAmount,
        array $postIds,
        string $shortUniqueCardId,
        int $pageNumber,
        WP_Query $query,
        array $queryArgs
    ): array {
        return [
            'pagesAmount' => $pagesAmount,
            'postIds' => $postIds,
        ];
    }

    public function getQueryArgs(CardData $acfCardData, int $pageNumber): array
    {
        $args = [
            'fields' => 'ids',
            'post_type' => $acfCardData->postTypes,
            'post_status' => $acfCardData->postStatuses,
            'posts_per_page' => $acfCardData->limit,
            'order' => $acfCardData->order,
            'orderby' => $acfCardData->orderBy,
            'ignore_sticky_posts' => $acfCardData->isIgnoreStickyPosts,
        ];

        if ($acfCardData->postIn) {
            $args['post__in'] = $acfCardData->postIn;
        }

        if ($acfCardData->postNotIn) {
            $args['post__not_in'] = $acfCardData->postNotIn;
        }

        if (in_array($acfCardData->orderBy, ['meta_value', 'meta_value_num',], true)) {
            $fieldMeta = new FieldMeta($acfCardData->getOrderByMetaAcfFieldId());

            if ($fieldMeta->isFieldExist()) {
                $args['meta_key'] = $fieldMeta->getName();
            }
        }

        return $args;
    }

    public function getPostsData(CardData $acfCardData, int $pageNumber = 1): array
    {
        // stub for tests
        if (!class_exists('WP_Query')) {
            return [
                'pagesAmount' => 0,
                'postIds' => [],
            ];
        }

        $queryArgs = $this->getQueryArgs($acfCardData, $pageNumber);
        $query = new WP_Query($queryArgs);

        $foundPosts = (-1 !== $acfCardData->limit &&
            $query->found_posts > $acfCardData->limit) ?
            $acfCardData->limit :
            $query->found_posts;

        $postsPerPage = $queryArgs['posts_per_page'] ?? 0;

        // otherwise, can be DivisionByZero error
        $pagesAmount = $postsPerPage ?
            (int)ceil($foundPosts / $postsPerPage) :
            0;

        // only ids, as the 'fields' argument is set
        $postIds = $query->get_posts();

        return $this->filterPostsData(
            $pagesAmount,
            $postIds,
            $acfCardData->getUniqueId(true),
            $pageNumber,
            $query,
            $queryArgs
        );
    }
}
