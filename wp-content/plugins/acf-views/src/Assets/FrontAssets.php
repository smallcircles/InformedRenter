<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Assets;

use org\wplake\acf_views\Cards\CardFactory;
use org\wplake\acf_views\Plugin;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use org\wplake\acf_views\Views\ViewFactory;

defined('ABSPATH') || exit;

class FrontAssets
{
    /**
     * @var Plugin
     */
    protected $plugin;
    /**
     * @var ViewFactory
     */
    protected $acfViewFactory;
    /**
     * @var CardFactory
     */
    protected $acfCardFactory;
    protected int $bufferLevel;

    public function __construct(
        Plugin $plugin,
        ViewFactory $acfViewFactory,
        CardFactory $acfCardFactory
    ) {
        $this->plugin = $plugin;
        $this->acfViewFactory = $acfViewFactory;
        $this->acfCardFactory = $acfCardFactory;
        $this->bufferLevel = 0;
    }

    protected function printPluginsCSS(): string
    {
        return '';
    }

    public function startBuffering(): void
    {
        ob_start();
        $this->bufferLevel = ob_get_level();
    }

    public function printStylesStub(): void
    {
        echo '<!--acf-views-styles-->';
    }

    public function enqueueGoogleMapsJS(): void
    {
        if (!function_exists('acf_get_setting') ||
            !$this->acfViewFactory->getMaps()) {
            return;
        }

        $apiData = apply_filters('acf/fields/google_map/api', []);

        $key = $apiData['key'] ?? '';

        $key = !$key ?
            acf_get_setting('google_api_key') :
            $key;

        if (!$key) {
            return;
        }

        wp_enqueue_script(
            ViewsCpt::NAME . '_maps',
            $this->plugin->getAssetsUrl('front/js/maps.min.js'),
            [],
            $this->plugin->getVersion(),
            [
                'in_footer' => true,
            ]
        );

        wp_localize_script(
            ViewsCpt::NAME . '_maps',
            'acfViewsMaps',
            $this->acfViewFactory->getMaps()
        );

        wp_enqueue_script(
            ViewsCpt::NAME . '_google-maps',
            sprintf('https://maps.googleapis.com/maps/api/js?key=%s&callback=acfViewsGoogleMaps', $key),
            [
                // setup deps, to make sure loaded only after plugin's maps.min.js
                ViewsCpt::NAME . '_maps',
            ],
            null,
            [
                'in_footer' => true,
            ]
        );
    }

    public function printCustomAssets(): void
    {
        $allJsCode = '';
        $allCssCode = $this->printPluginsCSS();

        $views = $this->acfViewFactory->getRenderedViews();
        foreach ($views as $view) {
            $cssCode = $view->getCssCode();

            // 'minify' JS
            $jsCode = str_replace(["\t", "\n", "\r"], '', $view->jsCode);
            $jsCode = trim($jsCode);

            // no escaping, it's a CSS code, so e.g '.a > .b' shouldn't be escaped
            $allCssCode .= $cssCode ?
                sprintf("\n/*view-%s*/\n%s", $view->getSource(), $cssCode) :
                '';
            $allJsCode .= $jsCode ?
                sprintf("\n/*view-%s*/\n%s", $view->getSource(), $jsCode) :
                '';
        }

        $cards = $this->acfCardFactory->getRenderedCards();
        foreach ($cards as $card) {
            $cssCode = $card->getCssCode();

            // 'minify' JS
            $jsCode = str_replace(["\t", "\n", "\r"], '', $card->jsCode);
            $jsCode = trim($jsCode);

            // no escaping, it's a CSS code, so e.g '.a > .b' shouldn't be escaped
            $allCssCode .= $cssCode ?
                sprintf("\n/*card-%s*/\n%s", $card->getSource(), $cssCode) :
                '';
            $allJsCode .= $jsCode ?
                sprintf("\n/*card-%s*/\n%s", $card->getSource(), $jsCode) :
                '';
        }

        if (!$allCssCode &&
            !$allJsCode) {
            // do not close the buffer, if it's not ours
            // (then ours will be closed automatically with the end of script execution)
            if (ob_get_level() === $this->bufferLevel) {
                echo ob_get_clean();
            }

            return;
        }

        // close previous buffers. Some plugins may not close, if detect that ob_get_level() is another than was
        // e.g. 'lightbox-photoswipe'
        while (ob_get_level() > $this->bufferLevel) {
            echo ob_get_clean();
        }

        $pageContent = ob_get_clean();
        $cssTag = $allCssCode ?
            sprintf("<style data-acf-views-css='css'>%s</style>", $allCssCode) :
            '';
        $pageContent = str_replace('<!--acf-views-styles-->', $cssTag, $pageContent);

        echo $pageContent;

        if ($allJsCode) {
            printf("<script data-acf-views-js='js'>(function (){%s}())</script>", $allJsCode);
        }
    }

    public function makeEnqueueJSAsync(string $tag, string $handle): string
    {
        if (!in_array($handle, [
            ViewsCpt::NAME . '_maps',
            ViewsCpt::NAME . '_google-maps'
        ], true)) {
            return $tag;
        }

        // defer, not async as order should be kept (google-maps will call a callback from maps' js)
        return str_replace(' src', ' defer src', $tag);
    }

    public function setHooks(): void
    {
        add_action('wp_footer', [$this, 'enqueueGoogleMapsJS']);
        // printCustomAssets() contains ob_get_clean, so must be executed after all other scripts
        add_action('wp_footer', [$this, 'printCustomAssets'], 9999);
        add_action('wp_head', [$this, 'printStylesStub']);
        // don't use 'get_header', as it doesn't work in blocks theme
        add_action('template_redirect', [$this, 'startBuffering']);
        add_filter('script_loader_tag', [$this, 'makeEnqueueJSAsync'], 10, 2);
    }
}
