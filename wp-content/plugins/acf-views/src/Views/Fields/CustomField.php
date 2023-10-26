<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Fields;

use WC_Product;
use WP_Post;
use WP_User;

defined('ABSPATH') || exit;

trait CustomField
{
    protected function getPost($notFormattedValue): ?WP_Post
    {
        $notFormattedValue = $notFormattedValue ?
            (array)$notFormattedValue :
            [];

        $postId = (int)($notFormattedValue[0] ?? 0);

        return $postId ?
            // returns null if post doesn't exist
            get_post($postId) :
            null;
    }

    protected function getUser($notFormattedValue): ?WP_User
    {
        $notFormattedValue = $notFormattedValue ?
            (array)$notFormattedValue :
            [];

        $userId = (int)($notFormattedValue[0] ?? 0);

        $user = $userId ?
            // returns false if user doesn't exist
            get_user_by('id', $userId) :
            false;

        return $user ?: null;
    }

    protected function getProduct($notFormattedValue): ?WC_Product
    {
        $notFormattedValue = $notFormattedValue ?
            (array)$notFormattedValue :
            [];

        $postId = (int)($notFormattedValue[0] ?? 0);

        $product = ($postId &&
            function_exists('wc_get_product')) ?
            wc_get_product($postId) :
            null;

        // extra check, as can be false (we need null)
        return $product ?: null;
    }
}
