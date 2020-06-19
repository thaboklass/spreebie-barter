<?php

/*------------------------------------------------------------------*
 * The 'spreebie_barter_admin' class
 * @author Thabo David Nyakallo Klass
/*------------------------------------------------------------------*/

if (!class_exists('spreebie_barter_admin')) :

class spreebie_barter_admin {
    // The AJAX data
    public $spreebie_barter_admin_ajax_data;

       
    /**
    * The 'spreebie_barter_admin' constructor
    * 
    */
    
    public function __construct() {
        // Nothing serious happening here

        // add Spreebie Barter options
        $this->spreebie_barter_add_options();

        // This loads admin scripts
        add_action('admin_enqueue_scripts', array($this, 'spreebie_barter_load_admin_scripts'));

        // AJAX dependency
        add_action('wp_ajax_spreebie_barter_send_receipt_via_email_results', array($this, 'spreebie_barter_send_receipt_via_email_ajax'));
    }
    
    
    /*------------------------------------------------------------------*
     * Menus
    /*------------------------------------------------------------------*/
    
    /**
     * Adds 'Spreebie Barter' menu item
     *
     * Adds the 'Settings' menu titled 'Ethereum Settings'
     * as a top level menu item in the dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function spreebie_barter_add_menu_page() {
        
        // Introduces a top-level menu page
        add_menu_page(
            'Spreebie Barter Configuration',                                   // The text that is displayed in the browser title bar
            __('Spreebie Barter'),                          // The text that is used for the top-level menu
            'manage_options',                                           // The user capability to access this menu
            'spreebie-barter-configuration',                            // The name of the menu slug that accesses this menu item
            array($this, 'spreebie_barter_configuration_display'),      // The name of the function used to display the page content
            '');
    } // end of function spreebie_barter_add_menu_page
    
    
    
    /*------------------------------------------------------------------*
     * Sections, Settings and Fields
    /*------------------------------------------------------------------*/
    
    /**
     * Register section, fields and page
     *
     * Registers a new settings section and settings fields on the
     * 'Spreebie Barter' page of the WordPress dashboard.
     *
     * @param	none
     * @return	none
    */
    
    public function spreebie_barter_initialize_options() {
        // Introduce an new section that will be rendered on the new
        // settings page.  This section will be populated with settings
        // that will give the 'Spreebie Barter' plugin its firebase
        // configuration options.
        add_settings_section(
            'spreebie_barter_ethereum_settings_section',                            // The ID to use for this section
            'Ethererum Settings',                                           // The title of this section that is rendered to the screen
            array($this, 'spreebie_barter_ethereum_settings_section_display'),      // The function that is used to render the options for this section
            'spreebie-barter-configuration'                                     // The ID of the page on which the section is rendered
        );

        // Defines the settings field 'Your Ethereum address'
        // which is a the Ethereum address of the website owner
        add_settings_field(
            'spreebie_barter_ethereum_address',                           // The ID of the setting field
            'Your Ethereum address:',                                  // The text to be displayed
            array($this, 'spreebie_barter_ethereum_address_display'),     // The function used to render the setting field
            'spreebie-barter-configuration',            // The ID of the page on which the setting field is rendered
            'spreebie_barter_ethereum_settings_section'     // The section to which the setting field belongs
        );

        // Register the 'spreebie_barter_ethereum_address'
        // with the 'Ethereum Settings' section
        register_setting(
            'spreebie_barter_settings',        // The section holding the settings fields
            'spreebie_barter_ethereum_address'                                // The name of the settings field to register
		);
		
		// Defines the settings field 'Your widget text'
        // which is a the initial text displayed on the
        // front end widget
        add_settings_field(
            'spreebie_barter_widget_text',                           // The ID of the setting field
            'Your widget text:',                                  // The text to be displayed
            array($this, 'spreebie_barter_widget_text_display'),     // The function used to render the setting field
            'spreebie-barter-configuration',            // The ID of the page on which the setting field is rendered
            'spreebie_barter_ethereum_settings_section'     // The section to which the setting field belongs
        );

        // Register the 'spreebie_barter_widget_text'
        // with the 'Ethereum Settings' section
        register_setting(
            'spreebie_barter_settings',        // The section holding the settings fields
            'spreebie_barter_widget_text'                                // The name of the settings field to register
        );

        // After the user interface elements have been rendered,
        // upload the video data if that has been requested.
        $this->spreebie_barter_create_payment();
		$this->spreebie_barter_create_donation();
		
		/// Send mail if there is any mail post data
        $this->spreebie_barter_send_error_email();
    } // end of function spreebie_barter_initialize_options
    
    
    
