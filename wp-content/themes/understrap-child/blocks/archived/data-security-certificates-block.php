<?php
$top_bg = get_field('data-certs-top-background-color');
$bottom_bg = get_field('data-certs-bottom-background-color');

if (!function_exists('determine_data_security_cert_bg_color')) {
  function determine_data_security_cert_bg_color($bg_select) {
    $bg_color = '#F3F5F5';

    switch($bg_select) {
      case 'Pine':
        $bg_color = '#13382e';
          break;
      case 'Spruce': 
        $bg_color = '#276e6b';
          break;
      case 'Peppermint':
        $bg_color = '#a8efca';
          break;
      case 'Grey':
        $bg_color = '#F3F5F5';
          break;
      case 'White':
        $bg_color = '#fff';
          break;
    }

    return $bg_color;
  }
}

$top_bg_color = determine_data_security_cert_bg_color($top_bg);
$bottom_bg_color = determine_data_security_cert_bg_color($bottom_bg);

?>
<section class="data-security-certificates-block"
  <?php 
    echo 'style="background: linear-gradient(' 
    . 'to top, '
    . $bottom_bg_color . ' 0%, '
    . $bottom_bg_color . ' 70%, '
    . $top_bg_color . ' 70%, ' 
    . $top_bg_color . ' 100% '
    . ');"';
    ?>
>
  <div class="data-security-certificates-container">
    <div class="data-security-certificates-body">
<?php 
      if (have_rows('data-certificates')) {
        while(have_rows('data-certificates')) {
          the_row();
          $image = get_sub_field('cert-icon');
          $name = get_sub_field('cert-name');
          $copy = get_sub_field('cert-copy');
?>
          <div class="data-certificate">
            <img class="cert-logo" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
            <p class="cert-name"><?php echo $name; ?></p>
            <p class="cert-copy"><?php echo $copy; ?></p>
          </div>
<?php
        }
      }
?>
    </div>
  </div>
</section>