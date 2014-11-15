<?php
/*
Plugin Name: WP Promo Emails
Plugin URI: http://cbfreeman.com/downloads/wp-promo-emails-premier/
Description: Create content rich HTML Emails to send to registered site users. Manage newsletter reports from admin dashboard.
Version: 4.0
Author: cbfreeman
Author URI: http://cbfreeman.com
License: GPLv3
 */

/*
  Copyright (c) 2014 Craig Freeman (email :ceo@cbfreeman.com)

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 3
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


  /**
  * Add Global WP Email
  *
  * create subscriber table
  * get general settings
  * email form
  * unsubscribe message & link
  */
  global $wpdb, $wp_version;
  define("WP_PROMO_EMAILS_TABLE", $wpdb->prefix . "users");
  define("WP_PROMO_EMAILS_NEWSLETTER_TABLE", $wpdb->prefix . "wppenewsletter");
  
  function email_install()
{
    global $wpdb, $wp_version;
    
    $wpdb->query("
            ALTER TABLE `". WP_PROMO_EMAILS_TABLE . "`
              ADD COLUMN `subscriber` char(3) NOT NULL default 'Y'
            ");
    
       
    if(strtoupper($wpdb->get_var("show tables like '". WP_PROMO_EMAILS_NEWSLETTER_TABLE . "'")) != strtoupper(WP_PROMO_EMAILS_NEWSLETTER_TABLE))
    {
        $wpdb->query("
            CREATE TABLE `". WP_PROMO_EMAILS_NEWSLETTER_TABLE . "` (
              `nid` int(11) NOT NULL auto_increment,
              `nfrom` char(250) NOT NULL ,
              `nsubject` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
              `ntemplate` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
              `nmember` char(250) NOT NULL ,
              `nsub` char(250) NOT NULL ,
              `nsent` char(3) NOT NULL default '0',
               `nopen` char(3) NOT NULL default '0',
                `nbounce` char(3) NOT NULL default '',
              `ndate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY  (`nid`) )
            ");
            
    }
  
}


           
  // GET WP General Settings
  $url = site_url();
  $name = get_bloginfo();
  $admin = get_option( 'admin_email' );
	
	// Send Preview Email
  if(isset($_POST['wppe_preview'])) {
  if(isset($_POST['admin']))
  $email = ($_POST['admin']);
  if(isset($_POST['subject_email']))
  $subject = ($_POST['subject_email']);
  if(isset($_POST['template']))
  $template = ($_POST['template']);
  $content = "$template";
  $to = "$email";
  $subject = "$subject";
  $sender = "$name Support for you" ;
  $email = "$name<$admin>";
  $headers = "From: " . $email . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $sent = mail($to, $subject, $content, $headers) ;
 }


  // Send HTML Email
  if(isset($_POST['wp_promo'])) {
  if(isset($_POST['subject_email']))
  $subject = ($_POST['subject_email']);
  if(isset($_POST['template']))
  $template = ($_POST['template']);
  global $wpdb;

  $registrant = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users ");
  $recipient = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users WHERE subscriber='Y'");
  
  $table_name = $wpdb->prefix . "wppenewsletter";
  $wpdb->insert( $table_name, array( 'nfrom' => $admin ,'nsubject' => $_POST['subject_email'], 'ntemplate' => $_POST['template'], 'nmember' => $registrant ,'nsub' => $recipient, 'nsent' => $recipient ) );

  $team = $wpdb->get_results(
  "
	SELECT ID, user_email
	FROM $wpdb->users WHERE subscriber='Y'"
  );
  foreach ( $team as $team )
 {
  $email = $team->user_email;
  $unsubscribelink = get_option('siteurl') . "/wp-content/plugins/wp-promo-emails/unsubscribe/unsubscribe.php?member_0_0_0_0=$email";
  $author = rawurlencode($subject);
  $content = "$template<br>If you do not wish to receive this email please <a href='$unsubscribelink'>unsubscribe</a>.";
  $to = "$email";
  $subject = "$subject";
  $sender = "$name Support for you" ;
  $email = "$name<$admin>";
  $headers = "From: " . $email . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $sent = mail($to, $subject, $content, $headers) ;

 }

}

  
  
  // Resend HTML Email
  if(isset($_POST['wppe_bonusfx'])) {
  if(isset($_POST['wppe_niddle']))
  $niddle = ($_POST['wppe_niddle']);
  $niddler =$wpdb->get_results("SELECT ntemplate FROM $wpdb->wppenewsletter WHERE nid='$niddle'");
  $resend = $wpdb->get_results(
  "
	SELECT ID, user_email
	FROM $wpdb->users WHERE subscriber='Y'"
  );
  foreach ( $resend as $resend )
 {
  $email = $resend->user_email;
  $unsubscribelink = get_option('siteurl') . "/wp-content/plugins/wp-promo-emails/unsubscribe/unsubscribe.php?member_0_0_0_0=$email";
  $success = "<img src=" . get_option('siteurl') . "/wp-content/plugins/wp-promo-emails/records/success.php?mail_1_1_1=$nsub>";
  $content = "$niddler<br>If you do not wish to receive this email please <a href='$unsubscribelink'>unsubscribe</a>.$success";
  $to = "$email";
  $subject = "$nsub";
  $sender = "$name Support for you" ;
  $email = "$name<$admin>";
  $headers = "From: " . $email . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
  $sent = mail($to, $subject, $content, $headers) ;

 }

}

   
   //Start
  if ( ! class_exists( 'WP_Promo_Emails' ) ) {

	if ( ! defined( 'WPPE_JS_URL' ) )
		define( 'WPPE_JS_URL', plugin_dir_url( __FILE__ ) . 'js' );

	if ( ! defined( 'WPPE_CSS_URL' ) )
		define( 'WPPE_CSS_URL', plugin_dir_url( __FILE__ ) . 'css' );

	class WP_Promo_Emails {

		var $options = array();
		var $page = '';

		/**
		 * Construct function
		 *
		 * @since 0.2
		 */
		function __construct() {
			global $wp_version;

			$this->get_options();
			

			if ( ! is_admin() )
				return;

			// Load translations
			load_plugin_textdomain( 'wp-promo-emails', null, basename( dirname( __FILE__ ) ) . '/langs/' );

			// Actions
			add_action( 'admin_init',           array( $this, 'init' ) );
			add_action( 'admin_menu',           array( $this, 'admin_menu' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );

			if ( version_compare( $wp_version, '3.2.1', '<=' ) )
				add_action( 'admin_head', array( $this, 'load_wp_tiny_mce' ) );

			if ( version_compare( $wp_version, '3.2', '<' ) && version_compare( $wp_version, '3.0.6', '>' ) )
				add_action( 'admin_print_footer_scripts', 'wp_tiny_mce_preload_dialogs' );

			// Filters
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_link' ) );
			add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugins' ) );
			add_filter( 'mce_buttons',          array( $this, 'tinymce_buttons' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_config' ) );
			register_activation_hook(__FILE__, 'email_install');

		}
		
	

		/**
		 * Get recorded options
		 *
		 * @since 0.2
		 */
		function get_options() {
			$this->options = get_option( 'wppe_options' );
		}

		/**
		 * Set the default options
		 *
		 * @since 0.2
		 */
		function set_options() {

			// HTML default template
			$template = '';
			@require 'templates/template-1.php';
			

			// Setup options array
			$this->options = array(
				'subject_email'         => '',
				'template'           => $template
			);

			// If option doesn't exist, save default option
			if ( get_option( 'wppe_options' ) === false ) {
				add_option( 'wppe_options', $this->options );
			}
		}

		/**
		 * Init plugin options to white list our options & register our script
		 *
		 * @since 0.1
		 */
		function init() {
			register_setting( 'wppe_full_options', 'wppe_options', array( $this, 'validate_options' ) );
			 wp_register_script( 'wppe-admin-script', WPPE_JS_URL . '/wppe-admin-script.js', array( 'jquery', 'thickbox' ), null, true );
			 wp_register_style( 'wppe-admin-style', WPPE_CSS_URL . '/wppe-admin-style.css' );
			 wp_register_style( 'wppe-dashboard-style', WPPE_CSS_URL . '/dashboard.css');
   		 wp_enqueue_style( 'wppe-dashboard-style' );
		}

	  
		/**
		 * Settings link in the plugins page
		 *
		 * @since 0.1
		 *
		 * @param array   $links Plugin links
		 * @return array Plugins links with settings added
		 */
		function settings_link( $links ) {
			$links[] = '<a href="options-general.php?page=wppe_options">' . __( 'Settings', 'wp-promo-emails' ) . '</a>';

			return $links;
		}

		/**
		 * Record options on plugin activation
		 *
		 * @since 0.1
		 * @global $wp_version
		 */
		function install() {
			global $wp_version;
			// Prevent activation if requirements are not met
			// WP 2.8 required
			if ( version_compare( $wp_version, '2.8', '<=' ) ) {
				deactivate_plugins( __FILE__ );

				wp_die( __( 'WP Promo Emails requires WordPress 2.8 or newer.', 'wp-promo-emails' ), __( 'Upgrade your Wordpress installation.', 'wp-promo-emails' ) );
			}

			$this->set_options();
		}

		/**
		 * Option page to the built-in settings menu
		 *
		 * @since 0.1
		 */
		function admin_menu() {
			$this->page = add_options_page( __( 'Email settings', 'wp-promo-emails' ), __( 'WP Promo Emails', 'wp-promo-emails' ), 'administrator', 'wppe_options',       array( $this, 'admin_page' ) );
			
			add_action( 'admin_print_styles-' . $this->page, array( $this, 'admin_print_style' ) );
		}

		/**
		 * Check if we're on the plugin page
		 *
		 * @since 0.2
		 * @global type $page_hook
		 * @return type
		 */
		function is_wppe_page() {
			global $page_hook;

			if ( $page_hook === $this->page )
				return true;

			return false;
		}


		/**
		 * Enqueue the style to display it on the options page
		 *
		 * @since 0.1
		 */
		function admin_print_style() {
			wp_enqueue_style( 'wppe-admin-style' );
			wp_enqueue_style( 'thickbox' );
		}

		/**
		 * Include admin options page
		 *
		 * @since 0.1
		 * @global $wp_version
		 */
		function admin_page() {
			global $wp_version;

			require 'wppe-options.php';
		}

		/**
		 * Sanitize each option value
		 *
		 * @since 0.1
		 * @param array   $input The options returned by the options page
		 * @return array $input Sanitized values
		 */
		function validate_options( $input ) {

			$subject_email = strtolower( $input['subject_email'] );
				$input['subject_email'] = esc_html( $input['subject_email'] );
			
	

			/** Check HTML template *****************************************/

			// Template is empty
			if ( empty( $input['template'] ) ) {
				add_settings_error( 'wppe_options', 'settings_updated', __( 'Newsletter is empty', 'wp-promo-emails' ) );
				
			}

			$input['template'] = $input['template'];


			return $input;
		}

	

		/**
		 * Checks the WP Promo Emails options
		 *
		 * @since 0.1
		 * @return bool
		 */
		function check_template() {
			if ( strpos( $this->options['template']) === false || empty( $this->options['template'] ) )
				return false;

			return true;
		}

		/**
		 * Always set content type to HTML
		 *
		 * @since 0.1
		 * @param string $content_type
		 * @return string $content_type
		 */
		function set_content_type( $content_type ) {
			// Only convert if the message is text/plain and the template is ok
			if ( $content_type == 'text/plain' && $this->check_template() === true ) {
				$this->send_as_html = true;
				return $content_type = 'text/html';
			} else {
				$this->send_as_html = false;
			}
			return $content_type;
		}


		/**
		 * Process the HTML version of the message
		 *
		 * @since 0.2.7
		 * @param string $message
		 * @return string
		 */
		function process_email_html( $message ) {

			// Clean < and > around text links in WP 3.1
			$message = $this->esc_textlinks( $message );

			// Convert line breaks & make links clickable
			$message = nl2br( make_clickable( $message ) );

			// Replace variables in email
			$message = apply_filters( 'wppe_html_body', $this->template_vars_replacement( $message ) );

			return $message;

		}

		/**
		 * Replaces the < & > of the 3.1 email text links
		 *
		 * @since 0.1.2
		 * @param string $body
		 * @return string
		 */
		function esc_textlinks( $body ) {
			return preg_replace( '#<(https?://[^*]+)>#', '$1', $body );
		}

		/**
		 * TinyMCE plugins
		 *
		 * Editing HTML emails requires some more plugins from TinyMCE:
		 *  - fullpage to handle html, meta, body tags
		 *  - codemirror for editing source
		 *
		 * @since 0.2
		 * @param array $external_plugins
		 * @return array
		 */
		function tinymce_plugins( $external_plugins ) {
			global $wp_version;

			if ( ! $this->is_wppe_page() )
				return $external_plugins;

			$fullpage = array();

			if ( version_compare( $wp_version, '3.2', '<' ) ) {
				$fullpage = array(
					'fullpage' => plugins_url( 'tinymce-plugins/3.3.x/fullpage/editor_plugin.js', __FILE__ )
				);
			} else {
				$fullpage = array(
					'fullpage' => plugins_url( 'tinymce-plugins/3.4.x/fullpage/editor_plugin.js', __FILE__ )
				);
			}

			$cmseditor = array(
				'cmseditor' => plugins_url( 'tinymce-plugins/cmseditor/editor_plugin.js', __FILE__ )
			);

			$external_plugins = $external_plugins + $fullpage + $cmseditor;

			return $external_plugins;
		}

		/**
		 * Button to the TinyMCE toolbar
		 *
		 * @since 0.2
		 * @global string $page
		 * @global type $page_hook
		 * @param type $buttons
		 * @return type
		 */
		function tinymce_buttons( $buttons ) {
			if ( $this->is_wppe_page() )
				array_push( $buttons, 'cmseditor' );

			return $buttons;
		}

		/**
		 * Prevent TinyMCE from removing line breaks
		 *
		 * @param array $init
		 * @return boolean
		 */
		function tinymce_config( $init ) {
			if ( ! $this->is_wppe_page() )
				return $init;

			$init['remove_linebreaks'] = false;
			$init['content_css']       = ''; // WP =< 3.0

			if ( isset( $init['extended_valid_elements'] ) )
				$init['extended_valid_elements'] = $init['extended_valid_elements'] . ',td[*]';

			return $init;
		}

		/**
		 * Load WP TinyMCE editor
		 *
		 * @since 0.2
		 */
		function load_wp_tiny_mce() {
			if ( ! $this->is_wppe_page() )
				return;

			$settings = array(
				'editor_selector' => 'wppe_template',
				'height'          => '400'
			);

			wp_tiny_mce( false, $settings );
		}

		/**
		 * Print WP TinyMCE editor to edit template
		 *
		 * @since 0.2
		 * @global string $wp_version
		 */
		function template_editor() {
			global $wp_version;

			if ( version_compare( $wp_version, '3.2.1', '<=' ) ) {
?>
				<textarea id="wppe_template" class="wppe_template" name="wppe_options[template]" cols="80" rows="10"><?php echo $this->options['template']; ?></textarea>
				<?php
			} else {
				// WP >= 3.3
				$settings = array(
					'wpautop'       => false,
					'editor_class'  => 'wppe_template',
					'quicktags'     => false,
					'textarea_name' => 'wppe_options[template]'
				);

				wp_editor( $this->options['template'], 'wppe_template', $settings );
			}
		}

	}

}

  
  /**
   *
   * WP Promo Emails Dashboard
   */
  function wppe_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'wppe_dashboard_widget',         // Widget slug.
                 'WP Promo Emails Stats',         // Title.
                 'wppe_dashboard_widget_function' // Display function.
        );
}
  add_action( 'wp_dashboard_setup', 'wppe_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
  function wppe_dashboard_widget_function() {
  global $wpdb;
  $mailer=$wpdb->get_var("SELECT COUNT(nid) FROM {$wpdb->prefix}wppenewsletter" );
  if ($mailer < 1000) {
    $promo = number_format($mailer);
  }elseif ($mailer < 1000000) {
    $promo = number_format($mailer / 1000) . 'K';
   } else if ($mailer < 1000000000) {
    // Anything less than a billion
    $promo = number_format($mailer / 1000000, 1) . 'M';
   } else {
    // At least a billion
    $promo = number_format($mailer/ 1000000000, 1) . 'B';
   }
  $last = $wpdb->get_var( "SELECT ndate FROM {$wpdb->prefix}wppenewsletter LIMIT 1" );
  $n = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}users WHERE subscriber='Y'");
  if ($n < 1000) {
    $subs = number_format($n);
  }elseif ($n < 1000000) {
    $subs = number_format($n / 1000) . 'K';
  } else if ($n < 1000000000) {
    // Anything less than a billion
    $subs = number_format($n / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $subs = number_format($n / 1000000000, 1) . 'B';
   }
   
    echo "<div id='newsletter'>";
    echo $promo;
    echo "<br>";
    echo "newsletters";
    echo "<hr>";
    echo $subs;
    echo "<br>";
    echo "subscribers";
    echo "<hr>";
    echo "Last newsletter sent on $last";
    echo "</div>";

  
}

if ( class_exists( 'WP_Promo_Emails' ) ) {
	$wp_promo_emails = new WP_Promo_Emails();
	register_activation_hook( __FILE__, array( $wp_promo_emails, 'install' ) );
}