    /*------------------------------------------------------------------*
     * Callbacks
    /*------------------------------------------------------------------*/
    
    /**
     * This function is used to render all of the page content
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_barter_configuration_display() {
    ?>
        <div class="wrap" id="spreebie_barter_main_content">
            <?php
                if(isset($_POST['spreebie_barter_payment_form_submitted'])) {
            ?>
            <div id="spreebie_barter_payment_complete">
                <p>Your payment method was successfully created! Go to "SB Payments and Donations->View..." to view it and get the payment token.</p>
            </div>
            <?php
                }
                if(isset($_POST['spreebie_barter_donation_form_submitted'])) {
            ?>
            <div id="spreebie_barter_donation_complete">
                <p>Your donation method was successfully created! Go to "SB Payments and Donations->View..." to view it and get the payment token.</p>
            </div>
            <?php
                }
            ?>
            <div id="icon-options-general" class="icon32"></div>
            <h2>Spreebie Barter - Ethereum Payments and Donations</h2>
            <?php
            if(isset($_GET[ 'tab' ])) {
                $active_tab = $_GET['tab'];
            } else if($active_tab == 'settings') {
                $active_tab = 'settings';
            } else if($active_tab == 'donations') {
                $active_tab = 'donations';
            } else if($active_tab == 'support') {
                $active_tab = 'support';
            } else {
                $active_tab = 'payments';
            } // end if/else
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=spreebie-barter-configuration&tab=payments" class="nav-tab <?php echo $active_tab == 'payments' ? 'nav-tab-active' : ''; ?>">Payments</a>
                <a href="?page=spreebie-barter-configuration&tab=donations" class="nav-tab <?php echo $active_tab == 'donations' ? 'nav-tab-active' : ''; ?>">Donations</a>
                <a href="?page=spreebie-barter-configuration&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
                <a href="?page=spreebie-barter-configuration&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            </h2>
            <?php 
            if($active_tab == 'payments') {
            ?>
                <h3>Create A New Payment</h3>
                This is helps you create a new payment invoice - when the process is complete, click create and a unique token will be created. This token will be used by your customer to pay you via the widget on your site.
                <form id="spreebie_barter_payment_form" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('spreebie_barter_payment_form', 'spreebie_barter_payment_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Payment title:</th>
                            <td>
                                <input type="text" name="spreebie_barter_payment_title" id="spreebie_barter_payment_title" class="regular-text code"><br>
                                <p>Enter the payment's title.</p>
                                <p>This is the name of the payment that will be display in 'Donations and Payments'</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Customer name:</th>
                            <td>
                                <input type="text" name="spreebie_barter_customer_name" id="spreebie_barter_customer_name" class="regular-text code"><br>
                                <p>Enter the customer's name.</p>
                                <p>This is the name the name of the person who will be paying you.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Customer email:</th>
                            <td>
                                <input type="text" name="spreebie_barter_customer_email" id="spreebie_barter_customer_email" class="regular-text code"><br>
                                <p>Enter the customer's email address.</p>
                                <p>This serves as a unique identifier for this particular customer.</p>
                                <p>You will then send the generated payment token to the user.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Currency:</th>
                            <td>
                                <div class="ui-widget">
                                    <select id="spreebie_barter_payment_currency" name="spreebie_barter_payment_currency">
                                        <option value="Pick a currency">Pick a currency</option>
                                        <?php
                                        foreach ($this->spreebie_barter_get_available_currencies() as $currency_key => $currency_obj) {
                                            $option = '<option value="' . $currency_key . '"';
                                            $option .= '>';
                                            $option .= $currency_obj['name'] . ' (' . $currency_obj['code'] . ')';
                                            $option .= '</option>';
                                            echo $option;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
			            </tr>
                        <tr>
                            <th scope="row">Payment amount:</th>
                            <td>
                                <input type="number" name="spreebie_barter_payment_amount" id="spreebie_barter_payment_amount"><br>
                                <p>Enter the payment amount.</p>
                                <p>Make user to enter numbers with a maximum of two decimal places.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Payment description:</th>
                            <td>
                                <textarea name="spreebie_barter_payment_description" id="spreebie_barter_payment_description" rows="4" cols="50" form="spreebie_barter_payment_form"></textarea>
                                <p>Give a general description detailing everything pertinent for the</p>
                                <p>customer and for yourself.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="spreebie_barter_payment_submit" value="Create Payment" id="spreebie_barter_payment_submit">
                </form>
                <div id="spreebie_barter_incomplete_dialog" title="Please fill all required fields!">
                    <p>It appears that you may have not filled one or more fields.  Please make sure that all fields are filled. Also make sure that the fields are filled correctly.</p>
                </div>
                <?php
            ?>
            <?php
            } else if($active_tab == 'donations') {
                ?>
                <h3>Create A New Donation Method</h3>
                This is helps you create a new donation method - when the process is complete, click create and a unique token will be created. This token will be used by your customer to donate you via the widget on your site.
                <form id="spreebie_barter_donation_form" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('spreebie_barter_donation_form', 'spreebie_barter_donation_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Donation title:</th>
                            <td>
                                <input type="text" name="spreebie_barter_donation_title" id="spreebie_barter_donation_title" class="regular-text code"><br>
                                <p>Enter the donation's title.</p>
                                <p>This is the name of the donation that will be display in 'Donations and Payments'</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Currency:</th>
                            <td>
                                <div class="ui-widget">
                                    <select id="spreebie_barter_donation_currency" name="spreebie_barter_donation_currency">
                                        <option value="Pick a currency">Pick a currency</option>
                                        <?php
                                        foreach ($this->spreebie_barter_get_available_currencies() as $currency_key => $currency_obj) {
                                            $option = '<option value="' . $currency_key . '"';
                                            $option .= '>';
                                            $option .= $currency_obj['name'] . ' (' . $currency_obj['code'] . ')';
                                            $option .= '</option>';
                                            echo $option;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
			            </tr>
                        <tr>
                            <th scope="row">Donation description:</th>
                            <td>
                                <textarea name="spreebie_barter_donation_description" id="spreebie_barter_donation_description" rows="4" cols="50" form="spreebie_barter_donation_form"></textarea>
                                <p>Give a general description detailing everything pertinent for the</p>
                                <p>customer and for yourself.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="spreebie_barter_donation_submit" value="Create Donation" id="spreebie_barter_donation_submit">
                </form>
                <div id="spreebie_barter_incomplete_dialog" title="Please fill all required fields!">
                    <p>It appears that you may have not filled one or more fields.  Please make sure that all fields are filled. Also make sure that the fields are filled correctly.</p>
                </div>
                <?php
            ?>
            <?php
            } else if($active_tab == 'support') {
                ?>
                <?php
                    if(isset($_POST['spreebie_barter_email_form_submitted']) && isset($_POST['spreebie_barter_error_from_email'])
                        && !empty($_POST['spreebie_barter_error_from_email']) && isset($_POST['spreebie_barter_error_description'])
                        && !empty($_POST['spreebie_barter_error_description'])) {

                        if(is_email($_POST['spreebie_barter_error_from_email'])) {
                ?>
                            <div id="spreebie_barter_upload_complete">
                                <p>Your support request has been sent. You will hear from a Spreebie representative shortly.</p>
                            </div>
                <?php
                        } else {
                ?>
                            <div id="spreebie_barter_email_invalid">
                                <p>The email you entered was invalid. Please try again.</p>
                            </div>
                <?php
                        }
                    }
                ?>
                <h3>Direct Support</h3>
				Are you having trouble with your Ethereum address setup? Are you encountering any problems related to using this plugin? Please reach out to us below:
				<p><b>Before you continue, please download the manual by clicking here: <a href="https://s3.amazonaws.com/spreebiebarter/Spreebie_Barter_Quick_Start_Guide.zip">DOWNLOAD MANUAL</a></b></p>
                <form id="spreebie_barter_email_form" action="" method="post" enctype="multipart/form-data">
                    <?php echo wp_nonce_field('spreebie_barter_email_form', 'spreebie_barter_email_form_submitted'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enter your email:</th>
                            <td>
                                <input type="text" name="spreebie_barter_error_from_email" id="spreebie_barter_error_from_email"><br>
								<p>Enter the email you want the Spreebie support team to reach you on.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Select the error stage:</th>
                            <td>
                                <?php $this->spreebie_barter_error_stages_display(); ?>
                                <p>This is the stage at which the error first started appearing.</p>
                                <p><b>Installation Stage:</b> This is when an error appears during installation.</p>
                                <p><b>Settings Stage:</b> This is when an error appears while configuring your settings.</p>
                                <p><b>General Usage Stage:</b> This is when an error appears while using the plugin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Describe the problem:</th>
                            <td>
                                <textarea rows="4" cols="70" name="spreebie_barter_error_description" id="spreebie_barter_error_description"></textarea><br>
                                <p>Describe the exact nature of the problem you are experiencing in detail.</p>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" class="button button-primary" name="spreebie_barter_submit" value="Send Message" id="spreebie_barter_submit">
                </form>
                <div id="spreebie_barter_support_fields_not_filled">
                    <p>It appears that you may have not filled one or more fields.  Please make sure the email and error description fields are filled. Also make sure that fields are filled correctly.</p>
                </div>
                <?php
            ?>
			<?php
			} else {
            ?>
            <form method="post" id="spreebie_barter_save_changes_form" action="options.php">
            <?php
                // Outputs pertinent nonces, actions and options for
                // the section
                settings_fields('spreebie_barter_settings');
                
                // Renders the setting sections added to the page
                // 'Configuration Settings'
                do_settings_sections('spreebie-barter-configuration');
                
                // Renders a submit button that saves all of the options
                // pertaining to the settings fields
                submit_button();
            ?>
            </form>
            <div id="spreebie_barter_support_fields_not_filled" title="Please fill all fields!">
                <p>It appears that one of your inputs contains quotes. Please remove the quotes and save again.</p>
            </div>
			<h3>DONATE - Do you find Spreebie Barter useful?</h3>
			<p>If you have found Spreebie Barter useful, we are incredibly pleased and appreciative for your curiousity and openness in trying something new and different. To us, Spreebie Barter was a labour of love – love for tech, love for the future and love for humanity. 
                We believe that each one of us has a latent talent and vision that can benefit humanity if harnessed. Despite being a labour of love, it takes time to create and manage Spreebie Barter.</p>
            <p><b>So, if you did find Spreebie Barter beneficial, please donate what you can to keep the project working and efficient. You can donate by filling the text fields and clicking the button below. If you wish to danate anonymously, do not fill the name and email fields.</b></p>
			<form id="spreebie_barter_support_donation_form" action="" method="post" enctype="multipart/form-data">
				<p><b>Please fill out the fields below and click the donate button: &nbsp;&nbsp;&nbsp;&nbsp; </b><input type="submit" class="button button-primary" name="spreebie_barter_submit" value="DONATE" id="spreebie_barter_submit"></p>
				<div id="spreebie_barter_donation_fields_not_filled">
					<p>It appears that you may have not entered the donation amount.  Please make sure the donation amount field is filled and then donate.</p>
				</div>
                <div id="spreebie_barter_donation_information" title="Your donation information">
                    <p>This is your donation information.</p>
                </div>
				<?php echo wp_nonce_field('spreebie_barter_support_donation_form', 'spreebie_barter_support_donation_form_submitted'); ?>
				<table class="form-table">
                    <tr>
						<th scope="row">Enter your donation amount (US$):</th>
						<td>
							<input type="number" name="spreebie_barter_donation_amount" id="spreebie_barter_donation_amount"><br>
							<p>Enter the donation amount.</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Enter your full name:</th>
						<td>
							<input type="text" name="spreebie_barter_donation_full_name" id="spreebie_barter_donation_full_name"><br>
							<p>Enter the full name you wanted listed with the donation.</p>
                            <p>Do not fill this if you wish to donate anonymously.</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Enter your email:</th>
						<td>
							<input type="text" name="spreebie_barter_donation_email" id="spreebie_barter_donation_email"><br>
							<p>Enter the email you wanted listed with the donation.</p>
                            <p>Do not fill this if you wish to donate anonymously.</p>
						</td>
					</tr>
				</table>
            </form>
            <?php
            }
			?>
        </div>
    <?php
    }
    
    
    /**
     * Inline 'Ethereum Settings' description
     *
     * Displays an explanation of the role of the 'Configuration
     * Settings' section.
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_barter_ethereum_settings_section_display() {
        echo esc_html("These are your Ethereum settings. These will be used to help you recieve your ether.");
    }


    /**
     * Renders 'Widget Text'
     *
     * Renders the input field for the 'Widget Text'
     * setting in the 'Ethereum Settings'
     * section.
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_barter_widget_text_display() {
	?>
		<textarea name="spreebie_barter_widget_text" id="spreebie_barter_widget_text" rows="4" cols="50" form="spreebie_barter_save_changes_form"><?php echo esc_html(get_option('spreebie_barter_widget_text')); ?></textarea>
        <p>Your widget's text. Enter the text with <b>no quotes</b>.</p>
        <p>This is the initial text that will appear in the widget's body.</p>
    <?php    
	} // end of spreebie_barter_widget_text_display
	

	/**
     * Renders 'Ethereum Address'
     *
     * Renders the input field for the 'Ethereum Address'
     * setting in the 'Ethereum Settings'
     * section.
     *
     * @param	none
     * @return	none
     */
    
