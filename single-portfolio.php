<?php
/**
 * Single portfolio template.
 *
 * This template is only used if the theme does not have a template titled "single-lp-portfolio dot php", all it does is
 * add the portfolio skills under the post info.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */

/* initiate lp_skills function */
add_filter( 'genesis_post_info', 'lp_skills' );
/* mod post meta */
add_filter( 'genesis_post_meta', 'lp_portfolio_meta' );
//* Remove the author box on single posts HTML5 Themes
remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );

/**
* Add skills.
* 
* This adds a list of the skills used in this project under the post info.
* 
* @see create_skills_tax()
* @since 1.0
* @link https://llama-press.com
*/
function lp_skills( $post_info ){
    $terms = get_the_terms( get_the_id(), 'lp-skills' );
    if( $terms ){
        foreach( $terms as $term ){
            $string .= $term->name . ", ";
        }
        echo "<p>" . __( "Skills required for this project", "lp" ) . ": " . rtrim($string, ", ") . "</p>";
    }

}
/**
* Add categories.
* 
* Removes the "Filed under".
* 
* @see create_skills_tax()
* @since 1.0
* @link https://llama-press.com
*/
function lp_portfolio_meta( $post_meta ){
    return;
}

genesis();
?>