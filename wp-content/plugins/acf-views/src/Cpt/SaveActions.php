<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Cpt;

use org\wplake\acf_views\Cache;
use org\wplake\acf_views\CptData;
use org\wplake\acf_views\Group;
use org\wplake\acf_views\Plugin;
use org\wplake\acf_views\Views\Cpt\ViewsCpt;

defined('ABSPATH') || exit;

abstract class SaveActions
{
    protected Cache $cache;
    protected Plugin $plugin;
    protected array $fieldValues;
    /**
     * @var CptData
     */
    protected $validationInstance;
    protected array $availableAcfFields;
    protected array $validatedInputNames;

    public function __construct(Cache $cache, Plugin $plugin, CptData $cptData)
    {
        $this->cache = $cache;
        $this->plugin = $plugin;
        $this->validationInstance = $cptData->getDeepClone();
        $this->availableAcfFields = array_keys($this->validationInstance->getFieldValues());
        $this->fieldValues = [];
        $this->validatedInputNames = [];
    }

    abstract protected function getCptName(): string;

    abstract public function performSaveActions($postId): void;

    protected abstract function getTranslatableLabels($cptData): array;

    protected function getInstanceData(int $postId): CptData
    {
        return $this->getCptName() === ViewsCpt::NAME ?
            $this->cache->getAcfViewData($postId) :
            $this->cache->getAcfCardData($postId);
    }

    protected function getTranslationsFromMarkup(array $translations, string $markup): array
    {
        $textDomains = [];

        // __("Some data") or __("Some data", "my-theme")
        preg_match_all(
            '/__\([ ]*["]([^"]+)["]([, ]+["]([^"]+)["])*[ ]*\)/',
            $markup,
            $functionsWithDoubleQuotes,
            PREG_SET_ORDER
        );

        // __('Some data') or __('Some data', 'my-theme')
        preg_match_all(
            "/__\([ ]*[']([^']+)[']([, ]+[']([^']+)['])*[ ]*\)/",
            $markup,
            $functionsWithSingleQuotes,
            PREG_SET_ORDER
        );

        // "Some data"|translate or "Some data"|translate("my-theme")
        preg_match_all(
            '/["]([^"]+)["]\|translate(\([ ]*["]([^"]+)["][ ]*\))*/',
            $markup,
            $filtersWithDoubleQuotes,
            PREG_SET_ORDER
        );

        // 'Some data'|translate or 'Some data'|translate('my-theme')
        preg_match_all(
            "/[']([^']+)[']\|translate(\([ ]*[']([^']+)['][ ]*\))*/",
            $markup,
            $filtersWithSingleQuotes,
            PREG_SET_ORDER
        );

        $functions = array_merge($functionsWithDoubleQuotes, $functionsWithSingleQuotes);
        $filters = array_merge($filtersWithDoubleQuotes, $filtersWithSingleQuotes);
        $matches = array_merge($functions, $filters);

        foreach ($matches as $match) {
            $label = $match[1] ?? '';
            $textDomain = $match[3] ?? $this->plugin->getThemeTextDomain();

            $translations[$textDomain] = $translations[$textDomain] ?? [];
            $translations[$textDomain][] = $label;

            $textDomains[] = $textDomain;
        }

        $textDomains = array_unique($textDomains);
        foreach ($textDomains as $textDomain) {
            $translations[$textDomain] = array_unique($translations[$textDomain]);
        }

        return $translations;
    }

    protected function addValidationError(string $fieldKey, string $message)
    {
        $inputName = $this->validatedInputNames[$fieldKey] ?? '';
        acf_add_validation_error($inputName, $message);
    }

    protected function validateSubmission()
    {
    }

    public function saveMetaField($value, array $field): void
    {
        $fieldName = $field['name'] ?? '';
        $validationInstance = $this->validationInstance;

        // convert repeater format. don't check simply 'is_array(value)' as not every array is a repeater
        // also check to make sure it's array (can be empty string)
        if (in_array($fieldName, $validationInstance->getRepeaterFieldNames(), true) &&
            is_array($value)) {
            $value = Group::convertRepeaterFieldValues($fieldName, $value);
        }

        // convert clone format
        // also check to make sure it's array (can be empty string)
        if (in_array($fieldName, $validationInstance->getCloneFieldNames(), true) &&
            is_array($value)) {
            $newValue = Group::convertCloneField($fieldName, $value);
            $this->fieldValues = array_merge($this->fieldValues, $newValue);

            return;
        }

        $this->fieldValues[$fieldName] = $value;
    }

    public function getAcfFieldFromInstance($value, int $postId, array $field, array $values)
    {
        $fieldName = $field['name'] ?? '';

        // skip sub-fields or fields from other groups
        if (!key_exists($fieldName, $values)) {
            return $value;
        }

        $value = $values[$fieldName];
        $instanceData = $this->getInstanceData($postId);

        // convert repeater format. don't check simply 'is_array(value)' as not every array is a repeater
        // also check to make sure it's array (can be empty string)
        $value = in_array($fieldName, $instanceData->getRepeaterFieldNames(), true) &&
        is_array($value) ?
            Group::convertRepeaterFieldValues($fieldName, $value, false) :
            $value;

        // convert clone format
        $cloneFieldNames = $instanceData->getCloneFieldNames();
        foreach ($cloneFieldNames as $cloneFieldName) {
            $clonePrefix = $cloneFieldName . '_';

            if (0 !== strpos($fieldName, $clonePrefix)) {
                continue;
            }

            // can be string field
            if (!is_array($value)) {
                break;
            }

            $fieldNameWithoutClonePrefix = substr($fieldName, strlen($clonePrefix));

            $value = Group::convertCloneField($fieldNameWithoutClonePrefix, $value, false);

            break;
        }

        return $value;
    }

