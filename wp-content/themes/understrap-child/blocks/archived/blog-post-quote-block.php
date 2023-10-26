<?php 
$quote = get_field('quote-text');
$quote_credit = get_field('quote-credit');
?>

<div class="features-details-quote-section">
  <blockquote class="features-details-quote">"<?php echo $quote; ?>"</blockquote>
  <p><?php echo $quote_credit; ?></p>
</div>