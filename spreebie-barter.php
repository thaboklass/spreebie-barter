<?php
/*
 Plugin Name: Spreebie Barter
 Plugin URI: http://openbeacon.biz/?p=668
 Description: A simple and intuitive plugin that enables the creation and facilitation of Ethereum payments and donations for any WordPress site via Metamask. To get started: 1) Click the "Activate" link, 2) Go to Appearance->Widgets and drag and drop Spreebie Barter to where you would like it to appear in the widget area and 3) Click on any single page version of a posted article and the Spreebie Barter widget will be ready for use.
 Author: Spreebie
 Version: 1.0.1
 Author URI: http://getspreebie.com/
*/


/*------------------------------------------------------------------*
 * Constants and dependencies
/*------------------------------------------------------------------*/

/**
 * Define constants
 * 
 */

define( 'SPREEBIE_BARTER_VERSION', '1.0.1' );
define( 'SPREEBIE_BARTER_ROOT' , dirname( __FILE__ ) );
define( 'SPREEBIE_BARTER_FILE_PATH' , SPREEBIE_BARTER_ROOT . '/' . basename( __FILE__ ) );
define( 'SPREEBIE_BARTER_URL' , plugins_url( '/', __FILE__ ) );


/**
 * Include other plugin dependencies
 * 
 */

require SPREEBIE_BARTER_ROOT . '/includes/spreebie-barter-admin.php';
require SPREEBIE_BARTER_ROOT . '/includes/spreebie-barter-payments-donations.php';


/*------------------------------------------------------------------*
 * The 'spreebie_barter' class: a WordPress widget
 * @author Thabo David Nyakallo Klass
/*------------------------------------------------------------------*/