    public function updateTranslationsFile(CptData $cptData): void
    {
        $folderInTheme = get_stylesheet_directory() . '/acf-views-labels';

        if (!is_dir($folderInTheme)) {
            return;
        }

        $translations = $this->getTranslatableLabels($cptData);

        if (!$translations) {
            return;
        }

        $translationFile = sprintf('%s/%s.php', $folderInTheme, $cptData->getUniqueId());
        $translationFileLines = [];

        foreach ($translations as $textDomain => $labels) {
            foreach ($labels as $label) {
                // to avoid breaking the PHP string
                $label = str_replace("'", "&#039;", $label);
                $label = str_replace('"', "&quot;", $label);

                $translationFileLines[] = sprintf("__('%s', '%s');", $label, $textDomain);
            }
        }

        $fileContent = "<?php\n" .
            "// " . get_the_title($cptData->getSource()) .
            "\n\n" .
            join("\n", $translationFileLines);

        // always overwrite the file
        file_put_contents($translationFile, $fileContent);
    }

    public function maybeSetUniqueId(CptData $acfCptData, string $prefix): bool
    {
        // do not check just for empty, because WP autofills the slug
        if (0 === strpos($acfCptData->getUniqueId(), $prefix)) {
            return false;
        }

        $uniqueId = uniqid($prefix);

        wp_update_post([
            'ID' => $acfCptData->getSource(),
            'post_name' => $uniqueId,
        ]);

        return true;
    }

    /**
     * @param int|string $postId Can be string, e.g. 'options'
     * @param array|null $targetStatuses
     *
     * @return bool
     */
    public function isMyPost($postId, ?array $targetStatuses = ['publish',]): bool
    {
        // for 'site-settings' and similar
        if (!is_numeric($postId) ||
            !$postId) {
            return false;
        }

        $post = get_post($postId);

        if (!$post ||
            $this->getCptName() !== $post->post_type ||
            wp_is_post_revision($postId) ||
            ($targetStatuses && !in_array($post->post_status, $targetStatuses, true))) {
            return false;
        }

        return true;
    }

    /**
     * @param bool|string $valid
     * @param mixed $value
     * @param array $field
     * @param string $inputName
     * @return bool|string
     */
    public function catchFieldValue($valid, $value, array $field, string $inputName)
    {
        if (true !== $valid ||
            !in_array($field['key'], $this->availableAcfFields, true)) {
            return $valid;
        }

        $this->validatedInputNames[$field['key']] = $inputName;

        $this->saveMetaField($value, $field);

        return true;
    }

    public function customValidation()
    {
        if (acf_get_validation_errors()) {
            return;
        }

        // remove slashes added by WP, as it's wrong to have slashes so early
        // (corrupts next data processing, like markup generation (will be \&quote; instead of &quote; due to this escaping)
        // in the 'saveToPostContent()' method using $wpdb that also has 'addslashes()',
        // it means otherwise \" will be replaced with \\\" and it'll create double slashing issue (every saving amount of slashes before " will be increasing)

        $fieldValues = array_map('stripslashes_deep', $this->fieldValues);

        $this->validationInstance->load(0, '', $fieldValues);

        $this->validateSubmission();
    }

    public function skipSavingToPostMeta($postId)
    {
        if (!$this->isMyPost($postId, null)) {
            return;
        }

        add_filter('acf/pre_update_value', function ($isUpdated, $value, int $postId, array $field): bool {
            // extra check, as probably it's about another post
            if (!$this->isMyPost($postId, null)) {
                return $isUpdated;
            }

            // avoid saving to the postmeta
            return true;
        }, 10, 4);
    }

    public function saveCaughtFields($postId)
    {
        if (!$this->isMyPost($postId, null)) {
            return;
        }

        $this->validationInstance->setSource($postId);
        $this->validationInstance->saveToPostContent();
    }

    public function loadFieldsFromPostContent()
    {
        global $post;
        $postId = $post->ID ?? 0;

        if (!$this->isMyPost($postId, null)) {
            return;
        }

        // values are cache here, to avoid call instanceData->getFieldValues() every time
        // as it takes resources (to go through all inner objects)
        $values = [];

        add_filter('acf/pre_load_value', function ($value, $postId, $field) use ($values) {
            // extra check, as probably it's about another post
            if (!$this->isMyPost($postId, null)) {
                return $value;
            }

            if (!key_exists($postId, $values)) {
                $instanceData = $this->getInstanceData($postId);

                $values[$postId] = $instanceData->getFieldValues();
            }

            return $this->getAcfFieldFromInstance($value, $postId, $field, $values[$postId]);
        }, 10, 3);
    }

    // by tests, json in post_meta in 13 times quicker than ordinary postMeta way (30ms per 10 objects vs 400ms)
    public function setHooks(): void
    {
        // priority is 20, to make sure it's run after the ACF's code
        add_action('acf/validate_value', [$this, 'catchFieldValue'], 20, 4);
        add_action('acf/validate_save_post', [$this, 'customValidation'], 20, 4);

        add_action('acf/save_post', [$this, 'skipSavingToPostMeta']);
        add_action('acf/save_post', [$this, 'saveCaughtFields'], 20);
        add_action('acf/save_post', [$this, 'performSaveActions'], 30);

        add_action('acf/input/admin_head', [$this, 'loadFieldsFromPostContent']);
    }
}