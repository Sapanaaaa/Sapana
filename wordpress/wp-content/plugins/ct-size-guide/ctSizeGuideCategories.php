<?php

/**
 * assign a category meta to the Size Guide post(create/edit post)
 * @author jacek
 */
class ctSizeGuideCategories {

	/**
	 * Inits object
	 */

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'chooseProductCategories' ) );
		add_action( 'save_post_ct_size_guide', array( $this, 'saveSizeGuideCategories' ) );
	}

	/**
	 * Register metabox
	 */

	public function chooseProductCategories() {
		add_meta_box( 'ct_sizeguideopt', __( 'Choose product categories', 'ct-sgp' ), array(
			$this,
			'renderSizeGuideCategories'
		), 'ct_size_guide', 'side' );
	}

	/**
	 * renders names of the parent terms
	 *
	 * @param $term_object
	 */
	public function getTermParents( $term_object ) {
		$term_parent = $term_object->parent;
		if ( is_integer( $term_parent ) && $term_parent != '0' ) {
			_e( ' in: ', 'ct-sgp' );
			$term_id       = $term_parent;
			$term_object   = get_term( $term_id, 'product_cat' );
			$terms_array[] = $term_object->name;
			echo $term_object->name;
			$this->getTermParents( $term_object );
		}
	}

	/**
	 * Render categoriers
	 *
	 * @param $post
	 */

	public function renderSizeGuideCategories( $post ) {
		$args = array(
			'hierarchical' => 1,
			'taxonomy'     => 'product_cat',
			'orderby'     => 'name',
		);

		$post_id = $post->ID;

		$assignedcats = get_post_meta( $post_id, '_ct_assignedcats' );
		if ( ! empty( $assignedcats ) ) {
			$assignedcats = $assignedcats[0];
		}

		$sg_cat_list = get_categories( $args );
		
		foreach ( $sg_cat_list as $sg_cat ) {
			$checked = false;
			if ( is_array($assignedcats) && in_array( $sg_cat->term_id, $assignedcats ) ) {
				$checked = 'checked';
			}
			echo '<input type="checkbox" name="ct_sgcategory[]" value="' . esc_html__($sg_cat->term_id) . '" ' . $checked . ' />';
			echo esc_html__($sg_cat->name, 'ct-sgp');
			$this->getTermParents( $sg_cat );
			echo '</input><br/>';
		}


	}

	/**
	 * Save categories
	 *
	 * @param $post_id
	 */

	public function saveSizeGuideCategories( $post_id ) { // Size Guide Post ID
		error_log('================SaveSizeGuideCategories=============');
		$termMetaKey = '_ct_assignedcats';
		$selectedTerms = isset( $_POST['ct_sgcategory'] ) ? $_POST['ct_sgcategory'] : [];
		$previousPostMetaValues = get_post_meta( $post_id, $termMetaKey);
		$previousPostMetaValues = $previousPostMetaValues ? $previousPostMetaValues[0] : [];
		$toDeleteTerms = $previousPostMetaValues; // ids that exists in $previousPostMetaValues but not in $selectedTerms
		$toCreateTerms = []; // ids that exists in $selectedTerms but not in $previousPostMetaValues
		$removedInToDeleteTerms = [];
		foreach($selectedTerms as $selectedTermId){
			$selectedTermKeyInPreviousPostMeta = array_search($selectedTermId, $previousPostMetaValues);
			if(in_array($selectedTermId, $previousPostMetaValues)){ // $selectedTermId exists in $previousPostMetaValues
				unset($toDeleteTerms[$selectedTermKeyInPreviousPostMeta]); // remove if it exists, the remaining values in toDeleteTerms will be deleted
				$removedInToDeleteTerms[] = $selectedTermId . $selectedTermKeyInPreviousPostMeta;
			}else{ // $selectedTermId does not exists in $previousPostMetaValues
				$toCreateTerms[] = $selectedTermId; // add the term id to be created
			}
		}
		error_log('selectedTerms: ' . json_encode($selectedTerms));
		error_log('previousPostMetaValues: ' . json_encode($previousPostMetaValues));
		error_log('removedInToDeleteTerms: ' . json_encode($removedInToDeleteTerms));
		error_log('toDeleteTerms: ' . json_encode($toDeleteTerms));
		error_log('toCreateTerms: ' . json_encode($toCreateTerms));
		foreach($toDeleteTerms as $toDeleteTerm){
			delete_term_meta( $toDeleteTerm, '_ct_assignsizeguide', $post_id );
		}
		foreach($toCreateTerms as $toCreateTerm){
			add_term_meta( $toCreateTerm, '_ct_assignsizeguide', $post_id );
		}
		update_post_meta( $post_id, $termMetaKey, $selectedTerms );
		error_log('================END=============');
	}

}

new ctSizeGuideCategories();