if(!class_exists('spreebie_barter')) :

    class spreebie_barter extends WP_Widget {
        // The website owner's ethereum address
        public $spreebie_barter_owner_etheruem_address;

        // A boolean for the footer position
        public $spreebie_barter_is_in_footer;

        // The AJAX data
        public $spreebie_barter_ajax_data;

        // The initial widget body text
        public $spreebie_barter_widget_text;
        
        
        /**
        * The 'spreebie_barter' constructor
        * 
        */
        
        function __construct() {
            // This adds backend options that define the basic
            // functionality
            $this->spreebie_barter_add_options();

            // Retrieve the website owner's website address
            $this->spreebie_barter_owner_etheruem_address = get_option('spreebie_barter_ethereum_address');

            // Initialize it to false
            $this->spreebie_barter_is_in_footer = false;

            // Retrieve the saved widget text
            $this->spreebie_barter_widget_text = get_option('spreebie_barter_widget_text');
            
            // Backend widget text and titles - this pertains
            // to how the Spreebie Barter widget appears in the backend
            $params = array('description' => "Enables payments and donations via Metamask");
            parent::__construct("spreebie_barter", $name = __("Spreebie Barter"), $params);
            
            // This loads all relevant external scripts
            add_action('wp_enqueue_scripts', array($this, 'spreebie_barter_load_scripts'));

            // AJAX dependencies: This loads ajax functions to send
            // javascript data to the PHP backend. This works for both
            // logged in users and for visitors who are not logged
            // in ('nopriv')
            add_action('wp_ajax_spreebie_barter_get_details_results', array($this, 'spreebie_barter_get_details_ajax'));
            add_action('wp_ajax_nopriv_spreebie_barter_get_details_results', array($this, 'spreebie_barter_get_details_ajax'));
            add_action('wp_ajax_spreebie_barter_update_payment_settled_results', array($this, 'spreebie_barter_update_payment_settled_ajax'));
            add_action('wp_ajax_nopriv_spreebie_barter_update_payment_settled_results', array($this, 'spreebie_barter_update_payment_settled_ajax'));
        }
        
        
        /**
        * Back-end widget form.
        *
        * @see WP_Widget::form()
        *
        * @param array $instance Previously saved values from database.
        */
        
        public function form($instance) {
            extract((array)$instance);
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title');?>">Title: </label>
                <input class="widefat"
                       id="<?php echo $this->get_field_id('title');?>"
                       name="<?php echo $this->get_field_name('title');?>"
                       value="<?php if(isset($title)) echo esc_attr($title); ?>" />
            </p>
            <?php
        }
        
        
        /**
        * Sanitize widget form values as they are saved.
        *
        * @see WP_Widget::update()
        *
        * @param array $new_instance Values just sent to be saved.
        * @param array $old_instance Previously saved values from database.
        *
        * @return array Updated safe values to be saved.
        */
        
        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['code'] = $new_instance['code'];
            return $instance;
        }
        
        
        /**
        * Front-end display of widget.
        *
        * @see WP_Widget::widget()
        *
        * @param array $args     Widget arguments.
        * @param array $instance Saved values from database.
        */
        
        public function widget($args, $instance) {
            // Check any variation of the footer position
            // because it the label varies depending on 
            // the theme.
            if (strpos($args['name'], 'Footer') !== false) {
                $this->spreebie_barter_is_in_footer = true;
            }

            // Check any variation of the footer position
            // because it the label varies depending on 
            // the theme - in this case, look for the word
            // 'Bottom'
            if (strpos($args['name'], 'Bottom') !== false) {
                $this->spreebie_barter_is_in_footer = true;
            }

            if (($args['name'] == 'Footer 1') || ($args['name'] == 'Footer 2') ||
                ($args['name'] == 'Content Bottom 1') || ($args['name'] == 'Content Bottom 2') ||
                ($args['name'] == 'Footer 3') || ($args['name'] == 'Footer Widgets') ||
                ($this->spreebie_barter_is_in_footer)) {
                // If the widget is positioned in the footer, load the footer style
                // to override the existing style
                wp_register_style('spreebie_barter_widget_css_footer', plugins_url('css/style_footer.css', __FILE__));                
                wp_enqueue_style('spreebie_barter_widget_css_footer');
            }
            
            // The widget is only displayed on a single page
            // of an article
            if (is_single()) {
                // Pass the ajax data to the javascript
                $this->spreebie_barter_ajax_data = array(
                    'spreebie_barter_owner_etheruem_address'=> $this->spreebie_barter_owner_etheruem_address,
                    'spreebie_barter_get_details_results_nonce' => wp_create_nonce('spreebie_barter_get_details_results'),
                    'spreebie_barter_update_payment_settled_results_nonce' => wp_create_nonce('spreebie_barter_update_payment_settled_results')
                );
                
                // Pass the PHP parameters to Javascript by localizing them
                wp_localize_script('spreebie_barter_widget_ajax', 'spreebie_barter_ajax_data', $this->spreebie_barter_ajax_data);
            ?>
            <!-- The HTML code for the front-end wdiget begins here -->
            <aside id="spreebie_barter_widget" class="widget">
                <h2 class="widget-title">
                <?php
                    if (($args['name'] == 'Footer 1') || ($args['name'] == 'Footer 2') ||
                    ($args['name'] == 'Content Bottom 1') || ($args['name'] == 'Content Bottom 2') ||
                    ($args['name'] == 'Footer 3') || ($args['name'] == 'Footer Widgets') ||
                    ($this->spreebie_barter_is_in_footer)) {
                        // Do nothing
                    } else {
                        ?>
                        <?php
                        echo esc_html($instance['title']);
                        ?>
                        <?php
                    }
                ?>
                </h2>
                <div id="spreebie_barter_payments_donations">
                    <div id="spreebie_barter_payments_donations_minimized">
                        <p class="spreebie_barter_button_open"><?php echo esc_html($instance['title']); ?></p>
                    </div>
                    <div id="spreebie_barter_payments_donations_maximized">
                        <?php
                            if (($args['name'] == 'Footer 1') || ($args['name'] == 'Footer 2')) {
                        ?>
                        <div class="spreebie_barter_button_close">X</div>
                        <?php
                            }
                        ?>
                        <header class="spreebie_barter_clearfix">
                            <form id="spreebie_barter_token_details" action="#" method="post">
                                    <input id="spreebie_barter_token_field" type="text" placeholder="Enter token here and 'GET'..." autofocus>
                                    <input id="spreebie_barter_get_details" name="" type="button" value="GET">
                            </form>
                        </header>
                        <div id="spreebie_barter_results" class="spreebie_barter_details_container">
                            <div class="spreebie_barter_details" id="spreebie_barter_details">
                                <p><?php echo esc_html($this->spreebie_barter_widget_text); ?></p>
                            </div>
                            <form action="#" method="post">
                                <fieldset>
                                    <input id="spreebie_barter_payment_button" type="button" value="PAY">
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div> <!-- end live-chat -->
            </aside>
            <?php
            }
        }


        /**
         * Process the token
         * 
         * An ajax callback to get the payment or donation
         * details from the token provided
         *
         * @param	none
         * @return	none
        */
        
        public function spreebie_barter_get_details_ajax() {
            check_ajax_referer('spreebie_barter_get_details_results', 'spreebie_barter_get_details_results_nonce');

            // $_POST data containing the token
            $sanitized_token = sanitize_text_field($_POST['spreebie_barter_token']);
            $spreebie_barter_token = $sanitized_token;
            
            // The result order
            $order = 'DESC';
            
            // The payment query
            $spreebie_barter_payment_query = new WP_Query(
                array(
                    'post_type' => 'sb_p_and_d',
                    'order' => $order,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_key' => 'payment_token',
                    'meta_value' => $spreebie_barter_token
                )
            );

            // The donation query
            $spreebie_barter_donation_query = new WP_Query(
                array(
                    'post_type' => 'sb_p_and_d',
                    'order' => $order,
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'meta_key' => 'donation_token',
                    'meta_value' => $spreebie_barter_token
                )
            );

            if ($spreebie_barter_payment_query->have_posts()) {
                $spreebie_barter_post = $spreebie_barter_payment_query->get_posts()[0];

                $spreebie_barter_post_meta = get_post_meta($spreebie_barter_post->ID);
                ?>
                <!-- Insert the payment data into HTML that will also be used by the ajax handler -->
                <div class="spreebie_barter_details" id="spreebie_barter_details">
                    <p>Customer name: <div id="spreebie_barter_customer_name"><?php echo esc_html($spreebie_barter_post_meta['customer_name'][0]); ?></div></p>
                    <br>
                    <p>Customer email: <div id="spreebie_barter_customer_email"><?php echo esc_html($spreebie_barter_post_meta['customer_email'][0]); ?></div></p>
                    <br>
                    <p>Payment title: <div id="spreebie_barter_payment_title"><?php echo esc_html(get_the_title($spreebie_barter_post->ID)); ?></div></p>
                    <br>
                    <p>Payment description: <?php echo esc_html($spreebie_barter_post_meta['payment_description'][0]); ?></p>
                    <br>
                    <p>Payment currency: <div id="spreebie_barter_payment_currency"><?php echo esc_html(strtoupper($spreebie_barter_post_meta['payment_currency'][0])); ?></div></p>
                    <br>
                    <p>Payment amount: <div id="spreebie_barter_payment_amount"><?php echo esc_html(strtoupper($spreebie_barter_post_meta['payment_amount'][0])); ?></div></p>
                    <br>
                    <p>Payment settled: <div id="spreebie_barter_payment_settled"><?php echo esc_html($spreebie_barter_post_meta['payment_settled'][0]); ?></div></p>
                    <br>
                    <p id="spreebie_barter_post_id"><?php echo esc_html($spreebie_barter_post->ID); ?><</p>
                </div>
                <form action="#" method="post">
                    <fieldset>
                        <input id="spreebie_barter_payment_button" type="button" value="PAY">
                    </fieldset>
                </form>
                <?php
            } elseif ($spreebie_barter_donation_query->have_posts()) {
                $spreebie_barter_post = $spreebie_barter_donation_query->get_posts()[0];

                $spreebie_barter_post_meta = get_post_meta($spreebie_barter_post->ID);
                ?>
                <!-- Insert the donation data into HTML that will also be used by the ajax handler -->
                <div class="spreebie_barter_details" id="spreebie_barter_details">
                    <p>Donation description: <?php echo esc_html($spreebie_barter_post_meta['donation_description'][0]); ?></p>
                    <br>
                    <p>Donation currency: <div id="spreebie_barter_donation_currency"><?php echo esc_html(strtoupper($spreebie_barter_post_meta['donation_currency'][0])); ?></div></p>
                    <br>
                    <p id="spreebie_barter_post_id"><?php echo esc_html($spreebie_barter_post->ID); ?></p>
                    <br>
                    <p>Below, enter the amount the amount you want to donate (left) and donate by clicking the button (right). </p>
                </div>
                <form action="#" method="post">
                    <fieldset>
                        <input id="spreebie_barter_donation_amount_field" type="number" placeholder="Enter the amount..." autofocus>  
                        <input id="spreebie_barter_donation_button" type="button" value="DONATE">
                    </fieldset>
                </form>
                <?php
            } else {
                ?>
                <!-- Insert the error into HTML that will also be used by the ajax handler -->
                <div class="spreebie_barter_details" id="spreebie_barter_details">
                    <p>Sorry! The token you entered is not associated with any payment or donation. Please check that your token is correct.</p>
                </div>
                <form action="#" method="post">
                    <fieldset>
                        <input id="spreebie_barter_message_field" type="button" value="PAY">
                    </fieldset>
                </form>
                <?php
            }
            die();
        }


        /**
         * Update payment setted
         * 
         * An ajax callback to get update the payment 
         * settled post meta
         *
         * @param	none
         * @return	none
        */
        
        public function spreebie_barter_update_payment_settled_ajax() {
            check_ajax_referer('spreebie_barter_update_payment_settled_results', 'spreebie_barter_update_payment_settled_results_nonce');

            // Validate the int before making the update
            $validated_spreebie_barter_post_id = intval((int) $_POST['spreebie_barter_post_id']);
            if ($validated_spreebie_barter_post_id) {
                $spreebie_barter_post_id = $validated_spreebie_barter_post_id;

                // Update the post meta to indicate that the payment has been settled
                update_post_meta($spreebie_barter_post_id, 'payment_settled', "Yes");

                $sanitized_spreebie_barter_customer_email = sanitize_email($_POST['spreebie_barter_customer_email']);
                $spreebie_barter_customer_email = $sanitized_spreebie_barter_customer_email;

                $sanitized_spreebie_barter_customer_name = sanitize_text_field($_POST['spreebie_barter_customer_name']);
                $spreebie_barter_customer_name = $sanitized_spreebie_barter_customer_name;

                $sanitized_spreebie_barter_payment_currency = sanitize_text_field($_POST['spreebie_barter_payment_currency']);
                $spreebie_barter_payment_currency = $sanitized_spreebie_barter_payment_currency;

                $sanitized_spreebie_barter_payment_amount = sanitize_text_field($_POST['spreebie_barter_payment_amount']);
                $spreebie_barter_payment_amount = $sanitized_spreebie_barter_payment_amount;

                $sanitized_spreebie_barter_payment_title = sanitize_text_field($_POST['spreebie_barter_payment_title']);
                $spreebie_barter_payment_title = $sanitized_spreebie_barter_payment_title;

                // Create a payment receipt
                $spreebie_barter_payment_receipt = "rec_" . get_bloginfo('name') . "_" . time() . "_" . mt_rand();

                // Create a subject text for the email
                $spreebie_barter_payment_subject_text = "Spreebie Barter payment - receipt no: " . $spreebie_barter_payment_receipt;

                $spreebie_barter_payment_message_text = "Hello " . $spreebie_barter_customer_name .
                                                        "\n\nYou have successfully settled the payment titled: " . $spreebie_barter_payment_title .
                                                        "\n\nThe payment amount is: " . $spreebie_barter_payment_currency . " " . $spreebie_barter_payment_amount  . 
                                                        "\n\nYour receipt number is: " . $spreebie_barter_payment_receipt .
                                                        "\n\n" . get_bloginfo('name');
            
                // Send mail to customer
                wp_mail($spreebie_barter_customer_email, $spreebie_barter_payment_subject_text, $spreebie_barter_payment_message_text);
            }

            die();
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
        
        public function spreebie_barter_load_scripts() {
            wp_register_style('spreebie_barter_widget_css', plugins_url('css/style.css', __FILE__));
            wp_register_script('spreebie_barter_dom_purify', plugins_url('js/dompurify/purify.min.js', __FILE__), array('jquery'), null, true);
            wp_register_script('spreebie_barter_widget_ajax', plugins_url('js/spreebie-barter-widget-ajax.js', __FILE__), array('jquery'), null, true);
            
            // Get the address of this WP installation's 'admin-ajax.php'
            $spreebie_barter_ajax_url = admin_url('admin-ajax.php');
            
            // The ajax_url parameter being passed to the ajax handler
            $spreebie_barter_ajax_params = array(
                'spreebie_barter_ajax_url' => $spreebie_barter_ajax_url
            );
            
            // Pass the PHP parameter to Javascript by localizing it
            wp_localize_script('spreebie_barter_widget_ajax', 'spreebie_barter_ajax_params', $spreebie_barter_ajax_params);
            
            wp_enqueue_style('spreebie_barter_widget_css');
            wp_enqueue_script('spreebie_barter_dom_purify');
            wp_enqueue_script('spreebie_barter_widget_ajax');
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
    }

endif;

/**
* Custom post type backend callback
*
* @param    none
* @return   none
*/

function spreebie_barter_post_type_init() {
    new spreebie_barter_payments_donations();
}


/**
* Widget callback: registers Spreebie Barter
* widget
*
* @param    none
* @return   none
*/

function spreebie_barter_widget_reg() {
    register_widget('spreebie_barter');
}


/**
* Deactivation callback: removes assorted data
* that will be added in later versions of Spreebie Barter
*
* @param    none
* @return   none
*/

function spreebie_barter_deactivate() {
    // do nothing, not yet; at least not in version 1.0.1
}

$spreebie_barter_admin_1 = new spreebie_barter_admin();

// This initializes the custom post type, taxonomy and other relevant backend forms
add_action('init', 'spreebie_barter_post_type_init');

// This add a settings page
add_action('admin_menu', array($spreebie_barter_admin_1, 'spreebie_barter_add_menu_page'));

// This adds settings functionality to the settings page
add_action('admin_init', array($spreebie_barter_admin_1, 'spreebie_barter_initialize_options'));

add_action('widgets_init', 'spreebie_barter_widget_reg');

register_deactivation_hook(__FILE__, 'spreebie_barter_deactivate');
?>