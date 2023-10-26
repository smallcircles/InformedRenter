<?php

declare(strict_types=1);

namespace org\wplake\acf_views\Groups\Integration;

use org\wplake\acf_views\Groups\MetaFieldData;

defined('ABSPATH') || exit;

class MetaFieldDataIntegration extends AcfIntegration
{
    protected FieldDataIntegration $fieldIntegration;

    public function __construct(FieldDataIntegration $fieldIntegration)
    {
        $this->fieldIntegration = $fieldIntegration;
    }

    protected function setFieldChoices(): void
    {
        add_filter(
            'acf/load_field/name=' . MetaFieldData::getAcfFieldName(MetaFieldData::FIELD_GROUP),
            function (array $field) {
                $field['choices'] = $this->getGroupChoices(false);

                return $field;
            }
        );

        add_filter(
            'acf/load_field/name=' . MetaFieldData::getAcfFieldName(MetaFieldData::FIELD_FIELD_KEY),
            function (array $field) {
                $field['choices'] = $this->fieldIntegration->getFieldChoices(false);

                return $field;
            }
        );
    }
}