    public function spreebie_barter_ethereum_address_display() {
		?>
			<input type="text" name="spreebie_barter_ethereum_address" id="spreebie_barter_ethereum_address" value="<?php echo esc_html(get_option('spreebie_barter_ethereum_address')); ?>" class="regular-text code" />
			<p>Your Ethereum address. Enter the Ethereum address with <b>no quotes</b>.</p>
			<p>This is the address that payers will use to send money to you.</p>
		<?php    
	} // end of spreebie_barter_ethereum_address_display
    
    
    // Error stage drop-down display
	public function spreebie_barter_error_stages_display() {
	?>
	</label><?php echo $this->spreebie_barter_get_error_stage_dropdown('spreebie_barter_error_stage', 0); ?><br/>
	<?php
	}
    
    
	/**
	* Taxonomy drop-down list on front-end widget
	*
	* @param $taxonomy     The taxanomy to be used to retrieve data.
	* @param $selected     Which item is to be selected on the list   
	*/
	
	function spreebie_barter_get_error_stage_dropdown($taxonomy, $selected){
		return wp_dropdown_categories(array('taxonomy' => $taxonomy, 'name' => 'spreebie_barter_error_stage', 'selected' => $selected, 'hide_empty' => 0, 'echo' => 0));
	}


    /**
    * Upload the content provided by the administor to create the
    * payment
    *
    * @param    none
    * @return   none
    */

