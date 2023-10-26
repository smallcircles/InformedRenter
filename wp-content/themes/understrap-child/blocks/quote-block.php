 <?php
$image = get_field('quote-image');
$text = get_field('quote-text');
$author = get_field('quote-author');
$title = get_field('quote-author-title');

?>

<section class="quote-block">
  <div class="content-container">
    <div class="quote-block-1-content">
      <div class="quote-block-1-image-container">
        <div class="image-wrapper">
          <img class="quote-block-1-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
        </div>
      </div>
      <div class="quote-block-1-info-container">
        <p class="quote-block-1-text"><?php echo $text; ?></p>
        <h5 class="quote-block-1-author"><?php echo $author; ?></h5>
        <h5 class="quote-block-1-author-title"><?php echo $title; ?></p>
      </div>
    </div>
  </div>
</section>