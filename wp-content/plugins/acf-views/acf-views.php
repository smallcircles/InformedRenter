<?php
/**
 * Plugin Name: ACF Views
 * Plugin URI: https://wplake.org/acf-views/
 * Description: Smart templates to display your content easily.
 * Version: 2.2.3
 * Author: WPLake
 * Author URI: https://wplake.org/acf-views/
 * Text Domain: acf-views
 */

namespace org\wplake\acf_views;

use org\wplake\acf_views\AcfPro\AcfPro;
use org\wplake\acf_views\Assets\AdminAssets;
use org\wplake\acf_views\Assets\FrontAssets;
use org\wplake\acf_views\Cards\{CardFactory,
    CardMarkup,
    Cpt\CardsCpt,
    Cpt\CardsMetaBoxes,
    Cpt\CardsSaveActions,
    Cpt\CardsViewIntegration,
    QueryBuilder};
use org\wplake\acf_views\Groups\{CardData,
    Integration\AcfIntegration,
    Integration\CardDataIntegration,
    Integration\FieldDataIntegration,
    Integration\ItemDataIntegration,
    Integration\MetaFieldDataIntegration,
    Integration\MountPointDataIntegration,
    Integration\TaxFieldDataIntegration,
    Integration\ToolsDataIntegration,
    ItemData,
    SettingsData,
    ToolsData,
    ViewData};
use org\wplake\acf_views\vendors\LightSource\AcfGroups\Creator;
use org\wplake\acf_views\vendors\LightSource\AcfGroups\Loader as GroupsLoader;
use org\wplake\acf_views\Views\{Cpt\ViewsCpt,
    Cpt\ViewsGroupIntegration,
    Cpt\ViewsMetaBoxes,
    Cpt\ViewsSaveActions,
    Fields\Fields,
    ViewFactory,
    ViewMarkup};

defined('ABSPATH') || exit;

