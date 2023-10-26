<?php

declare(strict_types=1);

namespace org\wplake\acf_views;

use org\wplake\acf_views\Views\Cpt\ViewsCpt;

defined('ABSPATH') || exit;

abstract class Group extends \org\wplake\acf_views\vendors\LightSource\AcfGroups\AcfGroup
{
    const GROUP_NAME_PREFIX = 'local_' . ViewsCpt::NAME . '_';

    // to keep back compatibility
    const FIELD_NAME_PREFIX = '';
    const TEXT_DOMAIN = 'acf-views';
}
