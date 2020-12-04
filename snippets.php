<?php

```php

// ---------------------------
// RELEVANSSI Remove Limit
// ---------------------------
add_filter( 'post_limits', 'rlv_postsperpage' );
function rlv_postsperpage( $limits ) {
    if ( is_search() ) {
        global $wp_query;
        $wp_query->query_vars['posts_per_page'] = 0;
    }
    return $limits;
}

// ---------------------------
// RELEVANSSI SEARCH FUNCTIONS
// ---------------------------
add_filter( 'relevanssi_modify_wp_query', 'rlv_force_post_product' );
function rlv_force_post_product( $query ) {
	$query->query_vars['post_types'] = ['post', 'page', 'product'];
	return $query;
}

add_filter( 'relevanssi_hits_filter', 'products_first' );
function products_first( $hits ) {
	$types = array();

	$types['product'] = array();
	$types['post']    = array();
	$types['page']    = array();

	// Split the post types in array $types
	if ( ! empty( $hits ) ) {
		foreach ( $hits[0] as $hit ) {
			array_push( $types[ $hit->post_type ], $hit );
		}
	}

	foreach ($types['product'] as $key => $post) {
		$field = get_post_field('product_sort_number', $post->ID);
		$types['product'][$key]->sort = $field;
	}

	function fieldsort($a, $b) {
		return strcmp($a->sort, $b->sort);
	}

	usort($types['product'], "fieldsort");

	// Merge back to $hits in the desired order
	$hits[0] = array_merge( $types['product'], $types['post'], $types['page'] );
	return $hits;

}
```
