<?php

$hb_photo = get_field('hb-photo') ?: null;
$hb_heading = get_field('hb-heading') ?: 'Hero heading';
$hb_description = get_field('hb-description') ?: 'lorem ipsum dolor sit amet, consectetur....';

$hb_hero_cta_button_1 = get_field('hb-hero-cta-button-1') ?: null;
$hb_hero_cta_button_2 = get_field('hb-hero-cta-button-2') ?: null;

$cta_link_1 = $hb_hero_cta_button_1['url'];
$cta_target_1 = $hb_hero_cta_button_1['target'] ? $hb_hero_cta_button_1['target'] : '_self';
$cta_title_1 = $hb_hero_cta_button_1['title'];

$cta_link_2 = $hb_hero_cta_button_2['url'];
$cta_target_2 = $hb_hero_cta_button_2['target'] ? $hb_hero_cta_button_1['target'] : '_self';
$cta_title_2 = $hb_hero_cta_button_2['title'];

$background_no_nav = get_field('hero-background-color-nonav');
$text_color = "";
$bg_color = "";
$button_class = "";
$nav_style_class = "";
$icon_class = "";
$nav_title_class = "";

switch($background_no_nav) {
    case 'pine':
        $text_color = '#FFF';
        $bg_color = '#13382e';
        $nav_style_class = 'pine-20';
        $button_class = "pine";
        break;
    case 'spruce': 
        $text_color = "#FFF";
        $bg_color = '#276e6b';
        $nav_style_class = "spruce-dark";
        $nav_title_class = "pine-20";
        break;
    case 'peppermint':
        $text_color = '#13382e';
        $bg_color = '#a8efca';
        $button_class = 'peppermint';
        $nav_style_class = "peppermint";
        $icon_class = 'peppermint';
        break;
    case 'grey':
        $text_color = '#13382E';
        $bg_color = '#F3F5F5';
        $button_class = 'grey';
        $nav_style_class = 'pine-20';
}

$has_nav = get_field('hero-with-nav') ?: false;
$nav_title = get_field('hero-nav-title') ?: null;
$nav_icon = get_field('hero-nav-icon') ?: null;
?>

<section id="hero-block" style="background-color: <?php echo $bg_color; ?>;">
    <div class="hero-block-container">
    <?php  if ($has_nav) { ?>
            <nav class="hero-block-nav <?php echo $nav_style_class; ?>">
                <div class="title-wrapper">
                    <div class="icon-wrapper <?php echo $icon_class; ?>"> 
                        <?php echo $nav_icon; ?>
                    </div>
                    <a class="hero-nav-title <?php echo $nav_title_class; ?>" href="#" style="color: <?php echo $text_color ?>;"><?php echo $nav_title; ?></a>
                </div>
                <ul class="hero-nav-items">
    <?php
             if (have_rows('hero-nav-fields')) {
                global $post;
                $current_slug = $post->post_name;

                while(have_rows('hero-nav-fields')) {
                    the_row();
                    $nav_link = get_sub_field('hero-nav-link');
                    $nav_href = $nav_link['url'];
                    $nav_target = $nav_link['target'] ? $nav_link['target'] : '_self';
                    $nav_title = $nav_link['title'];
                    $is_active = get_sub_field('hero-nav-link-is-active');
                    if ($is_active === "Yes") {
                        $is_active = true;
                    } else if ($is_active === "No") {
                        $is_active = false;
                    }

    ?>
                    <li><a href="<?php echo $nav_href; ?>" class="nav-item <?php if ($is_active) { echo 'active'; } else { echo ''; } ?>" target="<?php echo $nav_target; ?>"><?php echo $nav_title; ?></a></li>
    <?php
                }
             }
    ?>
                </ul>
            </nav>
    <?php    
         }
    ?>
        <div class="hero-content-grid<?php echo !empty($hb_photo) ? '' : ' no-image' ?>">
            <div class="hero-content-image">
                <?php if (!empty($hb_photo)) { ?>
                    <img id="hero-content-image" src="<?php echo esc_url($hb_photo['url']); ?>" alt="<?php echo esc_attr($hb_photo['alt']); ?>" />
                <?php } ?>
            </div>
            <div class="hero-content-info">
                <h1 class="hero-content-header" style="color: <?php echo $text_color; ?>;"><?php echo $hb_heading; ?></h1>
                <?php if (!empty($hb_description)) { ?>
                    <p class="hero-content-description" style="color: <?php echo $text_color; ?>;"><?php echo $hb_description; ?></p>
                <?php } ?>
                <?php if (!empty($hb_hero_cta_button_1) || !empty($hb_hero_cta_button_2) ) { ?>
                <div class="hero-content-cta-grid">
                    <?php if (!empty($hb_hero_cta_button_1)) { ?>
                        <a class="btn btn-secondary <?php echo $button_class; ?>" id="hero-content-cta-button1" target="<?php echo $cta_target_1; ?>" href="<?php echo $cta_link_1; ?>"><?php echo $cta_title_1; ?></a>
                    <?php } if (!empty($hb_hero_cta_button_2)) { ?>
                        <a class="btn btn-secondary <?php echo $button_class; ?>" id="hero-content-cta-button2" target="<?php echo $cta_target_2; ?>" href="<?php echo $cta_link_2; ?>"><?php echo $cta_title_2; ?></a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>