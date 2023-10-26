<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use Exception;
use org\wplake\acf_views\vendors\Twig\Environment;
use org\wplake\acf_views\vendors\Twig\Loader\FilesystemLoader;

defined('ABSPATH') || exit;

class Twig
{
    protected FilesystemLoader $loader;
    protected Environment $twig;
    protected Settings $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->loader = new FilesystemLoader(__DIR__ . '/templates');
        $this->twig = new Environment($this->loader, [
            // will generate exception if a var doesn't exist instead of replace to NULL
            'strict_variables' => true,
            // 'html' by default, just highlight that it's secure to not escape TWIG variable values in PHP
            'autoescape' => 'html',
        ]);
    }

    protected function getErrorMessage(int $id, string $errorMessage): string
    {
        return sprintf(
            '<p style="color:red;" class="acf-views__error">ACF Views (id=%s): <span class="acf-views__error-message">%s</span></p>',
            esc_html($id),
            esc_html($errorMessage)
        );
    }


    public function render(int $viewId, string $template, array $args): string
    {
        $html = '';

        // emulate the template file for every View.
        // as Twig generates a PHP class for every template file
        // so if you use the same, it'll have HTML of the very first View

        $templateFile = __DIR__ . '/templates/' . $viewId;

        $isWritten = false !== file_put_contents($templateFile, $template);

        if (!$isWritten) {
            $html .= $this->getErrorMessage($viewId, 'Can\'t write template file');

            return $html;
        }

        try {
            $html = $this->twig->render($viewId, $args);
        } catch (Exception $e) {
            $isAdmin = in_array('administrator', wp_get_current_user()->roles, true);
            $debugMode = $isAdmin && $this->settings->isDevMode();

            $html .= $this->getErrorMessage($viewId, $e->getMessage());

            if ($debugMode) {
                $html .= '<pre>' . print_r($args, true) . '</pre>';
            }
        }

        unlink($templateFile);

        return $html;
    }
}
