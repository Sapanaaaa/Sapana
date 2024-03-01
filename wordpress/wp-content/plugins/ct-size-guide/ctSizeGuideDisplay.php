<?php

/**
 * Displays the size guide
 * @author jacek
 */
class ctSizeGuideDisplay {

	protected $tables;
	protected $sg_id;
	protected $disable_updates;
	protected $default_id;
	private $defaultSectionTabTitle = "Size Guide";
	/**
	 * Initializes object
	 */

	public function __construct() {
	    // For Single
		add_action( 'woocommerce_before_single_product', array( $this, 'displaySizeGuide' ), 70 );

		// For Variation
        add_action( 'woocommerce_before_single_variation', array( $this, 'displaySizeGuide' ), 70 );

		add_action( 'add_meta_boxes', array( $this, 'chooseSizeGuide' ) );

		add_action( 'save_post', array( $this, 'saveSizeGuideDropdown' ) );
		add_action( 'edit_post', array( $this, 'saveSizeGuideDropdown' ) );

		add_shortcode( 'ct_size_guide', array( $this, 'triggerSizeGuide' ) );

		add_action( 'wp_footer', array( $this, 'overlayColor' ) );

		$this->disable_updates = get_option('wc_size_guide_update');
		$this->default_id = $this->getSizeGuideID(get_option('wc_size_guide_default'));

	}

	/**
	 * Size Guide MetaBox
	 */

	public function chooseSizeGuide() {
		add_meta_box( 'ct_sizeguideopt', __( 'Choose size guide', 'ct-sgp' ), array(
			$this,
			'renderSizeGuideDropdown'
		), 'product', 'side' );
	}

	/**
	 * Select size guide per product
	 *
	 * @param $post
	 */

	public function renderSizeGuideDropdown( $post ) {
		$args = array(
			'post_type'   => 'ct_size_guide',
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => "ASC"
		);

		$sg_list = get_posts( $args );
		$current = get_current_screen()->action;

		//we have to check if there is already size guide post attached to the product
		$post_id = $post->ID;

		$sg_post_id = get_post_meta( $post_id, '_ct_selectsizeguide' );

		$sg_post_id = isset( $sg_post_id[0] ) ? $sg_post_id[0] : '';

		echo '<select id="ct_selectsizeguide" name="ct_selectsizeguide"><br>';
		echo '<option value="'.__('#NONE#', 'ct-sgp').'"></option>';
		foreach ( $sg_list as $sg_object ) {
			$sg_id    = $sg_object->ID;
			$sg_title = $sg_object->post_title;

			echo '<option value="' . $sg_id . '"' . selected( $sg_post_id, $sg_id ) . '>' . esc_html__($sg_title, 'ct-sgp') . '</option>';
		}
		echo '</select>';

		echo '<br><br><input type="checkbox" name="ct_disablesizeguide" id="ct_disablesizeguide" ' . checked( 'checked', $sg_post_id, false ) . '>'.esc_html__('Hide size guide from this product', 'ct-sgp').'</input>';
	}

	/**
	 * Save size guide per product
	 *
	 * @param $post_id
	 */

	public function saveSizeGuideDropdown( $post_id ) {
		$slug = 'product';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}

