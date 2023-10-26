<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Assets;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cards\CardFactory;
use org\wplake\acf_views\Cards\Cpt\CardsCpt;
use org\wplake\acf_views\Groups\CardData;
use org\wplake\acf_views\Groups\FieldData;
use org\wplake\acf_views\Groups\ItemData;
use org\wplake\acf_views\Groups\MetaFieldData;
use org\wplake\acf_views\Groups\RepeaterFieldData;
use org\wplake\acf_views\Groups\TaxFieldData;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Plugin;
use org\wplake\acf_views\Settings;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use org\wplake\acf_views\Views\Post;
use org\wplake\acf_views\Views\ViewFactory;

defined('ABSPATH') || exit;

class AdminAssets
{
    /**
     * @var Plugin
     */
    protected $plugin;
    protected Cache $cache;
    protected ViewFactory $acfViewFactory;
    protected CardFactory $acfCardFactory;
    protected Settings $settings;

    public function __construct(
        Plugin $plugin,
        Cache $cache,
        ViewFactory $acfViewFactory,
        CardFactory $acfCardFactory,
        Settings $settings
    ) {
        $this->plugin = $plugin;
        $this->cache = $cache;
        $this->acfViewFactory = $acfViewFactory;
        $this->acfCardFactory = $acfCardFactory;
        $this->settings = $settings;
    }

    protected function getViewPreviewJsData(): array
    {
        $jsData = [
            'HTML' => '',
            'CSS' => '',
        ];

        global $post;

        if (!$this->plugin->isCPTScreen(ViewsCpt::NAME) ||
            'publish' !== $post->post_status) {
            return $jsData;
        }

        $acfViewData = $this->cache->getAcfViewData($post->ID);
        $previewPostId = $acfViewData->previewPost ?: 0;

        if ($previewPostId) {
            $postData = new Post($previewPostId, [], false, get_current_user_id());
            // without minify, it's a preview
            $viewHTML = $this->acfViewFactory->createAndGetHtml(
                $postData,
                $post->ID,
                0,
                false,
            );
        } else {
            // $this->viewMarkup->getMarkup give TWIG, there is no sense to show it
            // so the HTML is empty until the preview Post ID is selected
            $viewHTML = '';
        }

        // amend to allow work the '#view' alias
        $viewHTML = str_replace('class="acf-view ', 'id="view" class="acf-view ', $viewHTML);
        $jsData['HTML'] = htmlentities($viewHTML, ENT_QUOTES);

        $jsData['CSS'] = htmlentities($acfViewData->getCssCode(false, true), ENT_QUOTES);
        $jsData['HOME'] = get_site_url();

        return $jsData;
    }

    protected function getCardPreviewJsData(): array
    {
        $jsData = [
            'HTML' => '',
            'CSS' => '',
        ];

        global $post;

        if (!$this->plugin->isCPTScreen(CardsCpt::NAME) ||
            'publish' !== $post->post_status) {
            return $jsData;
        }

        $acfCardData = $this->cache->getAcfCardData($post->ID);
        $acfCardHtml = $this->acfCardFactory->createAndGetHtml($acfCardData, 1, false);
        $viewId = $this->cache->getPostIdByUniqueId($acfCardData->acfViewId, ViewsCpt::NAME);

        if (!$viewId) {
            return $jsData;
        }

        $acfViewData = $this->cache->getAcfViewData($viewId);

        // amend to allow work the '#card' alias
        $viewHTML = str_replace(
            'class="acf-card ',
            'id="card" class="acf-card ',
            $acfCardHtml
        );
        $jsData['HTML'] = htmlentities($viewHTML, ENT_QUOTES);
        // Card CSS without minification as it's for views' purposes
        $jsData['CSS'] = htmlentities($acfCardData->getCssCode(false, true), ENT_QUOTES);
        $jsData['VIEW_CSS'] = htmlentities($acfViewData->getCssCode(), ENT_QUOTES);
        $jsData['HOME'] = get_site_url();

        return $jsData;
    }

