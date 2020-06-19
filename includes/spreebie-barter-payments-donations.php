<?php

/*------------------------------------------------------------------*
 * The 'spreebie_barter_payments_donations' class
 * @author Thabo David Nyakallo Klass
/*------------------------------------------------------------------*/

if(!class_exists('spreebie_barter_payments_donations')) :

class spreebie_barter_payments_donations {
	// Taxonomy name for custom taxonomy metabox
	public $spreebie_barter_payments_donations_taxonomy;
	
	// Taxonomy id for custom taxonomy metabox
	public $spreebie_barter_payments_donations_taxonomy_metabox_id;
	
	// Post type text for custom taxonomy metabox
	public $spreebie_barter_payments_donations_post_type;
	
	
	/**
	* The 'spreebie_barter_payments_donations' constructor
	* 
	*/
	
	public function __construct() {
	    
	    // Start the static function to initialize the custom post type
	    // and custom taxonomy
	    self::spreebie_barter_payments_donations_post_type_and_taxonomy_init();
	    
	    // The payments and donations metaboxes
	    $this->spreebie_barter_payments_donations_video_metaboxes();
	    
	    $this->spreebie_barter_payments_donations_taxonomy = 'sb_p_and_d_category';
	    $this->spreebie_barter_payments_donations_taxonomy_metabox_id = 'sb_p_and_d_categorydiv';
	    $this->spreebie_barter_payments_donations_post_type = 'sb_p_and_d';
	    
	    // Remove old taxonomy meta box  
	    add_action( 'admin_menu', array($this, 'spreebie_barter_payments_donations_remove_meta_box'));
	    
	    // Add new screenshot meta box  
	    add_action('add_meta_boxes', array($this, 'spreebie_barter_payments_donations_add_screenshot_meta_box'));
	    
	    // Add new taxonomy meta box  
	    add_action('add_meta_boxes', array($this, 'spreebie_barter_payments_donations_add_taxonomy_meta_box'));
	    
	    // Load admin scripts
	    add_action('admin_enqueue_scripts', array($this, 'spreebie_barter_payments_donations_admin_script'));
	    
	    // Load admin scripts
	    add_action('wp_ajax_radio_spreebie_barter_payments_donations_ajax_add_term', array($this, 'spreebie_barter_payments_donations_ajax_add_term'));
	}
	
	
	/**
	 * Register post type and taxonomy
	 *
	 * Registers a custom post type called 'sb_p_and_d'
	 * and a custom taxonomy called sb_p_and_d_category
	 *
	 * @param	none
	 * @return	none
	*/
	
	public static function spreebie_barter_payments_donations_post_type_and_taxonomy_init() {
	    $spreebie_barter_payments_donations_type_args = array(
			'labels' => array(
				'name' => _x('Spreebie Barter Payments and Donations', 'post type general name'),
				'singular_name' => _x('SB Payment or Donation', 'post type singular name'),
				'add_new' => _x('Add New SB Payment or Donation', 'image'),
				'add_new_item' => __('Add SB Payment or Donation'),
				'edit_item' => __('Edit SB Payment or Donation'),
				'new_item' => __('Add New SB Payment or Donation'),
				'all_items' => __('View SB Payments and Donations'),
				'view_item' => __('View SB Payment or Donation'),
				'search_items' => __('Search SB Payments and Donations'),
				'not_found' =>  __('No SB Payments and Donations found'),
				'not_found_in_trash' => __('No SB Payments and Donations found in Trash'), 
				'parent_item_colon' => '',
				'menu_name' => 'SB Payments and Donations'
			),
			'public' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'capabilities' => array(
				'create_posts' => false, // Removes support for the "Add New" functionality
			),
			'has_archive' => true, 
			'hierarchical' => false,
			'map_meta_cap' => true,
			'menu_position' => null,
			'supports' => array(
				'thumbnail',
				'custom-fields'
			)
	    );
	    register_post_type('sb_p_and_d', $spreebie_barter_payments_donations_type_args);
	    
	    $spreebie_barter_payments_donations_category_args = array(
			'labels' => array(
				'name' => _x( 'Spreebie Barter Payments and Donations Categories', 'taxonomy general name' ),
				'singular_name' => _x( 'SB Payments and Donations Category', 'taxonomy singular name' ),
				'search_items' =>  __( 'SB Payments and Donations Categories' ),
				'all_items' => __( 'All SB Payments and Donations Categories' ),
				'parent_item' => __( 'Parent SB Payments and Donations Category' ),
				'parent_item_colon' => __( 'Parent SB Payments and Donations Category:' ),
				'edit_item' => __( 'Edit SB Payments and Donations Category' ), 
				'update_item' => __( 'Update SB Payments and Donations Category' ),
				'add_new_item' => __( 'Add New SB P & D Category' ),
				'new_item_name' => __( 'New SB Payments and Donations Category' ),
				'menu_name' => __( 'SB Payments and Donations Categories' ),
			),   
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'sb_p_and_d_category'
			)
	    );
	     
