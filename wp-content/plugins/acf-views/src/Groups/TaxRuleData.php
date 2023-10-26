<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Groups;

use org\wplake\acf_views\Group;

defined('ABSPATH') || exit;

class TaxRuleData extends Group
{
    // to fix the group name in case class name changes
    const CUSTOM_GROUP_NAME = self::GROUP_NAME_PREFIX . 'tax-rule';

    /**
     * @a-type select
     * @ui 1
     * @required 1
     * @label Relation
     * @instructions Controls how the taxonomies will be joined within the taxonomy rule
     * @choices {"AND":"AND","OR":"OR"}
     * @default_value AND
     * @conditional_logic [[{"field": "local_acf_views_tax-rule__taxonomies","operator": ">","value": "1"}]]
     */
    public string $relation;
    /**
     * @var TaxFieldData[]
     * @item \org\wplake\acf_views\Groups\TaxFieldData
     * @button_label Add Taxonomy
     * @label Taxonomies
     * @instructions Taxonomies for the taxonomy rule. Multiple taxonomies are supported
     * @a-no-tab 1
     * @required 1
     */
    public array $taxonomies;
}