    function spreebie_barter_create_payment() {
        // If the $_POST data and nonce are set, upload the data
        // within the payment inputs
        if(isset($_POST['spreebie_barter_payment_form_submitted']) && wp_verify_nonce($_POST['spreebie_barter_payment_form_submitted'], 'spreebie_barter_payment_form')) {
            $spreebie_barter_visitor_id = get_current_user_id();
            
            $sanitized_spreebie_barter_payment_title = sanitize_text_field($_POST['spreebie_barter_payment_title']);
            $payment_data = array(
                'post_title' => $sanitized_spreebie_barter_payment_title,
                'post_status' => 'publish',
                'post_author' => $spreebie_barter_visitor_id,
                'post_type' => 'sb_p_and_d'    
            );
                
            if ($spreebie_barter_post_id = wp_insert_post($payment_data)) {
                $payment_token = get_bloginfo('name') . "_" . time() . "_" . mt_rand();

                // The custom fields
                $sanitized_spreebie_barter_customer_name = sanitize_text_field($_POST['spreebie_barter_customer_name']);
                update_post_meta($spreebie_barter_post_id, 'customer_name', $sanitized_spreebie_barter_customer_name);
                $sanitized_spreebie_barter_customer_email = sanitize_email($_POST['spreebie_barter_customer_email']);
                update_post_meta($spreebie_barter_post_id, 'customer_email', $sanitized_spreebie_barter_customer_email);
                $sanitized_spreebie_barter_payment_currency = sanitize_text_field($_POST['spreebie_barter_payment_currency']);
                update_post_meta($spreebie_barter_post_id, 'payment_currency', $sanitized_spreebie_barter_payment_currency);
                $sanitized_spreebie_barter_payment_amount = sanitize_text_field($_POST['spreebie_barter_payment_amount']);
                update_post_meta($spreebie_barter_post_id, 'payment_amount', $sanitized_spreebie_barter_payment_amount);
                $sanitized_spreebie_barter_payment_description = sanitize_text_field($_POST['spreebie_barter_payment_description']);
                update_post_meta($spreebie_barter_post_id, 'payment_description', $sanitized_spreebie_barter_payment_description);
                update_post_meta($spreebie_barter_post_id, 'payment_token', $payment_token);
                update_post_meta($spreebie_barter_post_id, 'payment_settled', "No");
                
                // This refreshes the custom post type and taxonomy
                spreebie_barter_payments_donations::spreebie_barter_payments_donations_post_type_and_taxonomy_init();
                
                // This adds one term out of the taxonomy (one renumeration type)
                // to the paymentt. This is data viewed on the
                // backend by an administrator
                $payment_term_object = get_term_by('name', 'Payment', 'sb_p_and_d_category');
                $payment_term_id = $payment_term_object->term_id;
                $term_taxonomy_ids = wp_set_object_terms($spreebie_barter_post_id, $payment_term_id, 'sb_p_and_d_category');
                
                if (is_wp_error($term_taxonomy_ids)) {
                    echo esc_html('<p>WHOOPS: There was an error assigning a category to the comment</p>');
                    var_dump($term_taxonomy_ids);
                }
            }
        }
    }


