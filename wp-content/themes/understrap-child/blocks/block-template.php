<?php  // BLOCK NAME


$id = get_field('block_id') ?: false;
$title = get_field('block_title') ?: false;
$text = get_field('block_text') ?: false;
$link = get_field('block_link') ?: false;
$link_text = get_field('block_link_text') ?: false;
$link_type = get_field('block_link_type') ?: false;
$theme = get_field('block_theme') ?: false;
$image = get_field('block_image') ?: false;
$alignment = get_field('block_alignment') ?: false;


$block_classes = "block  ";
if($theme){ $block_classes .= " ".$theme; }

?>

<section id="<?php echo $id; ?>" class="<?php echo $block_classes; ?>">
	<div id="testvars"><!-- ERASE -->
	<h2>### BLOCK</h2>
	
		$id = <?php echo $id; ?><br />
		$alignment = <?php echo $alignment; ?><br />
		$title = <?php echo $title; ?><br />
		$text = <?php echo $text; ?><br />
		$link = <?php echo $link; ?><br />
		$link_text = <?php echo $link_text; ?><br />
		$link_type = <?php echo $link_type; ?><br />
		$theme = <?php echo $theme; ?><br />
		$image['url'] = <?php echo $image['url']; ?><br />
		$image['title'] = <?php echo $image['title']; ?><br />
		$image['caption'] = <?php echo $image['caption']; ?><br />
		$image['description'] = <?php echo $image['description']; ?><br />
		$image['alt'] = <?php echo $image['alt']; ?><br />
		
	</div>
	<div class="block_content">
	
	</div>
</div>