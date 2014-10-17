<?php
/**
 * Plugin Name: Genesis Portfolio CPT
 * Plugin URI: https://llama-press.com
 * Description: Use this plugin to add a Portfolio CPT to be used with the "portfolio" sortcode or a LlamaPress portfolio page template,
 *              this plugin can only be used with the Genesis framework.
 * Version: 1.1
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