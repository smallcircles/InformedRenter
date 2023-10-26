<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use org\wplake\acf_views\Cards\Cpt\CardsCpt;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;

defined('ABSPATH') || exit;

class Plugin
{
    const DOCS_URL = 'https://docs.acfviews.com/getting-started/acf-views-for-wordpress';
    const PRO_VERSION_URL = 'https://wplake.org/acf-views-pro/';
    const PRO_PRICING_URL = 'https://wplake.org/acf-views-pro/#pricing';
    const BASIC_VERSION_URL = 'https://wplake.org/acf-views/';
    const ACF_INSTALL_URL = 'plugin-install.php?s=deliciousbrains&tab=search&type=author';
    const SURVEY_URL = 'https://forms.gle/Wjb16B4mzgLEQvru6';
    const CONFLICTS_URL = 'https://docs.acfviews.com/getting-started/compatibility#conflicts';

    protected string $slug = 'acf-views/acf-views.php';
    protected string $shortSlug = 'acf-views';
    protected string $version = '2.2.3';
    protected bool $isProVersion = false;

    protected Options $options;
    protected Twig $twig;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // static, as called also in AcfGroup
    public static function isAcfProPluginAvailable(): bool
    {
        return class_exists('acf_pro');
    }

    public static function getThemeTextDomain(): string
    {
        return (string)wp_get_theme()->get('TextDomain');
    }

    public static function getLabelTranslation(string $label, string $textDomain = ''): string
    {
        $textDomain = $textDomain ?: self::getThemeTextDomain();

        // escape quotes to keep compatibility with the generated translation file
        // (quotes there escaped to prevent breaking the PHP string)
        $label = str_replace("'", "&#039;", $label);
        $label = str_replace('"', "&quot;", $label);

        $translation = __($label, $textDomain);

        $translation = str_replace("&#039;", "'", $translation);
        $translation = str_replace("&quot;", '"', $translation);

        return $translation;
    }

