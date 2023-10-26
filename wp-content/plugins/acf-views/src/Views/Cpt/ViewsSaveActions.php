<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Views\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\Cpt\SaveActions;
use org\wplake\acf_views\Groups\ViewData;
use org\wplake\acf_views\Html;
use org\wplake\acf_views\Plugin;
use org\wplake\acf_views\Shortcodes;
use org\wplake\acf_views\Views\FieldMeta;
use org\wplake\acf_views\Views\ViewMarkup;

defined('ABSPATH') || exit;

class ViewsSaveActions extends SaveActions
{
    protected ViewMarkup $viewMarkup;
    protected ViewsMetaBoxes $viewsMetaBoxes;
    protected Html $html;
    /**
     * @var ViewData
     */
    protected $validationInstance;

    public function __construct(
        Cache $cache,
        Plugin $plugin,
        ViewMarkup $viewMarkup,
        ViewsMetaBoxes $viewsMetaBoxes,
        Html $html,
        ViewData $viewData
    ) {
        parent::__construct($cache, $plugin, $viewData);

        $this->viewMarkup = $viewMarkup;
        $this->viewsMetaBoxes = $viewsMetaBoxes;
        $this->html = $html;
    }

    protected function getCptName(): string
    {
        return ViewsCpt::NAME;
    }

    /**
     * @param $cptData
     * @return array
     */
    protected function getTranslatableLabels($cptData): array
    {
        $labels = [];

        foreach ($cptData->items as $item) {
            if ($item->field->label) {
                $labels[] = $item->field->label;
            }
            if ($item->field->linkLabel) {
                $labels[] = $item->field->linkLabel;
            }
        }

        return $labels ?
            [
                Plugin::getThemeTextDomain() => array_unique($labels),
            ] :
            [];
    }

    protected function updateMarkup(ViewData $acfViewData): void
    {
        // pageId 0, so without CSS, also skipCache and customMarkup
        $viewMarkup = $this->viewMarkup->getMarkup($acfViewData, 0, '', true, true);

        $acfViewData->markup = $viewMarkup;
    }

    protected function updateIdentifiers(ViewData $acfViewData): void
    {
        foreach ($acfViewData->items as $item) {
            $item->field->id = ($item->field->id &&
                !preg_match('/^[a-zA-Z0-9_\-]+$/', $item->field->id)) ?
                '' :
                $item->field->id;

            if ($item->field->id &&
                $item->field->id === $this->getUniqueFieldId($acfViewData, $item, $item->field->id)) {
                continue;
            }

            $fieldMeta = new FieldMeta($item->field->getAcfFieldId());
            if (!$fieldMeta->isFieldExist()) {
                continue;
            }

            // $Post$ fields have '_' prefix, remove it, otherwise looks bad in the markup
            $name = ltrim($fieldMeta->getName(), '_');
            // transform '_' to '-' to follow the BEM standard (underscore only as a delimiter)
            $name = str_replace('_', '-', $name);
            $item->field->id = $this->getUniqueFieldId($acfViewData, $item, $name);
        }
    }

    protected function validateSubmission()
    {
        // todo validate custom markup
    }

    // public for tests
    public function getUniqueFieldId(ViewData $acfViewData, $excludeObject, string $name): string
    {
        $isUnique = true;

        foreach ($acfViewData->items as $item) {
            if ($item === $excludeObject ||
                $item->field->id !== $name) {
                continue;
            }

            $isUnique = false;
            break;
        }

        return $isUnique ?
            $name :
            $this->getUniqueFieldId($acfViewData, $excludeObject, $name . '2');
    }

    /**
     * @param int|string $postId
     *
     * @return void
     */
    public function performSaveActions($postId): void
    {
        if (!$this->isMyPost($postId)) {
            return;
        }

        $acfViewData = $this->cache->getAcfViewData($postId);

        $this->updateIdentifiers($acfViewData);
        $this->updateMarkup($acfViewData);
        $this->updateTranslationsFile($acfViewData);
        $this->maybeSetUniqueId($acfViewData, 'view_');

        // it'll also update post fields, like 'comment_count'
        $acfViewData->saveToPostContent();
    }

    public function refreshAjax(): void
    {
        $viewId = (int)($_POST['_postId'] ?? 0);
        $isWithShortcode = isset($_POST['_withShortcode']);

        $postType = get_post($viewId)->post_type ?? '';

        if ($this->getCptName() !== $postType) {
            echo "Post id is wrong";
            exit;
        }

        $acfViewData = $this->cache->getAcfViewData($viewId);

        $response = '';

        // ignore customMarkup (we need the preview)
        $markup = $this->viewMarkup->getMarkup(
            $acfViewData,
            0,
            '',
            false,
            true
        );
        $response .= sprintf('<div class="markup">%s</div>', $markup);

        if ($isWithShortcode) {
            $shortcodes = $this->html->postboxShortcodes(
                $acfViewData->getUniqueId(true),
                false,
                Shortcodes::SHORTCODE_VIEWS,
                get_the_title($viewId),
                false
            );
            $response .= sprintf('<div class="shortcode">%s</div>', $shortcodes);
        }

        $response .= '<div class="elements">';
        $response .= sprintf(
            '<div data-selector="#acf-views_related_groups .inside">%s</div>',
            $this->viewsMetaBoxes->printRelatedAcfGroupsMetaBox(get_post($viewId), true)
        );
        $response .= sprintf(
            '<div data-selector="#acf-views_related_cards .inside">%s</div>',
            $this->viewsMetaBoxes->getRelatedAcfCardsMetaBox(get_post($viewId))
        );
        $response .= '</div>';

        echo $response;

        exit;
    }

    public function setHooks(): void
    {
        parent::setHooks();

        add_action('wp_ajax_acf_views__view_refresh', [$this, 'refreshAjax',]);
    }
}