    /**
    * Upload the content provided by the admnistrator to create the
    * donation
    *
    * @param    none
    * @return   none
    */

    function spreebie_barter_create_donation() {
        // If the $_POST data and nonce are set, upload the data
        // within the donation inputs
        if(isset($_POST['spreebie_barter_donation_form_submitted']) && wp_verify_nonce($_POST['spreebie_barter_donation_form_submitted'], 'spreebie_barter_donation_form')) {
            $spreebie_barter_visitor_id = get_current_user_id();
            
            $sanitized_spreebie_barter_donation_title = sanitize_text_field($_POST['spreebie_barter_donation_title']);
            $donation_data = array(
                'post_title' => $sanitized_spreebie_barter_donation_title,
                'post_status' => 'publish',
                'post_author' => $spreebie_barter_visitor_id,
                'post_type' => 'sb_p_and_d'    
            );
                
            if ($spreebie_barter_post_id = wp_insert_post($donation_data)) {
                $donation_token = get_bloginfo('name') . "_" . time() . "_" . mt_rand();

                $sanitized_spreebie_barter_donation_currency = sanitize_text_field($_POST['spreebie_barter_donation_currency']);
                update_post_meta($spreebie_barter_post_id, 'donation_currency', $sanitized_spreebie_barter_donation_currency);
                $sanitized_spreebie_barter_donation_description = sanitize_text_field($_POST['spreebie_barter_donation_description']);
                update_post_meta($spreebie_barter_post_id, 'donation_description', $sanitized_spreebie_barter_donation_description);
                update_post_meta($spreebie_barter_post_id, 'donation_token', $donation_token);
                
                // This refreshes the custom post type and taxonomy
                spreebie_barter_payments_donations::spreebie_barter_payments_donations_post_type_and_taxonomy_init();
                
                // This adds one term out of the taxonomy (one renumeration type)
                // to the donation. This is data viewed on the
                // backend by an administrator
                $donation_term_object = get_term_by('name', 'Donation', 'sb_p_and_d_category');
                $donation_term_id = $donation_term_object->term_id;
                $term_taxonomy_ids = wp_set_object_terms($spreebie_barter_post_id, $donation_term_id, 'sb_p_and_d_category');
                
                if (is_wp_error($term_taxonomy_ids)) {
                    echo esc_html('<p>WHOOPS: There was an error assigning a category to the comment</p>');
                    var_dump($term_taxonomy_ids);
                }
            }
        }
	}
	

