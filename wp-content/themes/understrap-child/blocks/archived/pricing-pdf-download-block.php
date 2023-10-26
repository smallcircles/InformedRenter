<?php
$text = get_field('pricing-plan-pdf-download-text');
$file_url = get_field ('pricing-plan-pdf-download-file');
$link_text = get_field('pricing-plan-pdf-download-link-text');
?>

<section class="pricing-plan-pdf-download">
  <div class="pricing-plan-pdf-download-container">
    <div class="pricing-plan-pdf-body">
      <p class="pricing-plan-pdf-download-text"><?php echo $text; ?></p>
      <a href="<?php echo $file_url; ?>" class="btn btn-secondary pricing-plan-pdf-download-link">
        <?php echo $link_text; ?>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12.8301 13.67H2.33008V3.17004H5.50008V1.67004H0.830078V15.17H14.3301V10.5H12.8301V13.67Z" fill="#13382E"/>
          <path d="M6.99997 0.25V1.75H13.19L6.46997 8.47L7.52997 9.53L14.25 2.81V9H15.75V0.25H6.99997Z" fill="#13382E"/>
        </svg>
      </a>
    </div>
  </div>
</section>