		if ( isset( $_POST['ct_selectsizeguide'] ) ) {
			$disablesg = isset( $_POST['ct_disablesizeguide'] ) ? 'on' : '';
			if ( $disablesg == 'on' ) {
				$disablesg = 'checked';
				update_post_meta( $post_id, '_ct_selectsizeguide', $disablesg );
			} else {
				$selectedsg = $_POST['ct_selectsizeguide'];
				update_post_meta( $post_id, '_ct_selectsizeguide', $selectedsg );

			}
		}
	}

	protected function getChildTermId( $parentTermId, $terms ) { // get the child term of the $parentTermId

		foreach ( $terms as $term ) {
			if ( $parentTermId === $term->parent ) { // if the id is equal to the parent id
				return $term->term_id; // return the term id
			}
		}
		return false;
	}

	/**
	 * Display size guide
	 *
	 * @param null $post_id
	 */

	public function displaySizeGuide( $post_id = null ) {
		$post_id = get_the_ID(); // product post id
		if($this->hasSizeGuide($post_id)){
			$sg_post_id = $this->sg_id; // sg_id is a global variable set by hasSizeGuide. (sg is SizeGuide)
			$size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
			if ( $size_table ) {
				$size_table   = $size_table[0];
				$this->tables = $size_table;
				$this->handleShortcodeRender( $size_table, $sg_post_id );
			}
		}
	}

	/**
	 * @param null $post_id
	 *
	 * @return bool
	 */
	public function hasSizeGuide( $productPostId = null ) {

		$return     = false;
		$sg_post_id = get_post_meta( $productPostId, '_ct_selectsizeguide' ); // get the selected sg(Size Guide) of the product
		$sg_post_id = $sg_post_id && $sg_post_id[0] != '#NONE#' ? $sg_post_id[0] : false;
		$this->sg_id = false;
		if ( $sg_post_id) { // if there is an assigned sg post to the product
			$this->sg_id = $sg_post_id;
			$size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
			$return = !!$size_table;
		} else { // if there is no assigned sizeguide to the product
			
			$productCategoryTerms   = wp_get_post_terms( $productPostId, 'product_cat' );
			$productTagTerms       = wp_get_post_terms( $productPostId, 'product_tag' );
			$productTerms      = array_merge($productCategoryTerms, $productTagTerms); // category + tags terms
			foreach ( $productTerms as $term ) {
				$termId = $term->term_id;
				do {
					$hasChildTerm = $this->getChildTermId($termId, $productTerms); // only used the child most category
					if($hasChildTerm){
						$termId = $hasChildTerm; // getChildTermId returns the child term id if it has
					}
				}while($hasChildTerm);
				$sg_post_id_candidate = get_term_meta( $termId, '_ct_assignsizeguide', true );
				if ( $sg_post_id_candidate != '' && get_post( $sg_post_id_candidate ) ) {
					$sg_post_id = $sg_post_id_candidate;
					break; // use the size guide of the first term with size guide
				}
			}
			if ( $sg_post_id ) {
				$this->sg_id = $sg_post_id;
				$size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
				$return = !!$size_table;
			}
		}
		return $return;
	}


	/**
	 * Render shortcode
	 */

	protected function handleShortcodeRender( $size_table, $sg_post_id ) {

		switch ( $this->getSgOption( "wc_size_guide_button_position" ) ) {

			case 'ct-position-tab':
				add_filter( 'woocommerce_product_tabs', array( $this, 'addSizeGuideTab' ) );
				break;

			case 'ct-position-add-to-cart':
				add_action( 'woocommerce_after_add_to_cart_button', array(
					$this,
					'doSgShortcode'
				), $this->getSgOption( 'wc_size_guide_button_priority', 60 ) );
				break;

			case 'ct-position-price':
				add_filter( 'woocommerce_get_price_html', array(
					$this,
					'addToPrice'
				), $this->getSgOption( 'wc_size_guide_button_priority', 60 ) );
				break;

			case 'ct-position-info':

				add_action( 'woocommerce_before_add_to_cart_button', array(
					$this,
					'doBfrAddShortcode'
				), $this->getSgOption( 'wc_size_guide_button_priority', 60 ) );
				break;
		}
		$this->renderSizeGuideTableOutput( $size_table, $sg_post_id );
	}

	/**
	 * Add Size Guide under the price tag
	 *
	 * @param $quantity
	 *
	 * @return string
	 */
	public function addToquantity( $quantity ) {
		return $quantity . '<br><br>' . do_shortcode( '[ct_size_guide]' );
	}


	/**
	 * Add Size Guide under the price tag
	 *
	 * @param $price
	 *
	 * @return string
	 */
	public function addToPrice( $price ) {
		echo "<style>.related.products .open-popup-link,.related.products .button_sg{display:none;}</style>";
		return $price . '<br><br>' . do_shortcode( '[ct_size_guide]' );	
	}


	/**
	 * WooCommerce custom tab
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */

	public function addSizeGuideTab( $tabs ) {
		
		$tabs['size_guide'] = array(
			'title'    => $this->getSgOption( "wc_size_guide_button_label", esc_html__('Size Guide', 'ct-sgp') ),
			'priority' => $this->getSgOption( 'wc_size_guide_button_priority', 20 ),
			'callback' => array( $this, 'renderSizeGuideTab' )
		);

		return $tabs;

	}

	/**
	 * Render WooCommerce tab
	 */

	public function renderSizeGuideTab() {

		global $post;
		if ( $this->hasSizeGuide( $post->ID) ) {
			$sg_table  = get_post_meta( $this->sg_id, '_ct_sizeguide' );
			$this->renderSizeGuideTableOutput( $sg_table[0], $this->sg_id, true );
		} else { // no selected sizeguide in product page - use default Size Guide post - latest ID
			echo $this->sizeGuideNotSetNoticeMessage();
		} 
	}

	public function renderSizeGuideTableOutput( $tables, $post_id, $is_tab = false, $is_shortcode = false ) {
		$sg_object  = get_post( $post_id );
		$sg_title   = $sg_object->post_title;
		$sg_content =  wpautop( $sg_object->post_content );
		if ( $this->getSgOption( 'wc_size_guide_button_position', 'ct-position-summary' ) == 'ct-position-summary' ) {
			add_action( 'woocommerce_single_product_summary', array(
				$this,
				'doSgShortcode'
			), $this->getSgOption( 'wc_size_guide_button_priority', 60 ) );
		}
		

		$htmlString = '
			<h2 class="sg_title">' . esc_html__($sg_title, 'ct-sgp') . '</h2>
		';
		/* Size Guide Table Generation */
		$isTabMultipleTable = $this->getSgOption('wc_size_guide_tab_multiple_table', 'no') == 'yes'; // tab view if there are multiple tables and Multiple Table tabbing
		$sizeGuideTabs = "";
		$sizeGuideTabContent = "";
		if($isTabMultipleTable){
			$contentSections = $this->getContentSections($sg_content);
			$key = -1;
			foreach($contentSections as $sectionTitle => $contentSection){
				$isActive = $sectionTitle == $this->defaultSectionTabTitle ? 'active' : '';
				$sizeGuideTabs .="<div class=\"tab " . $isActive. "\" tab-key=\"" . $key . "\" >" . esc_html__($sectionTitle, 'ct-sgp') . "</div>";
				$sizeGuideTabContent .= "<div class=\"content " . $isActive. "\" tab-key=\"" . $key . "\" >" .do_shortcode( $contentSection ) . "</div>";
				$key--;
			}
		}else{
			$removeTabPattern = preg_replace('#\#\#\#\[(.*?)\]#', '', $sg_content);
			$htmlString .= "<hr />
				<div class=\"sg_content\">" . do_shortcode( $removeTabPattern ) . "</div>
				<div style=\"clear:both;\"></div> 
				<hr />
			";
		}
		foreach ( $tables as $key => $table ) {
			$tableTitle = 'Size Guide ' . ($key + 1);
			$responsive_class = $this->getSgOption( 'wc_size_guide_display_mobile_table', 'ct-size-guide--Responsive' );
			$hovers           = $this->getSgOption( 'wc_size_guide_hovers_on_tables', 'no' );
			if ( ! empty( $table['title'] ) ) {
				$tableTitle = do_shortcode( $table['title'] );
			}
			$tableGuideHTMLString = '<h4 class="ct_table_title">' . $tableTitle . '</h4>';
			$sizeGuideTabs .= "<div class=\"tab\" tab-key=\"".($key + 1)."\" >$tableTitle</div>";
			$tableRows = '';
			$row_mark = 1; // determine the row count
			foreach ( $table['table'] as $row ) {
				$col_mark = 1;
				$tableRowColumns = '';
				foreach ( $row as $cell ) {
					if ( $row_mark == 1) {
						$tableRowColumns .= '<th>' . $cell . '</th>';
					}else if($col_mark == 1){
						$tableRowColumns .= '<th>' . $cell . '</th>';
					} else {
						$tableRowColumns .= '<td>' . $cell . '</td>';
					}
					$col_mark ++;
				}
				$row_mark ++;
				$tableRows .= '<tr>' . $tableRowColumns . '</tr>';
			}
			$hoverClass = $hovers == 'yes' ? 'ct-table-hovers' : '';
			$tableClassAttribute = $responsive_class . ' ' . $hoverClass;
			$tableHTMLString = '
				<table class="' . $tableClassAttribute . '">
					<tbody>' . $tableRows . '</tbody>
				</table>
			';
			$tableGuideHTMLString .= '<div class="ct_table_container">' . $tableHTMLString . '</div>';
			if($isTabMultipleTable){
				$sizeGuideTabContent .= "<div class=\"content\" tab-key=\"". ($key + 1) ."\" >" .$tableGuideHTMLString . "</div>";
			}else{
				$sizeGuideTabContent .= $tableGuideHTMLString;
			}
		}
		
		$sizeGuideTabs = "<div class=\"sizeGuideTabs\" >" . $sizeGuideTabs . "</div>";
		$sizeGuideTabContent = "<div class=\"sizeGuideTabContents\" >" . $sizeGuideTabContent . "<div style=\"clear:both\"></div></div>";
		
		if($isTabMultipleTable){
			$htmlString .= $sizeGuideTabs;
		}
		$htmlString .= $sizeGuideTabContent;
		/* End of Size Guide Table Generation */

		if ( ! empty( $tables[0]['caption'] ) ) {
			$htmlString .= '<p class="ct_table_caption">' . do_shortcode( $tables[0]['caption'] ) . '</p>';
		}
		$styleAttribute = '';
		$classAttribute = '';
		if ( $is_tab ) {
			$classAttribute = 'ct-size-guide sg ct_sg_tabbed';
		}elseif( $is_shortcode ){
			$classAttribute = 'ct-size-guide';
		}else {
			$pleft   = $this->getSgOption( 'wc_size_guide_modal_padding_left', 0 );
			$ptop    = $this->getSgOption( 'wc_size_guide_modal_padding_top', 0 );
			$pright  = $this->getSgOption( 'wc_size_guide_modal_padding_right', 0 );
			$pbottom = $this->getSgOption( 'wc_size_guide_modal_padding_bottom', 0 );

			$paddings = '';

			if ( $pleft > 0 ) {
				$paddings .= 'padding-left: ' . (int) $pleft . 'px; ';
			}
			if ( $ptop > 0 ) {
				$paddings .= 'padding-top: ' . (int) $ptop . 'px; ';
			}
			if ( $pright > 0 ) {
				$paddings .= 'padding-right: ' . (int) $pright . 'px; ';
			}
			if ( $pbottom > 0 ) {
				$paddings .= 'padding-bottom: ' . (int) $pbottom . 'px; ';
			}
			$classAttribute = 'ct-size-guide sg mfp-hide';
			$styleAttribute = $paddings;
		}
		echo '<div id="ct_size_guide-' . $this->sg_id . '" class="' . $classAttribute . '" style="' . $styleAttribute . '" >' 
			. $htmlString
			. '</div>';
	}
	public function getContentSections($sg_content){
		$sectionSeparator = '###[';
		$sectionTitleSeparator = ']';
		$explodedStringContent = explode($sectionSeparator, $sg_content);
		$sections = array();
		$sections[$this->defaultSectionTabTitle] = $explodedStringContent[0];
		if(count($explodedStringContent) > 1){
			for($x = 1; $x < count($explodedStringContent); $x++){
				$explodedSectionContent = explode($sectionTitleSeparator, $explodedStringContent[$x], 2);
				if(count($explodedSectionContent) > 1){
					$sections[$explodedSectionContent[0]] = $explodedSectionContent[1];
				}else{
					$sections['Section '. ($x + 1) . ': No Title ']  = $explodedSectionContent[0];
				}
				
			}
		}
		return $sections;


	}
	public function triggerSizeGuide($atts) {

		global $product;
		$hide   = get_option( 'wc_size_guide_hide' );
		$output = '';
		
		$atts = shortcode_atts(
			array(
				'postid' => '',
                'button' =>    '',
                'button_value' =>    '',
			), $atts );
        $btn_true = $atts['button'];
        $btn_value = $atts['button_value'];
        if(empty($btn_value)){
            $btn_val = $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . ' '.get_the_title($atts['postid']).'';
        }else{
            $btn_val = $btn_value;
        }

		if ( $product ) {
			if (!$this->hasSizeGuide($product->get_id())){
				$output = $this->sizeGuideNotSetNoticeMessage();
				return $output;
			} 

			$productStock        = $product->get_stock_quantity();
			$productAvailability = $product->get_availability();

			if ( $hide != 'yes' || $productAvailability['class'] != 'out-of-stock' ) {
				$trigger = $this->getSgOption( 'wc_size_guide_button_style', 'ct-trigger-button' );

				$align = $this->getSgOption( 'wc_size_guide_button_align', 'left' );
				if ( $this->getSgOption( 'wc_size_guide_button_position' ) == 'ct-position-add-to-cart' ) {
					$align = '';
				}
				$clear = $this->getSgOption( 'wc_size_guide_button_clear', 'no' );

				$mleft   = $this->getSgOption( 'wc_size_guide_button_margin_left', 0 );
				$mtop    = $this->getSgOption( 'wc_size_guide_button_margin_top', 0 );
				$mright  = $this->getSgOption( 'wc_size_guide_button_margin_right', 0 );
				$mbottom = $this->getSgOption( 'wc_size_guide_button_margin_bottom', 0 );

				$margins = '';

				if ( $mleft != 0 ) {
					$margins .= 'margin-left: ' . (int) $mleft . 'px; ';
				}
				if ( $mtop != 0 ) {
					$margins .= 'margin-top: ' . (int) $mtop . 'px; ';
				}
				if ( $mright != 0 ) {
					$margins .= 'margin-right: ' . (int) $mright . 'px; ';
				}
				if ( $mbottom != 0 ) {
					$margins .= 'margin-bottom: ' . (int) $mbottom . 'px; ';
				}

				if ( $trigger == 'ct-trigger-button' ) {

					$sg_set_icon = $this->getSgOption( 'wc_size_guide_button_icon' );

					$sg_icon_markup = ( $sg_set_icon == 'null' ) || $sg_set_icon == 'fa fa-blank' ? '' : '<span class="' . $sg_set_icon . '"></span>';
					$output = '<a class="open-popup-link ' . $this->getSgOption( 'wc_size_guide_button_class', 'button_sg' ) . '" href="#ct_size_guide-'.$this->sg_id.'" style="float: ' . $align . '; ' . $margins . '">' . $sg_icon_markup . $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . '</a>';
				} else {
					$output = '<a class="open-popup-link" href="#ct_size_guide-'.$this->sg_id.'" style="float: ' . $align . '; ' . $margins . '">' . $this->getSgOption( "wc_size_guide_button_label", esc_html__( "Size Guide", "ct-sgp" ) ) . '</a>';
				}
				if ( $clear == 'no' ) {
					$output .= '<div class="clearfix"></div>';
				}
			}

		}else{
           //NOTE: This will display on the pages only and not on the product
            $sg_post_id  = $atts['postid'];
            ob_start();
            if($btn_true == 'true'){
                // display if button = true
               echo '<a class="open-popup-link '. $this->getSgOption( 'wc_size_guide_button_class', 'button_sg' ).'" href="#ct_size_guide-'.$sg_post_id.'" ">' .__($btn_val,'ct-sgp').'</a>';
                $this->sg_id = $sg_post_id;
                $size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
                if ( $size_table ) {
                    $size_table   = $size_table[0];
                    $this->tables = $size_table;
                    $output =  $this->renderSizeGuideTableOutput( $size_table, $sg_post_id, false, false );
                }
            }else{
                // display if button = empty/null
                $this->sg_id = $sg_post_id;
                $size_table  = get_post_meta( $sg_post_id, '_ct_sizeguide' );
                if ( $size_table ) {
                    $size_table   = $size_table[0];
                    $this->tables = $size_table;
                    $output =  $this->renderSizeGuideTableOutput( $size_table, $sg_post_id, false, true );
                }
            }



			$output = ob_get_clean();
		}

		// var_dump($output);
		return $output;
	}

	protected function getSgOption( $opt, $default = "null" ) {
		$val = get_post_meta( $this->sg_id, '_ct_sizeguidesettings' );

		if ( $val ) {
			$val = $val[0];
		}

		// check if global value
		if (

			// margin dropdown
			( false !== strpos( $opt, 'wc_size_guide_button_margin_' ) &&
			  isset( $val['wc_size_guide_button_margins_dropdown'] ) &&
			  $val['wc_size_guide_button_margins_dropdown'] === 'global'
			) ||

			// padding dropdown
			(
				false !== strpos( $opt, 'wc_size_guide_modal_padding_' ) &&
				isset( $val['wc_size_guide_modal_padding_dropdown'] ) &&
				$val['wc_size_guide_modal_padding_dropdown'] === 'global'
			) ||

			// simple input global value
			(
				isset( $val[ $opt ] ) &&
				$val[ $opt ] == 'global' ||
				! $val
			)
			|| ( $opt === 'wc_size_guide_display_mobile_table' )
			|| ( $opt === 'wc_size_guide_hovers_on_tables' )
			|| ( $opt === 'wc_size_guide_active_hover_color' )
			|| ( $opt === 'wc_size_guide_lines_hover_color' )
			|| ($opt === 'wc_size_guide_button_icon')
		) {

			// get global value
			$val = get_option( $opt, $default );

			/** WPML support for global SG options */
			if ( function_exists( 'icl_register_string' ) ) {
				icl_register_string( 'ct-sgp', 'Size Guide option: ' . $opt, $val );
			}

			$val = apply_filters( 'wpml_translate_single_string', $val, 'ct-sgp', 'Size Guide option: ' . $opt );
			/** End WPML */

			// individual value
		} elseif ( isset( $val[ $opt ] ) && $val[ $opt ] != "" ) {

			$val = $val[ $opt ];

			// nothing found
		} else {

			$val = $default;

		}

		// var_dump($val);
		return $val;
	}


	public function overlayColor() {
		echo '<style>.mfp-bg{background:' . $this->getSgOption( 'wc_size_guide_overlay_color', '#000000' ) . ';}
					.ct_table_container .ct-table-hover{background: ' . $this->getSgOption( 'wc_size_guide_lines_hover_color', '#999999' ) . '; }
					.ct_table_container .ct-table-cursor{background: ' . $this->getSgOption( 'wc_size_guide_active_hover_color', '#2C72AD' ) . ';
					 color: #FFFFFF; }
					</style>';
	}

	public function doBfrAddShortcode() {
		echo do_shortcode( '[ct_size_guide]' . '<br>' );
	}

	public function doSgShortcode() {
		echo do_shortcode( '[ct_size_guide]' );
	}

	/**
	 * return notice msg
	 */
	public function sizeGuideNotSetNoticeMessage() {
		$output = '';
		if( current_user_can('administrator', 'editor') ) {
			$error = esc_html__( "Product has no SizeGuide, and has no default SizeGuide on its categories or tags (only admin can see this)", "ct-sgp" );
			$output = '<div class="' . $this->getSgOption( 'wc_size_guide_button_class', 'button_sg' ) . '" style="color:#fc5050;font-size:12px;">' . $error . '</div>';
		}
		return $output;
	}

	/**
	 * return SizeGuide ID
	 */
	public function getSizeGuideID($index = null) {

		$args = array(
			'post_type'   => 'ct_size_guide',
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => "ASC"
		);
		$sg_list = get_posts( $args );
		$sg_id = [];

		if( !empty($sg_list) ) {

			foreach($sg_list as $list) {
				$sg_id[] = $list->ID;
			}
		}
		return $sg_id[$index];
	}

}

new ctSizeGuideDisplay();