	/**
    * Sends an error email to the Spreebie support team
    *
    * @param    none
    * @return   none
    */

    function spreebie_barter_send_error_email() {
        // If the $_POST data and nonce are set, upload the data
        // within the error inputs
        if(isset($_POST['spreebie_barter_email_form_submitted']) && wp_verify_nonce($_POST['spreebie_barter_email_form_submitted'], 'spreebie_barter_email_form')) {
            // the 'from' email
            
            if (isset($_POST['spreebie_barter_error_from_email']) && !empty($_POST['spreebie_barter_error_from_email'])
                && isset($_POST['spreebie_barter_error_description']) && !empty($_POST['spreebie_barter_error_description'])) {
                
                $spreebie_barter_from_email = $_POST['spreebie_barter_error_from_email'];
                $spreebie_barter_from_email = sanitize_email($spreebie_barter_from_email);

                // the to email
                $spreebie_barter_to_email = "thabo@openbeacon.biz";

                $validated_stage = intval((int)$_POST['spreebie_barter_error_stage']);
                $stage = $validated_stage;
                $term = get_term($stage);

                $stage_description = $term->name;

                $sanitized_spreebie_barter_error_description = sanitize_text_field($_POST['spreebie_barter_error_description']);
                $error_description = $sanitized_spreebie_barter_error_description;

                $stage_description = $stage_description . " (" . $spreebie_barter_from_email . ")";
                $stage_description = sanitize_text_field($stage_description);

                $message = $error_description . " - respond to: " . $spreebie_barter_from_email;
                wp_mail($spreebie_barter_to_email, $stage_description, $message);
            }
        }
	}
	

