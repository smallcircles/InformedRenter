<?php  // BLOCK NAME


$id = get_field('review_map_id') ?: false;
$title = get_field('review_map_title') ?: false;
$text = get_field('review_map_text') ?: false;
$link = get_field('review_map_link') ?: false;
$link_text = get_field('review_map_link_text') ?: false;
$link_type = get_field('review_map_link_type') ?: false;
$theme = get_field('review_map_theme') ?: false;
$image = get_field('review_map_image') ?: false;
$map = get_field('reviews_map') ?: false;
$location = get_field('reviews_map') ?: false;


$review_map_classes = "block  ";
if($theme){ $review_map_classes .= " ".$theme; }

?>
<style type="text/css">
	/*
.acf-map {
    width: 100%;
    height: 400px;
    border: #ccc solid 1px;
    margin: 20px 0;
}

// Fixes potential theme css conflict.
.acf-map img {
   max-width: inherit !important;
}
*/
.entry-footer { display:none; }
</style>
<section id="<?php echo $id; ?>" class="<?php echo $review_map_classes; ?>">
	<div id="testvars"><!--  -->
	<h2>### MAP</h2>
	
		$id = <?php echo $id; ?><br />
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
		$map = <?php echo $map; ?><br />
		$map = <?php echo $map; ?><br />
		
	</div>
	<div class="block_content" style="margin-bottom: -10px;">
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d20839.787058922295!2d-123.0812461514533!3d49.238993858946095!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x548673f143a94fb3%3A0xbb9196ea9b81f38b!2sVancouver%2C%20BC!5e0!3m2!1sen!2sca!4v1698253472213!5m2!1sen!2sca"  style="border:0; WIDTH:100%; height:450px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		
	</div>
</div>
<!-- $map array: <?php print_r($map); ?> -->
		
		<?php

/*
// TODO your field name here
$mapInfo = get_field("reviews_map");

$zoom = $mapInfo['zoom'] ?? '16';
$lat = $mapInfo['lat'] ?? '';
$lng = $mapInfo['lng'] ?? '';

// zoom level - gets from every specific map (when admins zoom out and saves a page, the zoom is also saved)
printf(
    '<div class="my-map" style="" data-zoom="%s">',
    $zoom
);

printf(
    '<div class="my-map__marker" data-lat="%s" data-lng="%s"></div>',
    esc_attr($lat),
    esc_attr($lng)
);

echo "</div>";
*/
?>
	
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAqQgaRRPPEX57kTznmU5-p684uFMlOfqY&callback=Function.prototype"></script>
<script type="text/javascript">

class Map {
    constructor(element) {
        this.element = element
        this.map = null
        this.mapMarkers = []
    }

    readMarkers() {
        // TODO replace the selector if you've changed it in the markup
        this.element.querySelectorAll('.my-map__marker').forEach((markerElement) => {
            let lat = markerElement.dataset.hasOwnProperty('lat') ?
                markerElement.dataset['lat'] :
                0
            let lng = markerElement.dataset.hasOwnProperty('lng') ?
                markerElement.dataset['lng'] :
                0

            this.mapMarkers.push({
                lat: parseFloat(lat),
                lng: parseFloat(lng),
            })

            markerElement.remove()
        })
    }

    createMap() {
        let mapArgs = {
            zoom: parseInt(this.element.dataset.hasOwnProperty('zoom') ?
                this.element.dataset['zoom'] :
                16),
            mapTypeId: window.google.maps.MapTypeId.ROADMAP,
        }
        this.map = new window.google.maps.Map(this.element, mapArgs)
    }

    createMarkers() {
        this.mapMarkers.forEach((marker) => {
            new window.google.maps.Marker({
                position: marker,
                map: this.map,
            })
        })
    }

    centerMap() {
        // Create map boundaries from all map markers.
        let bounds = new window.google.maps.LatLngBounds()

        this.mapMarkers.forEach((marker) => {
            bounds.extend({
                lat: marker.lat,
                lng: marker.lng,
            })
        })

        if (1 === this.mapMarkers.length) {
            this.map.setCenter(bounds.getCenter())
        } else {
            this.map.fitBounds(bounds)
        }
    }

    init() {
        if (!window.hasOwnProperty('google') ||
            !window.google.hasOwnProperty('maps') ||
            !window.google.maps.hasOwnProperty('Map') ||
            !window.google.maps.hasOwnProperty('Marker') ||
            !window.google.maps.hasOwnProperty('LatLngBounds') ||
            !window.google.maps.hasOwnProperty('MapTypeId') ||
            !window.google.maps.MapTypeId.hasOwnProperty('ROADMAP')) {
            console.log('Google maps isn\'t available')
            return
        }

        // before the map initialization, because during creation HTML is changed
        this.readMarkers()
        this.createMap()
        this.createMarkers()
        this.centerMap()
    }
}

class Maps {
    constructor() {
        this.isMapsLoaded = false
        this.mapsToInit = []

        // TODO change to yours if you've defined own callback (for https://maps.googleapis.com/maps/api...)
        window.googleMapsCallback = this.mapsLoadedCallback.bind(this)

        'loading' !== document.readyState ?
            this.setup() :
            window.addEventListener('DOMContentLoaded', this.setup.bind(this))
    }

    setup() {
        const observer = new MutationObserver((records, observer) => {
            for (let record of records) {
                record.addedNodes.forEach((addedNode) => {
                    this.addListeners(addedNode)
                })
            }
        })
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        })

        this.addListeners(document.body)
    }

    mapsLoadedCallback() {
        this.isMapsLoaded = true

        this.mapsToInit.forEach((map) => {
            map.init()
        })

        this.mapsToInit = []
    }

    addListeners(element) {
        if (Node.ELEMENT_NODE !== element.nodeType) {
            return
        }

        // TODO replace the selector if you've changed it in the markup

        element.querySelectorAll('.my-map').forEach((mapElement) => {
            let map = new Map(mapElement)

            if (!this.isMapsLoaded) {
                this.mapsToInit.push(map)

                return
            }

            map.init()
        })
    }

}

new Maps()



</script>
<!--
<?php if( have_rows('locations') ): ?>
    <div class="acf-map" data-zoom="16">
        <?php while ( have_rows('locations') ) : the_row(); 

            // Load sub field values.
            $location = get_sub_field('location');
            $title = get_sub_field('title');
            $description = get_sub_field('description');
            ?>
            <div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>">
                <h3><?php echo esc_html( $title ); ?></h3>
                <p><em><?php echo esc_html( $location['address'] ); ?></em></p>
                <p><?php echo esc_html( $description ); ?></p>
            </div>
    <?php endwhile; ?>
    </div>
<?php endif; ?>
-->