    public function getName(): string
    {
        return __('ACF Views', 'acf-views');
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getShortSlug(): string
    {
        return $this->shortSlug;
    }

    public function getVersion(): string
    {
        // return strval(time());

        return $this->version;
    }

    public function isProVersion(): bool
    {
        return $this->isProVersion;
    }

    public function getAssetsUrl(string $file): string
    {
        return plugin_dir_url(__FILE__) . 'Assets/' . $file;
    }

    public function getAcfProAssetsUrl(string $file): string
    {
        return plugin_dir_url(__FILE__) . 'AcfPro/assets/' . $file;
    }

    public function isAcfPluginAvailable(bool $isProOnly = false): bool
    {
        // don't use 'is_plugin_active()' as the function available lately
        return static::isAcfProPluginAvailable() ||
            (!$isProOnly && class_exists('ACF'));
    }

    public function showWarningAboutInactiveAcfPlugin(): void
    {
        if ($this->isAcfPluginAvailable()) {
            return;
        }

        $acfPluginInstallLink = get_admin_url(null, static::ACF_INSTALL_URL);
        $acfFree = 'https://wordpress.org/plugins/advanced-custom-fields/';
        $acfPro = 'https://www.advancedcustomfields.com/pro/';

        echo sprintf(
            '<div class="notice notice-error">' .
            '<p>%s <a target="_blank" href="%s">%s</a> (<a target="_blank" href="%s">%s</a> %s <a target="_blank" href="%s">%s</a>) %s</p>' .
            '</div>',
            __('"ACF Views" requires', 'acf-views'),
            $acfPluginInstallLink,
            __('Advanced Custom Fields', 'acf-views'),
            $acfFree,
            __('free', 'acf-views'),
            __('or', 'acf-views'),
            $acfPro,
            __('pro', 'acf-views'),
            __('to be installed and activated.', 'acf-views'),
        );
    }

    public function showWarningAboutOpcacheIssue(): void
    {
        if (!function_exists('ini_get') ||
            '0' !== ini_get('opcache.save_comments')) {
            return;
        }

        $readMoreLink = sprintf(
            '<a target="_blank" href="%s">%s</a>',
            self::CONFLICTS_URL,
            __('Read more', 'acf-views')
        );
        printf(
            '<div class="notice notice-error"><p>%s 
<br>%s %s
</p></div>',
            __('Compatibility issue detected! "ACF Views" plugin requires "PHPDoc" comments in code.', 'acf-views'),
            __(
                'Please change the "opcache.save_comments" option in your php.ini file to the default value of "1" on your hosting.',
                'acf-views'
            ),
            $readMoreLink
        );
    }

    public function isCPTScreen(string $cptName, array $targetBase = ['post', 'add',]): bool
    {
        $currentScreen = get_current_screen();

        $isTargetPost = in_array($currentScreen->id, [$cptName,], true) ||
            in_array($currentScreen->post_type, [$cptName], true);

        // base = edit (list management), post (editing), add (adding)
        return $isTargetPost &&
            in_array($currentScreen->base, $targetBase, true);
    }

    public function deactivateOtherInstances(string $activatedPlugin): void
    {
        if (!in_array($activatedPlugin, ['acf-views/acf-views.php', 'acf-views-pro/acf-views-pro.php'], true)) {
            return;
        }

        $pluginToDeactivate = 'acf-views/acf-views.php';
        $deactivatedNoticeId = 1;

        // If we just activated the free version, deactivate the pro version.
        if ($activatedPlugin === $pluginToDeactivate) {
            $pluginToDeactivate = 'acf-views-pro/acf-views-pro.php';
            $deactivatedNoticeId = 2;
        }

        if (is_multisite() &&
            is_network_admin()) {
            $activePlugins = (array)get_site_option('active_sitewide_plugins', []);
            $activePlugins = array_keys($activePlugins);
        } else {
            $activePlugins = (array)get_option('active_plugins', []);
        }

        foreach ($activePlugins as $pluginBasename) {
            if ($pluginToDeactivate !== $pluginBasename) {
                continue;
            }

            $this->options->setTransient(
                Options::TRANSIENT_DEACTIVATED_OTHER_INSTANCES,
                $deactivatedNoticeId,
                1 * HOUR_IN_SECONDS
            );
            deactivate_plugins($pluginBasename);

            return;
        }
    }

    // notice when either Basic or Pro was automatically deactivated
    public function showPluginDeactivatedNotice(): void
    {
        $deactivatedNoticeId = (int)$this->options->getTransient(Options::TRANSIENT_DEACTIVATED_OTHER_INSTANCES);

        // not set = false = 0
        if (!in_array($deactivatedNoticeId, [1, 2,], true)) {
            return;
        }

        $message = sprintf(
            '%s "%s".',
            __(
                "'ACF Views' and 'ACF Views Pro' should not be active at the same time. We've automatically deactivated",
                'acf-views'
            ),
            1 === $deactivatedNoticeId ?
                __('ACF Views', 'acf-views') :
                __('ACF Views Pro', 'acf-views')
        );

        $this->options->deleteTransient(Options::TRANSIENT_DEACTIVATED_OTHER_INSTANCES);

        echo sprintf(
            '<div class="notice notice-warning">' .
            '<p>%s</p>' .
            '</div>',
            $message
        );
    }

    public function amendProFieldLabelAndInstruction(array $field): array
    {
        $isProField = !$this->isProVersion() &&
            key_exists('a-pro', $field);
        $isAcfProField = !$this->isAcfPluginAvailable(true) &&
            key_exists('a-acf-pro', $field);

        if (!$isProField &&
            !$isAcfProField) {
            return $field;
        }

        $type = $field['type'] ?? '';
        $field['label'] = $field['label'] ?? '';
        $field['instructions'] = $field['instructions'] ?? '';

        if ($isProField) {
            if ('tab' === $type) {
                $field['label'] = $field['label'] . ' (Pro)';
            } else {
                $field['instructions'] = sprintf(
                    '<a href="%s" target="_blank">%s</a> %s %s',
                    Plugin::PRO_VERSION_URL,
                    __('Upgrade to Pro', 'acf-views'),
                    __('to unlock.', 'acf-views'),
                    $field['instructions']
                );
            }
        }

        if ($isAcfProField) {
            $field['instructions'] = sprintf(
                '(<a href="%s" target="_blank">%s</a> %s) %s',
                'https://www.advancedcustomfields.com/pro/',
                __('ACF Pro', 'acf-views'),
                __('version is required for this feature', 'acf-views'),
                $field['instructions']
            );
        }

        return $field;
    }

    public function addClassToAdminProFieldClasses(array $wrapper, array $field): array
    {
        $isProField = !$this->isProVersion() &&
            key_exists('a-pro', $field);
        $isAcfProField = !$this->isAcfPluginAvailable(true) &&
            key_exists('a-acf-pro', $field);

        if (!$isProField &&
            !$isAcfProField) {
            return $wrapper;
        }

        if (!key_exists('class', $wrapper)) {
            $wrapper['class'] = '';
        }

        $wrapper['class'] .= ' acf-views-pro';

        return $wrapper;
    }

    public function getAdminUrl(
        string $page = '',
        string $cptName = ViewsCpt::NAME,
        string $base = 'edit.php'
    ): string {
        $pageArg = $page ?
            '&page=' . $page :
            '';

        // don't use just '/wp-admin/x' as some websites can have custom admin url, like 'wp.org/wordpress/wp-admin'
        $pageUrl = get_admin_url(null, $base . '?post_type=');

        return $pageUrl . $cptName . $pageArg;
    }

    public function printSurveyLink(string $html): string
    {
        if (!$this->isCPTScreen(ViewsCpt::NAME, ['post', 'add', 'edit',]) &&
            !$this->isCPTScreen(CardsCpt::NAME, ['post', 'add', 'edit',])) {
            return $html;
        }

        $content = sprintf(
            '%s <a target="_blank" href="%s">%s</a> %s <a target="_blank" href="%s">%s</a>.',
            __('Thank you for creating with', 'acf-views'),
            'https://wordpress.org/',
            __('WordPress', 'acf-views'),
            __('and', 'acf-views'),
            self::BASIC_VERSION_URL,
            __('ACF Views', 'acf-views')
        );
        $content .= " " . sprintf(
                "<span>%s <a target='_blank' href='%s'>%s</a> %s</span>",
                __('Take', 'acf-views'),
                self::SURVEY_URL,
                __('2 minute survey', 'acf-views'),
                __('to improve the ACF Views plugin.', 'acf-views')
            );

        return sprintf(
            '<span id="footer-thankyou">%s</span>',
            $content
        );
    }

    public function setHooks(): void
    {
        add_action('admin_notices', [$this, 'showWarningAboutInactiveAcfPlugin']);
        add_action('admin_notices', [$this, 'showWarningAboutOpcacheIssue']);

        add_action('activated_plugin', [$this, 'deactivateOtherInstances']);
        add_action('pre_current_active_plugins', [$this, 'showPluginDeactivatedNotice']);

        add_filter('acf/prepare_field', [$this, 'amendProFieldLabelAndInstruction']);
        add_filter('acf/field_wrapper_attributes', [$this, 'addClassToAdminProFieldClasses'], 10, 2);
        add_filter('admin_footer_text', [$this, 'printSurveyLink']);
    }
}
