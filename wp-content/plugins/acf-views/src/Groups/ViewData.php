<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Groups;

use org\wplake\acf_views\CptData;
use org\wplake\acf_views\vendors\LightSource\AcfGroups\Interfaces\CreatorInterface;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;
use org\wplake\acf_views\Views\FieldMeta;

defined('ABSPATH') || exit;

class ViewData extends CptData
{
    // to fix the group name in case class name changes
    const CUSTOM_GROUP_NAME = self::GROUP_NAME_PREFIX . 'view';
    const LOCATION_RULES = [
        [
            'post_type == ' . ViewsCpt::NAME,
        ],
    ];
    const FIELD_MARKUP = 'markup';
    const FIELD_CSS_CODE = 'cssCode';
    const FIELD_JS_CODE = 'jsCode';
    const FIELD_CUSTOM_MARKUP = 'customMarkup';
    const FIELD_PHP_VARIABLES = 'phpVariables';
    const POST_FIELD_IS_HAS_GUTENBERG = 'post_mime_type';
    // keep the WP format 'image/jpg' to use WP_Query without issues
    const POST_VALUE_IS_HAS_GUTENBERG = 'block/block';

    /**
     * @a-type tab
     * @label Fields
     */
    public bool $fields;
    /**
     * @item \org\wplake\acf_views\Groups\ItemData
     * @var ItemData[]
     * @label Fields
     * @instructions Assign Advanced Custom Fields (ACF) to your View. <br> Click on the empty space to expand the row, click on the empty space near the tabs to collapse again. <br> Tip : hover mouse on the field number column and drag to reorder
     * @button_label Add Field
     * @collapsed local_acf_views_field__key
     * @a-no-tab 1
     */
    public array $items;

    /**
     * @a-type tab
     * @label Basic
     */
    public bool $general;
    /**
     * @a-type textarea
     * @label Description
     * @instructions Add a short description for your views’ purpose. Note : This description is only seen on the admin ACF Views list
     */
    public string $description;
    /**
     * @label BEM Unique Name
     * @instructions Define a unique <a target='_blank' href='https://getbem.com/introduction/'>BEM name</a> for the element that will be used in the markup, or leave it empty to use the default ('acf-view').
     */
    public string $bemName;
    /**
     * @label CSS classes
     * @instructions Add a class name without a dot (e.g. “class-name”) or multiple classes with single space as a delimiter (e.g. “class-name1 class-name2”). These classes are added to the wrapping HTML element. <a target='_blank' href='https://www.w3schools.com/cssref/sel_class.asp'>Learn more about CSS Classes</a>
     */
    public string $cssClasses;
    /**
     * @label With Gutenberg Block
     * @instructions If checked, a separate gutenberg block for this view will be available. <a target='_blank' href='https://docs.acfviews.com/guides/acf-views/features/gutenberg-pro'>Read more</a>
     * @a-pro The field must be not required or have default value!
     * @a-acf-pro ACF PRO version is necessary for this feature
     */
    public bool $isHasGutenbergBlock;