    protected function enqueueCodeEditor(): void
    {
        wp_enqueue_script(
            ViewsCpt::NAME . '_ace',
            $this->plugin->getAssetsUrl('admin/code-editor/ace.js'),
            [],
            $this->plugin->getVersion()
        );

        wp_enqueue_script(
            ViewsCpt::NAME . '_ace-ext-beautify',
            $this->plugin->getAssetsUrl('admin/code-editor/ext-beautify.js'),
            [
                ViewsCpt::NAME . '_ace',
            ],
            $this->plugin->getVersion()
        );
    }

    protected function enqueueAdminAssets(string $currentBase, array $jsData = []): void
    {
        switch ($currentBase) {
            // add, edit pages
            case 'post':
                $jsData = array_merge_recursive($jsData, [
                    'mods' => [
                        '_twig' => [
                            'mode' => 'ace/mode/twig',
                        ],
                        '_css' => [
                            'mode' => 'ace/mode/css',
                        ],
                        '_js' => [
                            'mode' => 'ace/mode/javascript',
                        ],
                        '_php' => [
                            'mode' => 'ace/mode/php',
                        ],
                    ],
                    'markupTextarea' => [
                        [
                            'idSelector' => ViewData::getAcfFieldName(ViewData::FIELD_MARKUP),
                            'isReadOnly' => true,
                            'mode' => '_twig',
                        ],
                        [
                            'idSelector' => ViewData::getAcfFieldName(ViewData::FIELD_CSS_CODE),
                            'isReadOnly' => false,
                            'mode' => '_css',
                        ],
                        [
                            'idSelector' => ViewData::getAcfFieldName(ViewData::FIELD_JS_CODE),
                            'isReadOnly' => false,
                            'mode' => '_js',
                        ],
                        [
                            'idSelector' => ViewData::getAcfFieldName(ViewData::FIELD_CUSTOM_MARKUP),
                            'isReadOnly' => false,
                            'mode' => '_twig',
                        ],
                        [
                            'idSelector' => ViewData::getAcfFieldName(ViewData::FIELD_PHP_VARIABLES),
                            'isReadOnly' => false,
                            'mode' => '_php',
                        ],
                        [
                            'idSelector' => CardData::getAcfFieldName(CardData::FIELD_MARKUP),
                            'isReadOnly' => true,
                            'mode' => '_twig',
                        ],
                        [
                            'idSelector' => CardData::getAcfFieldName(CardData::FIELD_CSS_CODE),
                            'isReadOnly' => false,
                            'mode' => '_css',
                        ],
                        [
                            'idSelector' => CardData::getAcfFieldName(CardData::FIELD_JS_CODE),
                            'isReadOnly' => false,
                            'mode' => '_js',
                        ],
                        [
                            'idSelector' => CardData::getAcfFieldName(
                                CardData::FIELD_CUSTOM_MARKUP
                            ),
                            'isReadOnly' => false,
                            'mode' => '_twig',
                        ],
                        [
                            'idSelector' => CardData::getAcfFieldName(CardData::FIELD_QUERY_PREVIEW),
                            'isReadOnly' => true,
                            'mode' => '_twig',
                        ],
                    ],
                    'fieldSelect' => [
                        [
                            'mainSelectId' => ItemData::getAcfFieldName(ItemData::FIELD_GROUP),
                            'subSelectId' => FieldData::getAcfFieldName(FieldData::FIELD_KEY),
                            'identifierInputId' => FieldData::getAcfFieldName(FieldData::FIELD_ID),
                        ],
                        [
                            'mainSelectId' => CardData::getAcfFieldName(
                                CardData::FIELD_ORDER_BY_META_FIELD_GROUP
                            ),
                            'subSelectId' => CardData::getAcfFieldName(CardData::FIELD_ORDER_BY_META_FIELD_KEY),
                            'identifierInputId' => '',
                        ],
                        [
                            'mainSelectId' => FieldData::getAcfFieldName(FieldData::FIELD_KEY),
                            'subSelectId' => RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_KEY),
                            'identifierInputId' => RepeaterFieldData::getAcfFieldName(RepeaterFieldData::FIELD_ID),
                        ],
                        [
                            'mainSelectId' => MetaFieldData::getAcfFieldName(MetaFieldData::FIELD_GROUP),
                            'subSelectId' => MetaFieldData::getAcfFieldName(MetaFieldData::FIELD_FIELD_KEY),
                            'identifierInputId' => '',
                        ],
                        [
                            'mainSelectId' => TaxFieldData::getAcfFieldName(TaxFieldData::FIELD_TAXONOMY),
                            'subSelectId' => TaxFieldData::getAcfFieldName(TaxFieldData::FIELD_TERM),
                            'identifierInputId' => '',
                        ],
                    ],
                    'viewPreview' => $this->getViewPreviewJsData(),
                    'cardPreview' => $this->getCardPreviewJsData(),
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'preferences' => [
                        'isNotCollapsedFieldsByDefault' => $this->settings->isNotCollapsedFieldsByDefault(),
                        'isWithoutFieldsCollapseCursor' => $this->settings->isWithoutFieldsCollapseCursor(),
                    ]
                ]);

                $this->enqueueCodeEditor();

                wp_enqueue_style(
                    ViewsCpt::NAME . '_cpt-item',
                    $this->plugin->getAssetsUrl('admin/css/cpt-item.min.css'),
                    [],
                    $this->plugin->getVersion()
                );
                // jquery is necessary for select2 events
                wp_enqueue_script(
                    ViewsCpt::NAME . '_cpt-item',
                    $this->plugin->getAssetsUrl('admin/js/cpt-item.min.js'),
                    // make sure acf and ACE editor are loaded
                    ['jquery', 'acf-input', ViewsCpt::NAME . '_ace',],
                    $this->plugin->getVersion(),
                    [
                        'in_footer' => true,
                        // in footer, so if we need to include others, like 'ace.js' we can include in header
                    ]
                );
                wp_localize_script(ViewsCpt::NAME . '_cpt-item', 'acf_views', $jsData);
                break;
            // 'edit' means 'list page'
            case 'edit':
                wp_enqueue_style(
                    ViewsCpt::NAME . '_list-page',
                    $this->plugin->getAssetsUrl('admin/css/list-page.min.css'),
                    [],
                    $this->plugin->getVersion()
                );
                break;
            case 'acf_views_page_acf-views-tools':
            case 'acf_views_page_acf-views-settings':
                wp_enqueue_style(
                    ViewsCpt::NAME . '_tools',
                    $this->plugin->getAssetsUrl('admin/css/tools.min.css'),
                    [],
                    $this->plugin->getVersion()
                );
                break;
        }

        // 'dashboard' for all the custom pages (but not for edit/add pages)
        if (0 === strpos($currentBase, 'acf_views_page_')) {
            wp_enqueue_style(
                ViewsCpt::NAME . '_page',
                $this->plugin->getAssetsUrl('admin/css/dashboard.min.css'),
                [],
                $this->plugin->getVersion()
            );
        }

        // plugin-header for all the pages without exception
        wp_enqueue_style(
            ViewsCpt::NAME . '_common',
            $this->plugin->getAssetsUrl('admin/css/common.min.css'),
            [],
            $this->plugin->getVersion()
        );
    }

    public function enqueueAdminScripts(): void
    {
        $currentScreen = get_current_screen();
        if (!$currentScreen ||
            (!in_array($currentScreen->id, [ViewsCpt::NAME, CardsCpt::NAME,], true) &&
                !in_array($currentScreen->post_type, [ViewsCpt::NAME, CardsCpt::NAME], true))) {
            return;
        }

        $this->enqueueAdminAssets($currentScreen->base);
    }

    public function setHooks(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
    }
}
