<?php
/*
Plugin Name: WP Theme Tutorial - Simple Photo Popup
Plugin URI: http://wpthemetutorial.com
Description: Adds a Photo post type and provides as shortcode to display all photo thumbs. Clicking a photo thumb links to the large version defined in your WordPress settings.
Version: 1.0
Author: WP Theme Tutorial, Curis McHale
Author URI: http://wpthemetutorial.ca
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

add_image_size( 'thwp-photo-thumb', 200, 200, true );

/**
 * Builds out the custom post types for the site
 *
 * @uses    register_post_type
 *
 * @since   1.0
 * @author  WP Theme Tutorial, Curtis McHale
 */
function theme_t_wp_photo_gallery(){

    register_post_type( 'thwp_photo_gallery', // http://codex.wordpress.org/Function_Reference/register_post_type
        array(
            'labels'                => array(
                'name'                  => __('Photos'),
                'singular_name'         => __('Photo'),
                'add_new'               => __('Add New'),
                'add_new_item'          => __('Add New Photo'),
                'edit'                  => __('Edit'),
                'edit_item'             => __('Edit Photo'),
                'new_item'              => __('New Photo'),
                'view'                  => __('View Photo'),
                'view_item'             => __('View Photo'),
                'search_items'          => __('Search Photo'),
                'not_found'             => __('No Photos Found'),
                'not_found_in_trash'    => __('No Photos found in Trash')
                ), // end array for labels
            'public'                => true,
            'menu_position'         => 5, // sets admin menu position
            'menu_icon'             => plugins_url( 'wpthemetutorial-simple-photo-popup/photo-icon.png' ),
            'hierarchical'          => false, // functions like posts
            'supports'              => array('title', 'editor', 'revisions', 'thumbnail'),
            'rewrite'               => array('slug' => 'photo', 'with_front' => true,), // permalinks format
            'can_export'            => true,
        )
    );

}
add_action( 'init', 'theme_t_wp_photo_gallery' );

/**
 * Adds any extra theme styles
 *
 * @uses    wp_enqueue_style
 *
 * @since   1.0
 * @author  SFNdesign, Curtis McHale
 */
function theme_t_wp_photo_gallery_theme_styles_scripts(){

    // enqueue our styles
    wp_enqueue_style( 'fancyboxcss', plugins_url( '/wpthemetutorial-simple-photo-popup/jquery.fancybox.css' ), '', '1.0', 'all' );
    wp_enqueue_style( 'thwpstyles', plugins_url( '/wpthemetutorial-simple-photo-popup/styles.css' ), '', '1.0', 'all' );

    // setting up fancybox and our theme scripts
    wp_enqueue_script('fancybox', plugins_url( '/wpthemetutorial-simple-photo-popup/jquery.fancybox.pack.js' ), array('jquery'), '1.0', true);
    wp_enqueue_script('thwpphotoscript', plugins_url( '/wpthemetutorial-simple-photo-popup/photo-gallery-scripts.js' ), array('jquery', 'fancybox' ), '1.0', true);

    // getting Ajax ready for the plugin
    wp_localize_script( 'thwpphotoscript', 'THWPPhotoAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}
add_action( 'wp_enqueue_scripts', 'theme_t_wp_photo_gallery_theme_styles_scripts' );

/**
 * Gets our content for the photo popup
 *
 * @since   1.0
 * @author  SFNdesign, Curtis McHale
 */
function theme_t_wp_get_photo_popup(){

    // extract the post id
    $id = $_POST['ID'];

    // get our post object
    $post = get_post( $id );

    echo get_the_post_thumbnail( $id, 'medium' );

    echo '<div class="popup-caption">';
    echo wp_kses_post( wpautop( $post->post_content ) );
    echo '</div>';

    // we die or Ajax gets a 0
    die;

}
add_action( 'wp_ajax_get_photo', 'theme_t_wp_get_photo_popup' );
add_action( 'wp_ajax_nopriv_get_photo', 'theme_t_wp_get_photo_popup' );

/**
 * Creates a shortcode that shows the photo gallery information
 *
 * @since   1.0
 * @author  SFNdesign, Curtis McHale
 */
function theme_t_wp_display_photo_gallery(){ ?>

    <section class="thwp-photo-gallery-wrapper">

      <?php

        // defining the arguements for the custom loop
        $photoGallery = new WP_Query( array(
            'post_type'                 => 'thwp_photo_gallery',
        )); // end query

        if ( $photoGallery->have_posts() ) : while ( $photoGallery->have_posts() ) : $photoGallery->the_post();

      ?>

          <article <?php post_class(); ?> id="photo-<?php the_ID(); ?>">
              <a href="<?php the_permalink(); ?>" id="<?php the_ID(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'thwp-photo-thumb' ); ?></a>
          </article>

      <?php endwhile; else: ?>

          <h3>Oops!! Looks like something went wrong. Please get in touch with the site <a href="mailto:<?php echo get_bloginfo('admin_email'); ?>">administrator</a> and we'll get this sorted out</h3>

      <?php endif; ?>

      <?php wp_reset_query(); ?>

    </section>

<?
}
add_shortcode( 'thwp_photo_gallery', 'theme_t_wp_display_photo_gallery' );
