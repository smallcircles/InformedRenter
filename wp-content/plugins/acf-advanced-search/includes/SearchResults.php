<?php

namespace ACFAdvancedSearch;

class SearchResults
{

    public function __construct()
    {
        add_shortcode('displayACFfields', array($this, 'displayACFfields'));
    }

    /**
     * Display ACF fields in single Post
     */
    public function displayACFfields()
    {
        if (is_single()) {

            if ($fields = get_field_objects()) {
                ?>
                <div class='data row'>
                    <?php
                    foreach ($fields as $field) {?>
                            <div class='col-sm-6 col-md-4 col-lg-3'>
                                <div class='term'>
                                    <h3><?php echo $field['label']; ?></h3>
                                </div>
                                <div class='value'>
                                    <?php

                                    if (isset($field['choices'])) {
                                        if (is_array($field['value'])) {
                                            $array = array();
                                            foreach ($field['value'] as $value) {
                                                $array[] = $field['choices'][$value];
                                            }
                                            echo implode('<br/>', $array);
                                        } else {
                                            echo $field['choices'][$field['value']];
                                        }
                                    } else {
                                        echo $field['value'];
                                    }
                                    if (isset($field['append'])) echo ' ' . $field['append'];
                                    ?>
                                </div>
                            </div>
                            <?php
                    }
                    ?>

                </div>
                <?php
            }

        }

    }

}