$acfViews = new class {
    protected FieldDataIntegration $fieldIntegration;
    protected Html $html;
    protected Cache $cache;
    protected Twig $twig;
    protected Plugin $plugin;
    protected ItemData $item;
    protected Options $options;
    protected ViewsSaveActions $acfViewsSaveActions;
    protected CardsSaveActions $acfCardsSaveActions;
    protected ViewFactory $acfViewFactory;
    protected CardFactory $acfCardFactory;
    protected ViewData $acfViewData;
    protected CardData $acfCardData;
    protected Creator $groupCreator;
    protected Settings $settings;

    protected function acfGroups(): void
    {
        $acfGroupsLoader = new GroupsLoader();
        $acfGroupsLoader->signUpGroups(
            'org\wplake\acf_views\Groups',
            __DIR__ . '/src/Groups'
        );

        $this->fieldIntegration = new FieldDataIntegration();
        $cardDataIntegration = new CardDataIntegration($this->fieldIntegration);
        $itemIntegration = new ItemDataIntegration();
        $metaFieldIntegration = new MetaFieldDataIntegration($this->fieldIntegration);
        $mountPointIntegration = new MountPointDataIntegration();
        $taxFieldIntegration = new TaxFieldDataIntegration();
        $toolsDataIntegration = new ToolsDataIntegration();

        $this->fieldIntegration->setHooks();
        $cardDataIntegration->setHooks();
        $itemIntegration->setHooks();
        $metaFieldIntegration->setHooks();
        $mountPointIntegration->setHooks();
        $taxFieldIntegration->setHooks();
        $toolsDataIntegration->setHooks();
    }

    protected function acfViews(): void
    {
        $fields = new Fields();
        $viewMarkup = new ViewMarkup($this->html, $fields);
        $this->acfViewFactory = new ViewFactory($this->cache, $viewMarkup, $this->twig, $fields);

        $acfViewMetaBoxes = new ViewsMetaBoxes($this->html, $this->cache);
        $acfViewsCpt = new ViewsCpt($this->cache, $this->html, $acfViewMetaBoxes);
        $this->acfViewsSaveActions = new ViewsSaveActions(
            $this->cache,
            $this->plugin,
            $viewMarkup,
            $acfViewMetaBoxes,
            $this->html,
            $this->acfViewData
        );
        $acfViewGroupIntegration = new ViewsGroupIntegration(
            $this->item, $this->cache, $this->fieldIntegration, $this->acfViewsSaveActions
        );

        $acfViewMetaBoxes->setHooks();
        $acfViewsCpt->setHooks();
        $this->acfViewsSaveActions->setHooks();
        $acfViewGroupIntegration->setHooks();
    }

    protected function acfCards(): void
    {
        $queryBuilder = new QueryBuilder();

        $cardMarkup = new CardMarkup($queryBuilder);
        $this->acfCardFactory = new CardFactory($queryBuilder, $cardMarkup, $this->twig);
        $acfCardsMetaBoxes = new CardsMetaBoxes($this->html, $this->cache);
        $acfCardsCpt = new CardsCpt($this->cache, $this->html, $acfCardsMetaBoxes);
        $this->acfCardsSaveActions = new CardsSaveActions(
            $this->cache, $this->plugin, $cardMarkup, $queryBuilder,
            $this->html, $acfCardsMetaBoxes, $this->acfCardData
        );
        $acfCardsViewIntegration = new CardsViewIntegration($this->cache, $this->acfCardsSaveActions);

        $acfCardsCpt->setHooks();
        $acfCardsMetaBoxes->setHooks();
        $this->acfCardsSaveActions->setHooks();
        $acfCardsViewIntegration->setHooks();
    }

    protected function primary(): void
    {
        $this->groupCreator = new Creator();
        $this->acfViewData = $this->groupCreator->create(ViewData::class);
        $this->acfCardData = $this->groupCreator->create(CardData::class);
        $this->options = new Options();
        $this->html = new Html();
        $this->cache = new Cache($this->acfViewData, $this->acfCardData);
        $this->settings = new Settings($this->options);
        $this->twig = new Twig($this->settings);
        $this->plugin = new Plugin($this->options);
        $this->item = $this->groupCreator->create(ItemData::class);

        // load right here, as used everywhere
        $this->settings->load();

        $this->plugin->setHooks();
    }

    protected function others(): void
    {
        $demoImport = new DemoImport(
            $this->acfViewsSaveActions,
            $this->settings,
            $this->item,
            $this->acfCardsSaveActions,
            $this->cache
        );
        $acfIntegration = new AcfIntegration();
        $dashboard = new Dashboard($this->plugin, $this->html, $demoImport, $acfIntegration);
        $acfPro = new AcfPro($this->plugin);
        $upgrades = new Upgrades(
            $this->plugin,
            $this->settings,
            $this->cache,
            $this->acfViewsSaveActions,
            $this->acfCardsSaveActions
        );
        $activeInstallations = new ActiveInstallations($this->plugin, $this->settings, $this->options);
        $shortcodes = new Shortcodes($this->acfViewFactory, $this->acfCardFactory, $this->cache, $this->settings);
        $tools = new Tools(
            new ToolsData($this->groupCreator),
            $this->cache,
            $this->plugin,
            $this->acfViewData,
            $this->acfCardData
        );
        $settings = new SettingsPage(new SettingsData($this->groupCreator), $this->settings);
        $adminAssets = new AdminAssets(
            $this->plugin, $this->cache, $this->acfViewFactory, $this->acfCardFactory,
            $this->settings
        );
        $frontAssets = new FrontAssets($this->plugin, $this->acfViewFactory, $this->acfCardFactory);

        $dashboard->setHooks();
        $demoImport->setHooks();
        $acfPro->setHooks();
        $upgrades->setHooks();
        $activeInstallations->setHooks();
        $shortcodes->setHooks();
        $tools->setHooks();
        $adminAssets->setHooks();
        $frontAssets->setHooks();
        $settings->setHooks();
    }

    public function init(): void
    {
        // skip initialization if PRO already active
        if (class_exists(Plugin::class)) {
            return;
        }

        require_once __DIR__ . '/prefixed_vendors/vendor/scoper-autoload.php';

        $this->acfGroups();
        $this->primary();
        $this->acfViews();
        $this->acfCards();
        $this->others();
    }
};

$acfViews->init();
