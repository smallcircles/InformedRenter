<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cards\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;

defined('ABSPATH') || exit;

class CardsViewIntegration
{
    protected Cache $cache;
    protected CardsSaveActions $acfCardsSaveActions;

    public function __construct(Cache $cache, CardsSaveActions $acfCardsSaveActions)
    {
        $this->cache = $cache;
        $this->acfCardsSaveActions = $acfCardsSaveActions;
    }

    public function maybeCreateCardForView(): void
    {
        $screen = get_current_screen();
        $from = (int)($_GET['_from'] ?? 0);
        $fromPost = $from ?
            get_post($from) :
            null;

        $isAddScreen = 'post' === $screen->base &&
            'add' === $screen->action;


        if (CardsCpt::NAME !== $screen->post_type ||
            !$isAddScreen ||
            !$fromPost ||
            ViewsCpt::NAME !== $fromPost->post_type ||
            'publish' !== $fromPost->post_status) {
            return;
        }

        $cardId = wp_insert_post([
            'post_type' => CardsCpt::NAME,
            'post_status' => 'publish',
            'post_title' => $fromPost->post_title,
        ]);

        if (is_wp_error($cardId)) {
            return;
        }

        $acfViewData = $this->cache->getAcfViewData($fromPost->ID);
        $acfCardData = $this->cache->getAcfCardData($cardId);

        $acfCardData->acfViewId = $acfViewData->getUniqueId();
        $acfCardData->postTypes[] = 'post';

        $this->acfCardsSaveActions->performSaveActions($cardId);

        wp_redirect(get_edit_post_link($cardId, 'redirect'));
        exit;
    }

    public function setHooks(): void
    {
        add_action('current_screen', [$this, 'maybeCreateCardForView']);
    }
}