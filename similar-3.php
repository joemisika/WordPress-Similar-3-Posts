<?php
 /*
  * Plugin Name: Similar 3
  * Plugin URI: http://similar-3.com
  * Description: A plugin that inserts into the content of a given post, the lastest 3 published posts from the same category (related posts)
  * Version: 1.0
  * Author: Joe Misika
  * Author URI: http://joemisika.com
  * License: GPL2
  */

add_action('wp_enqueue_scripts', 'enqueue_ajax_scripts');

function enqueue_ajax_scripts(){
    	global $post; //wordpress post global object
    	$category = get_the_category();
    	$firstCategory = $category[0]->cat_name;
    	//echo admin_url('admin-ajax.php');
	wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );
    	wp_enqueue_style( 'similar-3', plugins_url( '/css/similar.css', __FILE__ ) );

    	wp_enqueue_script( 'ajax-script', plugins_url( '/js/similar-3.js', __FILE__ ), array('jquery') );
    	wp_localize_script('ajax-script', 'relatedobject', array('postid'=>$post->ID, 'category'=>$firstCategory, 'ajax_url'=> admin_url( 'admin-ajax.php' ) ));
}

add_filter('the_content', 'post_page');

function post_page($content){
    	global $post; //wordpress post global object
    	$category = get_the_category();
    	$firstCategory = $category[0]->cat_name;

    	if($post->post_type == 'post')
    	{
        		$content.= '<h3><a id="similar3" name="Similar3">See related articles</a></h3><div id="related-articles" class="row"></div>';
    	}
    	return $content;
}

//add_action('wp_ajax_nopriv_getRelated', 'getRelated');
add_action( 'wp_ajax_getRelated', 'getRelated');

function getRelated()
{
	$postid = $_POST['postid'];
	$category = $_POST['category'];
	$args = array(
		'category_name'=>$category,
		'posts_per_page' => 3,
		'post__not_in' => array($postid)
		);
	$result = '';
	//echo '<div class="row">';
	$related_posts = new WP_Query($args);
	//print_r($related_posts);
	if($related_posts->have_posts()):

		while($related_posts->have_posts()): $related_posts->the_post();

		$relatedpic = wp_get_attachment_image_src(get_post_thumbnail_id());

		$result .='<div class="col-md-4 col-sm-12 col-xs-12">';
		$result .='<a href=" '.esc_url(get_the_permalink()).' "><img src="'.$relatedpic[0].'" class="img-responsive"></a> ';
		$result .='<h5><a href=" '.esc_url(get_the_permalink()).' ">'.get_the_title().'</a></h5>';
		$result .='<p>'.substr( get_the_content(), 0, 150).'....</p>';
		$result .='</div>';

		endwhile;
	endif;
	wp_reset_postdata();
	echo json_encode(array('result' => $result));
	die();
}

?>