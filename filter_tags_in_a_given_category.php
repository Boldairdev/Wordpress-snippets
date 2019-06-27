<?php 

// used when the query is a search
function bda_advanced_search_query($query) {

	if( is_admin() ) {
		return $query;
	}
	if($query->is_search()) {
		
		// tag search
		if (isset($_GET['taglist']) ) {
				$query->set('tag', filter_var($_GET['taglist'],FILTER_SANITIZE_STRING));
		}

		// category filter
	  	if (isset($_GET['category_name'])) {
			$meta_query[] = array(
			            'key'		=> 'category_name',
            			'value'		=> filter_var($_GET['category_name'],FILTER_SANITIZE_STRING),
            			'compare'	=> 'IN',
						'relation'	=>	'AND'
			);
		}

	  	return $query;
	}

}
add_action('pre_get_posts', 'bda_advanced_search_query', 1000);


// sql query that find tags for post in the $args category
// adapted from  https://www.wprecipes.com/wordpress-trick-function-to-get-tags-related-to-category/#comment-927

function bda_get_category_tags($args) {
	global $wpdb;
  // 
  $sql = "
				SELECT DISTINCT
				terms2.term_id as term_id, terms2.name as name, terms2.slug as slug, t2.count as posts_count
				

				FROM
					$wpdb->posts as p1
					LEFT JOIN $wpdb->term_relationships as r1 ON p1.ID = r1.object_ID
					LEFT JOIN $wpdb->term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
					LEFT JOIN $wpdb->terms as terms1 ON t1.term_id = terms1.term_id,

					$wpdb->posts as p2
						LEFT JOIN $wpdb->term_relationships as r2 ON p2.ID = r2.object_ID
						LEFT JOIN $wpdb->term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
						LEFT JOIN $wpdb->terms as terms2 ON t2.term_id = terms2.term_id

				WHERE (
						t1.taxonomy = 'category' AND
						p1.post_status = 'publish' AND
						terms1.term_id = '".$args['categories']."' AND
						t2.taxonomy = 'post_tag' AND
						p2.post_status = 'publish' AND
						p1.ID = p2.ID
					  )
			    ORDER by name
			";

  			$tags = $wpdb->get_results($sql);
 
  $count = 0;
  //Find all tag links
	foreach ($tags as $tag) {
		$tags[$count]->tag_link = get_tag_link($tag->term_id);
		++$count;
	}
	return $tags;
}

// Generates the form

function bda_get_tag_from_current_category() {
  $category_name	= get_query_var('category_name');
  $category_id 	= get_query_var('cat');
  
  $args = array('categories' => $category_id);
	$terms = bda_get_category_tags($args);

  // if we are alresdy on a result page use the tag of what was searched for
  if (isset($_GET['taglist']) ) {
 		$current_tag = filter_var($_GET['taglist'],FILTER_SANITIZE_STRING);
    $current_term =  get_term_by( 'slug', $current_tag, 'post_tag');
	}
  
  // the form outputs a search URL like "?s=&category_name=current-category-slug&taglist=tag-slug-to-filter-by"
  
  $output = '<div class="shortcode"><form method="get" action="' . get_bloginfo('url') .'">';
	$output .= '<fieldset>';
	$output .= '<input type="hidden" name="s" value=""/>';
	$output .= '<input type="hidden" name="category_name" value="'.$category_name.'"/>';
	$output .= '<h3>'. __('Trier par thème', 'authentic').'</h3>';
  
  // if we are already on a result page output a text with what was searched
  if(isset($current_tag)) {
		$output.= '<p class="you-have-filtered">>'.__('You have filtered post with the keyword', 'domain-text').' “' . $current_term->name .'”.</p>';
	}
  
  $output .= '<select name="taglist"><option value="">' .__(' -- Select a tag -- ', 'domain-text'). '</option>';


	foreach ( $terms as $term )
	{
	  $selected ='';

    // select the currently searched tag in the list if there is one
	  if($term->slug == $current_term->slug) {
		  $selected = 'selected="selected"';
	  }

    // output only the option if there are posts with taht tag and the tag does not have the name of the current category
    // since some people add a tag to all posts that's the same as the category, for whatever reason
	  if( $term->posts_count > 0 && $term->slug != $category_name) {
		  $output .= '<option value="' . $term->slug . '" '.$selected.' > ' . $term->name . "</option>\n";
	  }
	}
 	$output .= '</select>
	<button type="submit" class="btn btn-primary btn-filter-by-tag">'.__('Search', 'domain-text').'</button>
	</fieldset>
	</form></div>';
	return $output; 

}

add_shortcode( 'filter_by_tags_form', 'bda_get_tag_from_current_category' );
?>
