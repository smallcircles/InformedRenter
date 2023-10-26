<?php

$view = $view ?? [];
$name = $view['name'] ?? '';
$tabs = $view['tabs'] ?? [];

?>
<div class="av-toolbar">
    <h2 class="av-toolbar__title">
        <i class="av-toolbar__icon dashicons dashicons-welcome-widgets-menus"></i>
        <?php
        echo esc_html($name) ?>
    </h2>
    <?php
    for ($i = 0; $i < 2; $i++) {
        $class = 0 === $i ?
            'left' :
            'right';
        printf('<div class="av-toolbar__block av-toolbar__block--type--%s">', $class);
        foreach ($tabs as $tab) {
            if ((0 === $i && !isset($tab['isLeftBlock'])) ||
                (1 === $i && !isset($tab['isRightBlock']))
            ) {
                continue;
            }

            $class = $tab['isActive'] ?
                ' av-toolbar__tab--active' :
                '';
            $class .= $tab['isSecondary'] ?
                ' av-toolbar__tab--secondary' :
                '';

            printf(
                '<a class="av-toolbar__tab%s" href="%s" target="%s"><span>%s</span>%s</a>',
                esc_html($class),
                esc_url($tab['url']),
                isset($tab['isBlank']) ? '_blank' : '_self',
                esc_html($tab['label']),
                $tab['icon'] ?? ''
            );
        }
        echo '</div>';
    }

    ?>

</div>