    /**
     * @a-type tab
     * @label Markup
     */
    public bool $markupTab;
    /**
     * @a-type textarea
     * @new_lines br
     * @label Template Preview
     * @instructions Output preview of the generated <a target='_blank' href='https://docs.acfviews.com/guides/twig-templates'>Twig template</a>. Important! Publish or Update your view to see the latest markup.
     */
    public string $markup;
    /**
     * @a-type textarea
     * @label Custom Template
     * @instructions Write your own template with full control over the HTML markup. You can copy the Template Preview field output and make your changes. <br> Powerful <a target='_blank' href='https://docs.acfviews.com/guides/twig-templates'>Twig features</a>, including <a target='_blank' href='https://docs.acfviews.com/guides/twig-templates#our-functions'>our functions</a>, are available for you. <br> Press Ctrl (Cmd) + Alt + L to format the code. <br> Important! This field will not be updated automatically when you add or remove fields, so you have to update this field manually to reflect the new changes (you can refer to the Template Preview field for assistance). <a target='_blank' href='https://docs.acfviews.com/guides/acf-views/features/custom-markup-pro'>Read more</a>
     */
    public string $customMarkup;
    /**
     * @a-type textarea
     * @label Custom Template Variables
     * @instructions You can add custom variables to the template using this PHP code snippet. <br>The snippet must return an associative array of values, where keys are variable names. Names should be PHP compatible, which means only letters and underscores are allowed. <br> You can access these variables in the template just like others: '{{ your_variable }}'. <br> Press Ctrl (Cmd) + Alt + L to format the code. <br> In the snippet, the following variables are predefined: '&#36;_objectId' (current data post), '&#36;_viewId' (current view id),'&#36;_fields' (an associative field values array, where keys are field identifiers). <a target='_blank' href='https://docs.acfviews.com/guides/acf-views/features/custom-markup-variables-pro'>Read more</a>
     * @default_value <?php return [];
     * @a-pro The field must be not required or have default value!
     */
    public string $phpVariables;
    /**
     * @a-type true_false
     * @label Add classification classes to the markup
     * @instructions By default, the field name is added as a prefix to all inner classes. For example, the image within the 'avatar' field will have the '__avatar-image' class. <br> Enabling this setting adds the generic class as well, such as '__image'. This feature can be useful if you want to apply styles based on field types.
     */
    public bool $isWithCommonClasses;
    /**
     * @a-type true_false
     * @label Render template when it's empty
     * @instructions By default, if all the selected fields are empty, the Twig template won't be rendered. <br> Enable this option if you have specific logic inside the template and you want to render it even when all the fields are empty.
     */
    public bool $isRenderWhenEmpty;
    /**
     * @a-type true_false
     * @label Do not skip unused wrappers
     * @instructions By default, empty wrappers in the markup are skipped to optimize the output. For example, the '__row' wrapper will be skipped if there is no field label. <br> Enable this feature if you need all the wrappers in the output.
     */
    public bool $isWithUnnecessaryWrappers;
    /**
     * @a-type true_false
     * @label Use the Post ID as the View ID in the markup
     * @instructions Note: For backward compatibility purposes only. Enable this option if you have external CSS selectors that rely on outdated digital IDs
     */
    public bool $isMarkupWithDigitalId;

    /**
     * @a-type tab
     * @label Advanced
     */
    public bool $advancedTab;
    /**
     * @a-type textarea
     * @label CSS Code
     * @instructions Define your CSS style rules here or within your theme. Rules defined here will be added within &lt;style&gt;&lt;/style&gt; tags ONLY to pages that have this view. <br> Press Ctrl (Cmd) + Alt + L to format the code. <br> Magic shortcuts are available (and will use the BEM Unique Name if defined) : <br> '#view' will be replaced with '.bem-name' (or '.acf-view--id--X'). <br> '#view__' will be replaced with '.bem-name .bem-name__' (or '.acf-view--id--X .acf-view__'). It means you can use '#view__row' and it'll be replaced with '.bem-name .bem-name__row'. <br> '#__' will be replaced with '.bem-name__'
     */
    public string $cssCode;
    /**
     * @a-type textarea
     * @label JS Code
     * @instructions Add your own Javascript to your view. This will be added within &lt;script&gt;&lt;/script&gt; tags ONLY to pages that have this view and also will be wrapped into an anonymous function to avoid name conflicts. <br> Press Ctrl (Cmd) + Alt + L to format the code. <br> Don't use inline comments ('//') inside the code, otherwise it'll break the snippet.
     */
    public string $jsCode;

    /**
     * @a-type tab
     * @label Preview
     */
    public bool $previewTab;
    /**
     * @a-type post_object
     * @return_format 1
     * @allow_null 1
     * @label Preview Object
     * @instructions Select a data object (which field values will be used) and press the 'Update' button to see the markup in the preview
     */
    public int $previewPost;
    /**
     * @label Preview
     * @instructions Here you can see the preview of the view and play with CSS rules. <a target='_blank' href='https://docs.acfviews.com/guides/acf-views/features/preview'>Read more</a><br>Important! Press the 'Update' button after changes to see the latest markup here. <br>Your changes to the preview won't be applied to the view automatically, if you want to keep them copy amended CSS to the 'CSS Code' field and press the 'Update' button. <br> Note: styles from your front page are included in the preview (some differences may appear)
     * @placeholder Loading... Please wait a few seconds
     * @disabled 1
     */
    public string $preview;