    /**
    * Add options for new activation
    *
    * This checks whether or not backend options that define the basic
    * functionality have been added and if not, they are added
    * with what have been determined as the most efficient defaults
    *
    * @param	none
    * @return	none
   */
   
    public function spreebie_barter_add_options() {
        if (!get_option('spreebie_barter_widget_text')) {
            add_option('spreebie_barter_widget_text', 'Hello,

             this is the Spreebie Barter widget. You can use it to make ethereum payments and donations from tokens using Metamask or any other Web3 plugin.
            
            First enter the payment or donation token in the field above, and then click GET. This will retrieve all the payment or donation information. If the details are correct, click the PAY or DONATE button below and Metamask will open. You can then process your payment.
            
            Thanks!');
            add_option('spreebie_barter_ethereum_address', '');
        }
    }


    /**
    * Load scripts
    *
    * Load all relevant styles and scripts - in this case we load just
    * one stylesheet and two javascript files
    *
    * @param	none
    * @return	none
    */
   
    public function spreebie_barter_load_admin_scripts() {
        wp_register_script('spreebie_barter_firebase', 'https://www.gstatic.com/firebasejs/4.8.2/firebase.js');
        wp_register_script('spreebie_barter_admin_ajax', plugins_url('../js/spreebie-barter-admin-ajax.js', __FILE__), array('jquery'), null, true);
        wp_register_style('spreebie_barter_admin_css', plugins_url('../css/admin.css', __FILE__));        

        // Pass the ajax data to the javascript
        $this->spreebie_barter_admin_ajax_data = array(
            'spreebie_barter_send_receipt_via_email_results_nonce' => wp_create_nonce('spreebie_barter_send_receipt_via_email_results')
        );

        // Pass the PHP parameters to Javascript by localizing them
        wp_localize_script('spreebie_barter_admin_ajax', 'spreebie_barter_admin_ajax_data', $this->spreebie_barter_admin_ajax_data);

        // Get the address of this WP installation's 'admin-ajax.php'
        $spreebie_barter_ajax_url = admin_url('admin-ajax.php');
            
        // The ajax_url parameter being passed to the ajax handler
        $spreebie_barter_ajax_params = array(
            'spreebie_barter_ajax_url' => $spreebie_barter_ajax_url
        );

        // Pass the PHP parameter to Javascript by localizing it
        wp_localize_script('spreebie_barter_admin_ajax', 'spreebie_barter_ajax_params', $spreebie_barter_ajax_params);
        
        wp_enqueue_script('spreebie_barter_firebase');
        wp_enqueue_style('spreebie_barter_admin_css');
        wp_enqueue_script('spreebie_barter_admin_ajax');
    }


    /**
    * Get the currency symbol
    *
    * Gets the currency symbol
    *
    * @param	none
    * @return	none
    */
    public function get_currency_symbol_for($currency) {
		if (isset($currency)) {
			$available_currencies = $this->spreebie_barter_get_available_currencies();

			if (isset($available_currencies) && array_key_exists($currency, $available_currencies)) {
				$currency_symbol = $available_currencies[$currency]['symbol'];
			} else {
				$currency_symbol = strtoupper($currency);
			}

			return $currency_symbol;
		}

		return null;
	}


    /**
    * Get major global currencies
    *
    * Load all the global currencies
    *
    * @param	none
    * @return	none
    */
	public function spreebie_barter_get_available_currencies() {
		return array(
			'aud' => array(
				'code'   => 'AUD',
				'name'   => 'Australian Dollar',
				'symbol' => '$'
			),
			'brl' => array(
				'code'   => 'BRL',
				'name'   => 'Brazilian Real',
				'symbol' => 'R$'
			),
			'cad' => array(
				'code'   => 'CAD',
				'name'   => 'Canadian Dollar',
				'symbol' => '$'
			),
			'eur' => array(
				'code'   => 'EUR',
				'name'   => 'Euro',
				'symbol' => '€'
			),
			'gbp' => array(
				'code'   => 'GBP',
				'name'   => 'British Pound',
				'symbol' => '£'
			),
			'jpy' => array(
				'code'   => 'JPY',
				'name'   => 'Japanese Yen',
				'symbol' => '¥'
			),
			'rub' => array(
				'code'   => 'RUB',
				'name'   => 'Russian Ruble',
				'symbol' => 'руб'
			),
			'usd' => array(
				'code'   => 'USD',
				'name'   => 'United States Dollar',
				'symbol' => '$'
            ),
            'krw' => array(
				'code'   => 'KRW',
				'name'   => 'South Korean Won',
				'symbol' => '₩'
			)
		);
    }
   
    

    /**
    * Sends a receipt via email to the donator after
    * a donation
    *
    * @param    none
    * @return   none
    */

    function spreebie_barter_send_receipt_via_email_ajax() {
        check_ajax_referer('spreebie_barter_send_receipt_via_email_results', 'spreebie_barter_send_receipt_via_email_results_nonce');

        // $_POST data containing the to email and name
        $sanitized_spreebie_barter_from_email = sanitize_email($_POST['spreebie_barter_donator_email']);
        $spreebie_barter_donator_email = $sanitized_spreebie_barter_from_email;

        $sanitized_spreebie_barter_donator_name = sanitize_text_field($_POST['spreebie_barter_donator_name']);
        $spreebie_barter_donator_name = $sanitized_spreebie_barter_donator_name;

        // Create a donation receipt
        $spreebie_barter_donation_receipt = "rec_" . get_bloginfo('name') . "_" . time() . "_" . mt_rand();

        // Create a subject text for the email
        $spreebie_barter_donation_subject_text = "Spreebie Barter donation - receipt no: " . $spreebie_barter_donation_receipt;

        $spreebie_barter_donation_message_text = "Hello " . $spreebie_barter_donator_name .
                                                "\n\nThank you very much! You have successfuly donated Spreebie Barter. We are truly grateful for your support. " .
                                                "This donation gives us the support to continue our work on Spreebie Barter and make it better. " . 
                                                "\n\nYour receipt number is: " . $spreebie_barter_donation_receipt .
                                                "\n\nSpreebie, Inc";
       
        // Send mail to user
        wp_mail($spreebie_barter_donator_email, $spreebie_barter_donation_subject_text, $spreebie_barter_donation_message_text);

        // Send mail to project lead
        wp_mail("thabo@openbeacon.biz", $spreebie_barter_donation_subject_text, $spreebie_barter_donation_message_text);

        // Echo out the receipt as a response to the ajax
        echo esc_html($spreebie_barter_donation_receipt);

        die();
	}
}

endif;
?>