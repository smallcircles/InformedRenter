<?php
$title = get_field('cta_section_title');
$text = get_field('cta_section_text');
$cta1 = get_field('cta_section_cta_1');
$cta2 = get_field('cta_section_cta_2');
?>

<section class="cta-section">
  <div class="content-container">
  
    <?php if (!empty($title)) { ?>
      <h2 class="cta-heading"><?php echo $title; ?></h2>
    <?php } ?>

    <?php if (!empty($text)) { ?>
      <p>
        <?php echo $text; ?>
      </p>
    <?php } ?>

    <div class="cta-buttons">

      <?php if ($cta1) {
        $link_url = $cta1['url'];
        $link_title = $cta1['title'];
        $link_target = $cta1['target'] ? $cta1['target'] : '_self';
      ?>
        <a class="cta-button light" target="<?php echo $link_target; ?>" href="<?php echo $link_url; ?>"><?php echo $link_title; ?></a>
      <?php } ?>

      <?php if ($cta2) {
        $link_url2 = $cta2['url'];
        $link_title2 = $cta2['title'];
        $link_target2 = $cta2['target'] ? $cta2['target'] : '_self';
      ?>
        <a class="cta-button dark" target="<?php echo $link_target2; ?>" href="<?php echo $link_url2; ?>"><?php echo $link_title2; ?></a>
      <?php } ?>
      
    </div>
  </div>
</section>