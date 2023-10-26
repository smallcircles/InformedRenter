<?php


$id = get_field('props_id') ?: false;
$title = get_field('props_title') ?: false;
$text = get_field('props_text') ?: false;
$link_text = get_field('props_link_text') ?: false;
$link = get_field('props_link') ?: false;
$image = get_field('props_image') ?: false;
$size = get_field('props_size') ?: false;
$theme = get_field('props_theme') ?: false;
$columns = get_field('props_columns') ?: false;
$items = get_field('props_items') ?: false;

$section_classes = "props  ";
$wrapper_style = "";
if($theme){ $section_classes .= $theme." "; }
if($columns){ $section_classes .= "cols_".$columns." "; }
if($image){ $wrapper_style .= "background-image:url(".$image['url'].";"; }

$block_header = false;
if($title || $text){ $block_header = true; }
?>


				
<section id="<?php echo $id; ?>" class="block <?php echo $section_classes; ?>">
	<div class="wrapper" style="<?php echo $wrapper_style; ?>">
		<div class="content">
		<?php
			if($block_header){?>
			<div class="header"><?php
				if($title){?><h2><?php echo $title; ?></h2><?php  }
				if($text){?><p><?php echo $text; ?></p><?php } ?>
			</div>
		<?php }
			
			if(have_rows('props_items')) {
			while( have_rows('props_items') ) : the_row();
				$item_title = get_sub_field('props_item_title') ?: false;
				$item_text = get_sub_field('props_item_text') ?: false;
				$item_link = get_sub_field('props_item_link') ?: false;
				$item_link_text = get_sub_field('props_item_link_text') ?: false; ?>
			<div class="props-item">
			<?php 
				if($item_title){?><h4><?php echo $item_title; ?></h4><?php  }
				if($item_text){?><p><?php echo $item_text; ?></p><?php		}
				if($item_link){?><p class="props-link"><a href="<?php echo $item_link; ?>"><?php echo $item_link_text
				?></a></p><?php } ?>
			</div>
			<?php
					endwhile;
				}
			?>
		</div>
	</div>
	<!-- 
	<div id="testvars" style="display:none;">
			<h2>VALUE PROPS BLOCK</h2>
			$id = <?php echo $id; ?><br />
			$columns = <?php echo $columns; ?><br />
			$title = <?php echo $title; ?><br />
			$text = <?php echo $text; ?><br />
			$theme = <?php echo $theme; ?><br />
			$image['url'] = <?php echo $image['url']; ?><br />
			$image['title'] = <?php echo $image['title']; ?><br />
			$image['caption'] = <?php echo $image['caption']; ?><br />
			$image['description'] = <?php echo $image['description']; ?><br />
			$image['alt'] = <?php echo $image['alt']; ?><br />
			<hr>
		<?php
		print_r($items);
		?>
	</div>
	-->
</section>