    private array $fieldsMeta;

    public function __construct(CreatorInterface $creator)
    {
        parent::__construct($creator);

        $this->fieldsMeta = [];
    }

    public static function getGroupInfo(): array
    {
        return array_merge(parent::getGroupInfo(), [
            'title' => __('View settings', 'acf-views'),
        ]);
    }

    protected function getUsedItems(): array
    {
        $fieldGroups = [];

        foreach ($this->items as $item) {
            $fieldGroup = explode('|', $item->field->key)[0];

            // ignore 'magic' groups
            if (0 !== strpos($fieldGroup, '$')) {
                $fieldGroups[] = $fieldGroup;
            }

            foreach ($item->repeaterFields as $repeaterField) {
                $subFieldGroup = explode('|', $repeaterField->key)[0];

                // ignore 'magic' groups
                if (0 !== strpos($subFieldGroup, '$')) {
                    $fieldGroups[] = $subFieldGroup;
                }
            }
        }

        $fieldGroups = array_unique($fieldGroups);

        return $fieldGroups;
    }

    public function setFieldsMeta(array $fieldsMeta = []): void
    {
        if ($fieldsMeta) {
            $this->fieldsMeta = $fieldsMeta;

            return;
        }

        if ($this->fieldsMeta) {
            return;
        }

        foreach ($this->items as $item) {
            $fieldId = $item->field->getAcfFieldId();
            $this->fieldsMeta[$fieldId] = new FieldMeta($fieldId);
        }
    }

    /**
     * @return FieldMeta[]
     */
    public function getFieldsMeta(): array
    {
        return $this->fieldsMeta;
    }

    public function getCssCode(bool $isMinify = true, bool $isPreview = false): string
    {
        $cssCode = $this->cssCode;

        if ($isMinify) {
            // remove all CSS comments
            $cssCode = preg_replace('|\/\*(.?)+\*\/|', '', $cssCode);

            // 'minify' CSS
            $cssCode = str_replace(["\t", "\n", "\r"], '', $cssCode);

            $markupId = $this->getMarkupId();

            // do not use getBemName(), because it'll always return something
            $selector = $this->bemName ?: 'acf-view--id--' . $markupId;

            // magic shortcuts
            $cssCode = str_replace(
                '#view ',
                sprintf('.%s ', $selector),
                $cssCode
            );
            $cssCode = str_replace(
                '#view{',
                sprintf('.%s{', $selector),
                $cssCode
            );
            $cssCode = str_replace(
                '#view__',
                sprintf('.%s .%s__', $selector, $this->getBemName()),
                $cssCode
            );
            $cssCode = str_replace(
                '#__',
                sprintf('.%s__', $selector),
                $cssCode
            );

            $cssCode = trim($cssCode);
        } elseif ($isPreview) {
            $cssCode = str_replace('#view__', sprintf('#view .%s__', $this->getBemName()), $cssCode);
        }

        return $cssCode;
    }

    public function saveToPostContent(array $postFields = [], bool $isSkipDefaults = false): bool
    {
        $isHasGutenberg = $this->isHasGutenbergBlock ?
            static::POST_VALUE_IS_HAS_GUTENBERG :
            '';

        $postFields = array_merge($postFields, [
            static::POST_FIELD_IS_HAS_GUTENBERG => $isHasGutenberg,
        ]);

        return parent::saveToPostContent($postFields, $isSkipDefaults);
    }

    public function getBemName(): string
    {
        $bemName = trim($this->bemName);

        if (!$bemName) {
            return 'acf-view';
        }

        return preg_replace('/[^a-z0-9\-_]/', '', $bemName);
    }
}
