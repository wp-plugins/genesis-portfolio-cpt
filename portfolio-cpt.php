<?php
/**
 * Plugin Name: Genesis Portfolio CPT
 * Plugin URI: https://llama-press.com
 * Description: Use this plugin to add a Portfolio CPT to be used with the "portfolio" sortcode or a LlamaPress portfolio page template,
 *              this plugin can only be used with the Genesis framework.
 * Version: 1.0
 * Author: LlamaPress
 * Author URI: https://llama-press.com
 * License: GPL2
 */

/*  Copyright 2014  LlamaPress LTD  (email : info@llama-press.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//include plugins
include( plugin_dir_path( __FILE__ ) . 'inc/plugins/plugins.php');

/**
 * This class creates a custom post type lp-portfolio, this post type allows the user to create 
 * portfolio entries to display in the Portfolio page template.
 *
 * @since 1.0
 * @link https://llama-press.com
 */
class lpPortfolio {
    /**
    * Initiate functions. 
    *
    * @since 1.0
    * @link https://llama-press.com
    */
    public function __construct( ){
        
        /** Create portfolio custom post type */
        add_action( 'genesis_init', array( $this, 'portfolio_post_type' ) );
        
        /** Register skills Taxonomy */
        add_action( 'genesis_init', array( $this, 'create_skills_tax' ) );
        
        /** Creates portfolio featured image for archive grid */
        add_image_size( 'lp-portfolio', 330, 230, TRUE );
        
        /** Creates shortcode */
        add_shortcode( 'portfolio', array( $this, 'portfolio_shortcode' ) );
        
        /* create text domain */
        load_plugin_textdomain( 'lp', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
        
        /* check what template to use for the single display */
        add_filter( 'single_template', array( $this, 'get_custom_post_type_template' ) );
        
        /* Register gallery Taxonomy */
        add_action( 'genesis_init', array( $this, 'create_portfolio_tax' ) );
        
        /* Add Genesis layout options */
        add_post_type_support( 'lp-portfolio', 'genesis-layouts' );
    }

    /**
    * Creates lp-portfolio custom post type.
    * 
    * @since 1.0
    * @link https://llama-press.com
    */
    public function portfolio_post_type() {
        register_post_type( 'lp-portfolio',
            array(
                'labels' => array(
                    'name' => __( 'Portfolio', 'lp' ),
                    'singular_name' => __( 'Portfolio items', 'lp' ),
                    'all_items' => __( 'All Portfolio items', 'lp' ),
                    'add_new' => _x( 'Add new Portfolio item', 'Portfolio item', 'lp' ),
                    'add_new_item' => __( 'Add new Portfolio item', 'lp' ),
                    'edit_item' => __( 'Edit Portfolio item', 'lp' ),
                    'new_item' => __( 'New Portfolio item', 'lp' ),
                    'view_item' => __( 'View Portfolio item', 'lp' ),
                    'search_items' => __( 'Search Portfolio items', 'lp' ),
                    'not_found' =>  __( 'No Portfolio items found', 'lp' ),
                    'not_found_in_trash' => __( 'No Portfolio items found in trash', 'lp' ), 
                    'parent_item_colon' => ''
                ),
                'exclude_from_search' => true,
                'has_archive' => false,
                'hierarchical' => false, 
                'taxonomies'   => array( 'lp-portfolio-cat', 'lp-skills' ),
                'public' => true,
                'menu_icon' => 'dashicons-book-alt',
                'rewrite' => array( 'slug' => 'portfolio' ),
                'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'revisions', 'page-attributes' ),
            )
        );
        flush_rewrite_rules();
    }

    /**
    * Create skills taxanomy for lp-portfolio custom post type.
    * 
    * @see portfolio_post_type()
    * @since 1.0
    * @link https://llama-press.com
    */
    public function create_skills_tax() {
        register_taxonomy(
            'lp-skills',
            'lp-portfolio',
            array(
                'label' => __( 'Skills', 'lp' ),
                'hierarchical' => true,
                'labels' => array('name' => _x( 'Skills', 'taxonomy general name', 'lp' ),
                                  'singular_name' => _x( 'Skill', 'taxonomy singular name', 'lp' ),
                                  'search_items' => __( 'Search Skills', 'lp' ),
                                  'popular_items'              => __( 'Popular Skills', 'lp' ),
                                  'all_items'                  => __( 'All Skills', 'lp' ),
                                  'parent_item'                => null,
                                  'parent_item_colon'          => null,
                                  'edit_item'                  => __( 'Edit Skill', 'lp' ),
                                  'update_item'                => __( 'Update Skill', 'lp' ),
                                  'add_new_item'               => __( 'Add New Skill', 'lp' ),
                                  'new_item_name'              => __( 'New Skill', 'lp' ),
                                  'separate_items_with_commas' => __( 'Separate skills with commas', 'lp' ),
                                  'add_or_remove_items'        => __( 'Add or remove skills', 'lp' ),
                                  'choose_from_most_used'      => __( 'Choose from the most common skills', 'lp' ),
                                  'not_found'                  => __( 'No skills found.', 'lp' ),
                                  'menu_name'                  => __( 'Skills', 'lp' ),)
            )
        );
    }
    
    
    /**
    * Create portfolio categories.
    * 
    * This function creates categories for portfolio items
    *
    * @since 1.0
    * @link https://llamapress.com
    */
    public function create_portfolio_tax() {
        register_taxonomy(
            'lp-portfolio-cat',
            'lp-portfolio',
            array(
                'label' => __( 'Portfolio Category', 'lp' ),
                'hierarchical' => false,
            )
        );
    }
    
    /**
    * Creates shortcode to display portfolio items on any post or page.
    * 
    * @since 1.0
    * @link https://llama-press.com
    */
    public function portfolio_shortcode( $atts ) {
            
          $atts = shortcode_atts( array(
                    'amount' => '',
                    'orderby' => '',
                    'order' => '',
                    'category' => ''
            ), $atts );
            $amount = $atts['amount'];
            $orderby = $atts['orderby'];
            $cat = $atts['category'];
            if( $orderby == "" ) $orderby = 'post_date';
            $order = $atts['order'];
            if( $order == "" ) $order = 'DESC';
            
            if( $amount != '' ){
                $args = array(
                    'post_type' => 'lp-portfolio',
                    'orderby'       => $orderby,
                    'order'         => $order,
                    'posts_per_page' => $amount,
                    
                );
            }
            else{
                $args = array(
                    'post_type' => 'lp-portfolio',
                    'orderby'       => $orderby,
                    'order'         => $order,

                );
            }
            
            if( $cat != "" ) $args['lp-portfolio-cat'] = $cat;
            
             $id = $post->ID;
             $layout = genesis_site_layout();
             if($layout == "full-width-content"){
                 $classMain = "one-fourth";
                 $num = 4;
             }
             else{
                 $classMain = "one-third";
                 $caption_push = " caption_push ";
                 $num = 3;
             }
             $loop = new WP_Query( $args );
             if( $loop->have_posts() ){
                 //loop through portfolio items
                while( $loop->have_posts() ): $loop->the_post();
                    if( 0 == $loop->current_post || 0 == $loop->current_post % $num )
                    $class = $classMain . ' first';
                    $excerpt = get_the_excerpt();
                    if($excerpt != ""){
                        $text = substr($excerpt, 0, 80);
                    }
                    else{
                        if( get_the_content()){
                            $text = substr(get_the_content, 0, 80);
                        }
                        else{
                            $text = "";
                        }
                    }
                    $content .= "<div class='lp-grid-item $class'>";
                        $content .= "<div class='lp-portfolio-item'>";
                                    if( has_post_thumbnail(  ) ){
                                        $content .= get_the_post_thumbnail(get_the_id(), 'lp-portfolio');
                                    }
                                    else{
                                        $content .= "<img class='attachment-lp-portfolio wp-post-image' src='" . plugins_url( 'img/grid-bg.png' , __FILE__ ) . "' />";
                                    }
                            $content .= "<strong><a href='" . get_the_permalink() . "'>" . get_the_title() . "</a></strong>";
                            $content .= "<div class='lp-caption'>";
                                $content .= "<div class='caption_info$caption_push'>";
                                    $content .= "<p class='hidden-md'>" . $text . "</p>";
                                    $content .= "<a class='lp-btn lp-btn-white' href='". get_the_permalink() ."'>Read More&nbsp;&nbsp;<i class='fa fa-arrow-circle-right'></i></a>";
                                $content .= "</div>";
                            $content .= "</div>";
                        $content .= "</div>";
                    $content .= "</div>";
                    $class = $classMain;
                endwhile;
                 
                $content .= "<div class='clearfix'></div>";
             } 
             wp_reset_postdata();
              
             
             if( $content )
             return $content;
    } 

    /**
    * Loads the correct template.
    * 
    * Checks to see if the current theme has a template titled single-lp-portfolio, if it does then that file is used and if not then the plugin template file is used.
    * 
    * @since 1.0
    * @link https://llama-press.com
    */
    public function get_custom_post_type_template($single_template) {
         global $post;

         $located = locate_template( 'single-lp-portfolio.php' );
         if ($post->post_type == 'lp-portfolio' && empty( $located )) {
              $single_template = dirname( __FILE__ ) . '/single-portfolio.php';
         }
         return $single_template;
    }

    
}

$portfolio = new lpPortfolio();

?>