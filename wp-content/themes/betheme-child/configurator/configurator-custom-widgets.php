<?php
// Custom Widget Area
function configurator_custom_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Configurator Sidebar', 'configurator_custom' ),
		'id' => 'configurator-sidebar',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h1>',
		'after_title' => '</h1>',
	) );
}
add_action( 'widgets_init', 'configurator_custom_widgets_init' );

// filter
function config_sort_by_key( $query ) {
	if (!is_admin()) {
		if(isset($_GET['config_orderby']) && !empty($_GET['config_orderby'])) {
			if($_GET['config_orderby'] == 'price-desc') {
				$query->set('orderby', 'meta_value_num');   
				$query->set('meta_key', 'addon_price');  
				$query->set('order', 'desc'); 
			}
			if($_GET['config_orderby'] == 'price') {
				$query->set('orderby', 'meta_value_num');   
				$query->set('meta_key', 'addon_price');  
				$query->set('order', 'asc'); 
			}
			if($_GET['config_orderby'] == 'date') {
				$query->set('orderby', 'date');   
				$query->set('order', 'desc'); 
			}
			if($_GET['config_orderby'] == 'title') {
				$query->set('orderby', 'title');   
				$query->set('order', 'asc'); 
			}

		}
	}
}


function custom_product_filter_query( $query ) {
	if (!is_admin()) {
		if(isset($_GET['post_tags']) && !empty($_GET['post_tags'])) {
			add_filter( 'posts_join' , 'multiple_meta_keys_join');
		} 
		if(isset($_GET['post_tags']) && !empty($_GET['post_tags']) || !empty($_GET['max_custom_price'])) {

			
			$query->set( 'meta_key', 'post_title_tags' );   
			add_filter( 'posts_where', 'custom_filter_meta_query',10, 2);
		}
	}
	return $query;
}
function multiple_meta_keys_join($join){
	global $wpdb;
	$join .= " LEFT JOIN $wpdb->postmeta as meta_1 ON $wpdb->posts.ID = meta_1.post_id LEFT JOIN $wpdb->postmeta as meta_2 ON $wpdb->posts.ID = meta_2.post_id";
	return $join;
}


// tags filters
function custom_filter_meta_query( $where = '', $query ) {
    global $wpdb;
  
    $where = '';
    $post_tags_arr = $query->get('post_tags_arr');
    $search_term = $query->get('search_component_post_title');
    $price_filter = $query->get('search_component_price');
    $current_term_category = $query->get('current_term_category');
    $max_custom_price = $_GET['max_custom_price'];
    $min_custom_price = $_GET['min_custom_price'];
    $start_bracket = '';
    $price_start_bracket = '';
    $key_meta = $wpdb->postmeta;
    if(!empty($post_tags_arr)) {
    	$start_bracket = ')';
    	$key_meta = 'meta_2';
    }
	$search_query = " AND ( wp_term_relationships.term_taxonomy_id IN ($current_term_category))";
    if(!empty($post_tags_arr) || !empty($search_term)) {
    	$price_start_bracket = ')';
    	$search_query = '';
    }
    
    if (isset($search_term) && !empty($search_term)) {

    	$search_query .=  $start_bracket. '  AND (' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
    }
    if (isset($price_filter) && !empty($price_filter)) {

    	$search_query .= " $price_start_bracket AND ( ( $key_meta.meta_key = 'addon_price' AND CAST($key_meta.meta_value AS SIGNED) BETWEEN '$min_custom_price' AND '$max_custom_price' ) )"; 
    }

    if(!empty($post_tags_arr)) {
     
        $sql_query = '';
        $selected_term_values = array_keys($_GET['post_tags']);


     
        
      
        $search_bracket = '';

      
        
        $f=0;
        $orc = 0;
        $store_unselected =[];
        $group_not_selected =[];
        foreach($post_tags_arr as $post_title_tag) {
        	$flag = 0;
        	$iterate = 0;
        	$group_selected = [];
        	foreach($post_title_tag as $index => $post_tag) {
        		$or = 'OR';
        		if ($index == end($post_title_tag)){
        			$or = '';
        		}
        		if(!empty($selected_term_values)) {
        			if(in_array($post_tag,$selected_term_values)) {

                        //search 
        				if(isset($search_term) && !empty($search_term) || isset($price_filter) && !empty($price_filter)) {
        					if($orc < 1) {
        						$search_bracket = '(';
        					} else {
        						$search_bracket = '';
        					}
        				}
        				$orc++;
        				if($iterate < 1)
        				{
        					$sql_query .= " AND $search_bracket ( ( meta_1.meta_key = 'post_title_tags' AND meta_1.meta_value REGEXP '(^|,)$post_tag(,|$)' )"; 
        				}
        				else
        				{
        					$sql_query .= "OR ( meta_1.meta_key = 'post_title_tags' AND meta_1.meta_value REGEXP '(^|,)$post_tag(,|$)' ) ";
        				}
        				$iterate++;

        				$flag = 1;
        				$f++;
        				$group_selected[] = $post_tag;
        			}
        		}
        		 
        	}

        	if($flag) {

        		if(!empty($group_selected))
        		{
        			$arra_diff = array_diff($post_title_tag,$group_selected );
        			foreach($arra_diff as $a_diff){
        				$group_not_selected[] = $a_diff;
        			}
        		}
        		$sql_query .= ' ) ' ;
        	}
        	else
        	{
        		$store_unselected[] = $post_title_tag;
        	}
        }
        if(!empty($group_not_selected))
        {
            $i = 0;
            foreach ($group_not_selected as $key => $value) {
                $sql_query .= " and ( meta_1.meta_key = 'post_title_tags' AND meta_1.meta_value NOT REGEXP '(^|,)$value(,|$)' ) ";
            }
        }

        $where .= $sql_query;
    }
    //return $where;

    return $where.''.$search_query;
}

function prefix_change_category_cpt_posts_per_page( $query ) {
    if ( $query->is_main_query() && ! is_admin()  && is_tax( 'configurator-category' )) {
        $query->set( 'post_type', array( 'configurator' ) );
        $query->set( 'posts_per_page', '6' );
    }
}
add_action( 'pre_get_posts', 'prefix_change_category_cpt_posts_per_page' );



function custom_pagination( $numpages = '', $pagerange = '', $paged='' ) {

    if (empty($pagerange)) {
        $pagerange = 6;
    }

    global $paged;
    if (empty($paged)) {
        $paged = 1;
    }
    if ($numpages == '') {
        global $wp_query;
        $numpages = $wp_query->max_num_pages;
        if(!$numpages) {
            $numpages = 1;
        }
    }


    $big = 999999999;
    $pagination_args = array(
        'base'            => str_replace($big, '%#%', esc_url(get_pagenum_link($big ) ) ),
        'format'          => '/page/%#%',
        'total'           => $numpages,
        'current'         => $paged,
        'show_all'        => False,
        'end_size'        => 1,
        'mid_size'        => $pagerange,
        'prev_next'       => True,
        'prev_text'       => __('&laquo;'),
        'next_text'       => __('&raquo;'),
        'type'            => 'plain',
        'add_args'        => false,
        'add_fragment'    => ''
    );

    $paginate_links = paginate_links($pagination_args);

    if ($paginate_links) {


      echo $paginate_links;

  }

}