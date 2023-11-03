<?php
/**
 * Plugin Name: Custom Post Type Grids
 * Description: This Plugin Allows you to create amazing grids for any type of post type, it can be posts, products or any custom post type
 * Version: 1
 * Author: Ajay Malik
 * Author URI: https://Theonlined.com/
 **/


add_action('admin_menu', 'CUPOTYGR_menu');

function CUPOTYGR_menu()
{

    add_menu_page(
        'Grid Generator',
        // page <title>Title</title>
        'Grid Generator',
        // link text
        'manage_options',
        // user capabilities
        'grid_generator',
        // page slug
        'CUPOTYGR_page_callback',
        // this function prints the page content
        'dashicons-images-alt2',
        // icon (from Dashicons for example)
        4 // menu position
    );

    add_action('admin_init', 'register_CUPOTYGR_theme_settings');
}
function register_CUPOTYGR_theme_settings()
{
    //register our settings
    register_setting('CUPOTYGR-plugin-settings-group', 'CUPOTYGR_settings');
}


function CUPOTYGR_page_callback()
{
    echo "";
    ?>
    <div class='CUPOTYGR-wrap'>
        <div>
            <label for="post_type">Post Type:</label>
            <input type="text" id="post_type" name="post_type" placeholder="post,service,custom post type">
        </div>
        <div>
            <label for="show">Show Use <code>%custom_field_key%</code> to show custom field & always use <code>'</code> for
                HTML attribute. ( you can use as many custom field as you can): Available Keys <code>%title%</code>,<code>%excerpt%</code>,<br><code>%content%</code>,<code>%featuredimage%</code>,<br><code>%link%</code></label>
            <input  type="text" id="show" name="show" value="<div class='fimage'>%featuredimage%</div><h2>%title%</h2><p>%excerpt%</p><a href='%link%'>Read More</a>">
      

</input>
        </div>
        <div>
            <label for="pagination">Pagination:</label>
            <select id="pagination" name="pagination">
                <option value="true">True</option>
                <option value="false" selected>False</option>
            </select>
        </div>
        <div>
            <label for="container_class">Container Class:</label>
            <input type="text" id="container_class" name="container_class" placeholder="CSS class of items Container">
        </div>
        <div>
            <label for="item_class">Item Class:</label>
            <input type="text" id="item_class" name="item_class" placeholder="CSS Class of Items">
        </div>
        <div>
            <label for="per_page">Per Page:</label>
            <input type="text" id="per_page" name="per_page" placeholder='No. of items per page'>
        </div>

        <button onclick="generateShortcode()">Generate Shortcode</button>

        <p><textarea id="shortcode"></textarea></p>
    </div>
    <script>
        function generateShortcode() {
            const postType = document.getElementById("post_type").value.trim();
            const show = document.getElementById("show").value.trim();
            const pagination = document.getElementById("pagination").value;
            const containerClass = document.getElementById("container_class").value.trim();
            const itemClass = document.getElementById("item_class").value.trim();
            const perPage = document.getElementById("per_page").value.trim();

            let shortcode = `[custom-archive type="${postType}" show="${show}" pagination="${pagination}" container_class="${containerClass}" item_class="${itemClass}" per_page="${perPage}"]`;

            document.getElementById("shortcode").innerText = shortcode;
        }
    </script>
    <style>
        .CUPOTYGR-wrap>div {
            display: grid;
            grid-template-columns: 50% 50%;
            margin-bottom: 1em;
            align-items: center;
        }

        .CUPOTYGR-wrap {
            padding: 50px;
            background: #fff;
            margin: 50px;
            max-width: 800px;
        }

        .CUPOTYGR-wrap #show {
            height: 150px;
        }
        textarea#shortcode {
    width: 100%;
    height: 100px;
}
    </style>


    <?php
}
function custom_archive_shortcode($atts)
{
    $atts = shortcode_atts(
        array(
            'type' => 'post',
            'show' => '',
            'pagination' => 'false',
            'per_page' => 10,
            'container_class' => 'custom-archive',
            'item_class' => 'custom-archive-item'
        ), $atts);

    $args = array(
        'post_type' => $atts['type'],
        'posts_per_page' => $atts['per_page'],
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    );

    $query = new WP_Query($args);

    $output = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $content = $atts['show'];

            $fields = get_post_custom();
            foreach ($fields as $key => $value) {
                $field = '%' . $key . '%';
                $content = str_replace($field, $value[0], $content);
            }

            $content = str_replace('%title%', get_the_title(), $content);
            $content = str_replace('%content%', get_the_content(), $content);
            $content = str_replace('%excerpt%', get_the_excerpt(), $content);
            $content = str_replace('%featuredimage%', get_the_post_thumbnail(), $content);
            $content = str_replace('%link%', get_permalink(), $content);

            $output .= '<div class="' . $atts['item_class'] . '">' . $content . '</div>';
        }

        if ($atts['pagination'] === 'true') {
            $output .= '<div class="custom-archive-pagination">' . paginate_links(
                array(
                    'total' => $query->max_num_pages,
                    'current' => $query->get('paged'),
                )) . '</div>';
        }
    }

    wp_reset_postdata();

    $output = '<div class="' . $atts['container_class'] . '">' . $output . '</div>';

    return $output;
}

add_shortcode('custom-archive', 'custom_archive_shortcode');