	    register_taxonomy('sb_p_and_d_category', array('sb_p_and_d'), $spreebie_barter_payments_donations_category_args);
	    
	    // Default categories 
	    $spreebie_barter_payments_donations_default_categories = array('Payment', 'Donation');
	     
	    foreach($spreebie_barter_payments_donations_default_categories as $cat) {
	     
		if(!term_exists($cat, 'sb_p_and_d_category')) wp_insert_term($cat, 'sb_p_and_d_category');
	       
		}

		$spreebie_barter_error_stage_args = array(
			'labels' => array(
				'name' => _x( 'SB Payments and Donations Error Stages', 'taxonomy general name' ),
				'singular_name' => _x( 'SB Payments and Donations Error Stage', 'taxonomy singular name' ),
				'search_items' =>  __( 'SB Payments and Donations Error Stages' ),
				'all_items' => __( 'All SB Payments and Donations Error Stages' ),
				'parent_item' => __( 'Parent SB Payments and Donations Error Stage' ),
				'parent_item_colon' => __( 'Parent SB Payments and Donations Error Stage:' ),
				'edit_item' => __( 'Edit SB Payments and Donations Error Stage' ), 
				'update_item' => __( 'Update SB Payments and Donations Error Stage' ),
				'add_new_item' => __( 'Add New SB Payments and Donations Error Stage' ),
				'new_item_name' => __( 'New SB Payments and Donations Error Stage' ),
				'menu_name' => __( 'SB Payments and Donations Error Stages' ),
			),   
			'hierarchical' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'spreebie_barter_error_stage'
			)
		);
			
		register_taxonomy('spreebie_barter_error_stage', array('sb_p_and_d'), $spreebie_barter_error_stage_args);

		// Error stages
		$spreebie_barter_error_stages = array('Installation Stage', 'Settings Stage', 'General Usage Stage');
			
		foreach($spreebie_barter_error_stages as $stage) {
			
		if(!term_exists($stage, 'spreebie_barter_error_stage')) wp_insert_term($stage, 'spreebie_barter_error_stage');
			
		}
	}
	
	
	/**
	 * Adds metabaxes to the custom post type backend
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_video_metaboxes() {
	    add_action('add_meta_boxes', array($this, 'spreebie_barter_payments_donations_add_video_metaboxes'));
	}
	
	
	/**
	 * Metaboxes callback
	 *
	 * Callback implements the catergory and caption metaboxex.
	 * Through this, a user will be able to see the caption,
	 * and other metadata
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_add_video_metaboxes() {       
	    add_meta_box('spreebie_barter_payments_donations_meta_box_caption', 'Caption', 'spreebie_barter_payments_donations_caption', 'sb_p_and_d');
	    
	    function spreebie_barter_payments_donations_caption($post) {
	    ?>
		<p>
		    <textarea class="widefat" rows="1" cols="40" name="spreebie_barter_payments_donations_meta_box_caption" id="spreebie_barter_payments_donations_meta_box_caption"><?php echo esc_html(get_the_title($post->ID)); ?></textarea>
		</p>
	    <?php
	    }
	}
	
	
	/**
	 * Removes the existing taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_remove_meta_box(){  
	    remove_meta_box($this->spreebie_barter_payments_donations_taxonomy_metabox_id, $this->spreebie_barter_payments_donations_post_type, 'normal');  
	}
	
	
	/**
	 * Add radio buttons-based metabox
	 *
	 * Add the new custom taxonomy metabox that is based
	 * on radio buttons instead of checkboxes. In this way,
	 * a video comment can be associated with only one term.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_add_taxonomy_meta_box() {  
	    add_meta_box(
		'spreebie_barter_payments_donations_meta_box_categories',
		'SB P & D Categories',
		array($this, 'spreebie_barter_payments_donations_taxonomy_metabox'),
		$this->spreebie_barter_payments_donations_post_type,
		'side',
		'core'
	    );  
	}
	
	/**
	 * Radio buttons-based metaxbox callback
	 *
	 * Callback implements the new taxonomy metabox
	 * based on radio buttons
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function spreebie_barter_payments_donations_taxonomy_metabox($post) {
	    //Get taxonomy and terms  
	    $taxonomy = $this->spreebie_barter_payments_donations_taxonomy;
	  
	    //Set up the taxonomy object and get terms  
	    $tax = get_taxonomy($taxonomy);
	    $terms = get_terms($taxonomy, array('hide_empty' => 0));
	  
	    //Name of the form
	    $name = 'tax_input[' . $taxonomy . ']';
	  
	    //Get current and popular terms  
	    $popular = get_terms( $taxonomy, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );  
	    $postterms = get_the_terms($post->ID, $taxonomy);  
	    $current = ($postterms ? array_pop($postterms) : false);  
	    $current = ($current ? $current->term_id : 0);  
	    ?>  
	  
		<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="categorydiv">
		    <!-- Display tabs-->
		    <ul id="<?php echo esc_attr($taxonomy); ?>-tabs" class="category-tabs">
			<li class="tabs"><a href="#<?php echo esc_attr($taxonomy); ?>-all" tabindex="3"><?php echo esc_html($tax->labels->all_items); ?></a></li>
			<li class="hide-if-no-js"><a href="#<?php echo esc_attr($taxonomy); ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
		    </ul>
    
		    <!-- Display taxonomy terms -->
		    <div id="<?php echo esc_attr($taxonomy); ?>-all" class="tabs-panel">
			<ul id="<?php echo esc_attr($taxonomy); ?>checklist" class="list:<?php echo esc_attr($taxonomy)?> categorychecklist form-no-clear">
			<?php
			    foreach($terms as $term) {
				$id = $taxonomy.'-'.$term->term_id;
				$value = (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			     }
			?>
			</ul>
		    </div>
    
		    <!-- Display popular taxonomy terms -->
		    <div id="<?php echo esc_attr($taxonomy); ?>-pop" class="tabs-panel" style="display: none;">
			<ul id="<?php echo esc_attr($taxonomy); ?>checklist-pop" class="categorychecklist form-no-clear" >
			<?php
			    foreach($popular as $term){
				$id = 'popular-'.$taxonomy.'-'.$term->term_id;
				$value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$term->term_id}'" : "value='{$term->term_slug}'");
				echo "<li id='$id'><label class='selectit'>";
				echo "<input type='radio' id='in-$id'".checked($current,$term->term_id,false)." {$value} />$term->name<br />";
				echo "</label></li>";
			    }
			?>
			</ul>
		    </div>
    
		    <p id="<?php echo esc_attr($taxonomy); ?>-add" class="">
			<label class="screen-reader-text" for="new<?php echo esc_attr($taxonomy); ?>"><?php echo esc_html($tax->labels->add_new_item); ?></label>
			<input type="text" name="new<?php echo esc_attr($taxonomy); ?>" id="new<?php echo esc_attr($taxonomy); ?>" class="form-required form-input-tip" value="<?php echo $tax->labels->new_item_name; ?>" tabindex="3" aria-required="true"/>
			<input type="button" id="" class="radio-tax-add button" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
			<?php wp_nonce_field( 'radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag', false ); ?>
		    </p>
		</div>
	    <?php  
	}
	
	
	/**
	 * Add screenshot metabox
	 *
	 * Add the screenshot metabox that is based.
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_add_screenshot_meta_box() {  
	    add_meta_box(
		'spreebie_barter_payments_donations_meta_box_screenshot',
		'Screenshot',
		array($this, 'spreebie_barter_payments_donations_screenshot_metabox'),
		$this->spreebie_barter_payments_donations_post_type,
		'side',
		'core'
	    );
	}
	
	/**
	 * Screenshot metaxbox callback
	 *
	 * Callback implements the screenshot metabox
	 *
	 * @param	$post: the video concomp post variable
	 * @return	none
	*/ 
	public function spreebie_barter_payments_donations_screenshot_metabox($post) {
	    $spreebie_barter_payments_donations_screenshot = get_attached_media('image', $post->ID);
	    foreach ($spreebie_barter_payments_donations_screenshot as $screenshot) {
	?>
		<p>
		    <label for="spreebie_barter_payments_donations_meta_box_screenshot"> URL: </label>
		    <input type="text" name="spreebie_barter_payments_donations_meta_box_screenshot" id="spreebie_barter_payments_donations_meta_box_screenshot" value="<?php echo wp_get_attachment_url($screenshot->ID, 'full'); ?>" />
		    <a href="<?php echo wp_get_attachment_url($screenshot->ID, 'full'); ?>" target="_blank">
			<input type="button" id="spreebie_barter_payments_donations_screenshot_view_button" class="action" name="spreebie_barter_payments_donations_screenshot_view_button" value="View"/>
		    </a>               
		</p>
	<?php
		break;
	    } 
	}
	
	/**
	 * Add scripts to make the new radio button based
	 * metabox work
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_admin_script() {  
	    wp_register_script('radiotax', plugins_url('../js/radiotax.js', __FILE__), array('jquery'), null, true ); // We specify true here to tell WordPress this script needs to be loaded in the footer  
	    wp_localize_script('radiotax', 'radio_tax', array('slug'=>$this->spreebie_barter_payments_donations_taxonomy));
	    wp_enqueue_script('radiotax');  
	}
	
	
	/**
	 * Add terms to the new radio button based custom
	 * taxonomy metabox
	 *
	 * @param	none
	 * @return	none
	*/
	
	public function spreebie_barter_payments_donations_ajax_add_term() {
    
	    $taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : '';
	    $term = !empty($_POST['term']) ? $_POST['term'] : '';
	    $tax = get_taxonomy($taxonomy);
    
	    check_ajax_referer('radio-tax-add-'.$taxonomy, '_wpnonce_radio-add-tag');
    
	    if(!$tax || empty($term))
		    exit();
    
	    if ( !current_user_can( $tax->cap->edit_terms ) )
		    die('-1');
    
	    $tag = wp_insert_term($term, $taxonomy);
    
	    if ( !$tag || is_wp_error($tag) || (!$tag = get_term( $tag['term_id'], $taxonomy )) ) {
		    //TODO Error handling
		    exit();
	    }
    
	    $id = $taxonomy.'-'.$tag->term_id;
	    $name = 'tax_input[' . $taxonomy . ']';
	    $value= (is_taxonomy_hierarchical($taxonomy) ? "value='{$tag->term_id}'" : "value='{$term->tag_slug}'");
    
	    $html ='<li id="'.$id.'"><label class="selectit"><input type="radio" id="in-'.$id.'" name="'.$name.'" '.$value.' />'. $tag->name.'</label></li>';
    
	    echo json_encode(array('term'=>$tag->term_id,'html'=>$html));
	    exit();
	}
    }

endif;
?>