<?php 
$layout = get_field('split-text-image-layout');
$image = get_field('split-text-image-image');
$title = get_field('split-text-image-heading');
$copy = get_field('split-text-image-copy');
$cta = get_field('split-text-image-cta');
$background_class = get_field('split-text-image-background');
$is_left_layout = $layout === 'left';
$layout_class = $is_left_layout ? '' : 'right-layout';
?>

<section class="split-text-image <?php echo $background_class; ?>">
  <div class="content-container <?php echo $layout_class; ?>">
    <div class="text-copy">
      <div class="copy-wrapper" >
        <h2 class="heading"><?php echo $title; ?></h2>
        <p class="copy"><?php echo $copy; ?></p>
        <?php if ($cta) { ?>
          <a class="cta-button dark" href="<?php echo $cta['url']; ?>" target="<?php echo $cta['target'] ? $cta['target'] : '_self'; ?>">
            <?php echo $cta['title']; ?>
          </a>
        <?php } ?>
      </div>
    </div>
    <div class="image">
      <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
    </div>
  </div>
</section>