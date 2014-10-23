<?php
/*
  Copyright (c) 2014 Craig Freeman.

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


// Add Global WP Database function
global $wpdb;
$url = site_url();
$name = get_bloginfo();
$admin = get_option( 'admin_email' );

if(isset($_POST['wp_promo'])) {
if(isset($_POST['subject_email']))
$subject = ($_POST['subject_email']);
if(isset($_POST['template']))
$template = ($_POST['template']);
global $wpdb;
$team = $wpdb->get_results(
"
	SELECT ID, user_email
	FROM $wpdb->users"
);
foreach ( $team as $team )
{
$email = $team->user_email;
$content = "$template";
$to = "$email";
$subject = "$subject";
$sender = "$name Support for you" ;
$email = "$name<$admin>";
$headers = "From: " . $email . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$sent = mail($to, $subject, $content, $headers) ;

 }

}


if ( ! class_exists( 'WP_Promo_Emails' ) ) {

	if ( ! defined( 'WPPE_JS_URL' ) )
		define( 'WPPE_JS_URL', plugin_dir_url( __FILE__ ) . 'js' );

	if ( ! defined( 'WPPE_CSS_URL' ) )
		define( 'WPPE_CSS_URL', plugin_dir_url( __FILE__ ) . 'css' );

	class WP_Promo_Emails {

		var $options = array();
		var $page = '';
		var $send_as_html;

		/**
		 * Construct function
		 *
		 * @since 0.2
		 */
		function __construct() {
			global $wp_version;

			$this->get_options();

			add_action( 'phpmailer_init',   array( $this, 'send_html' ) );
			

			if ( ! is_admin() )
				return;

			// Load translations
			load_plugin_textdomain( 'wp-promo-emails', null, basename( dirname( __FILE__ ) ) . '/langs/' );

			// Actions
			add_action( 'admin_init',           array( $this, 'init' ) );
			add_action( 'admin_menu',           array( $this, 'admin_menu' ) );
			add_action( 'wp_ajax_send_preview', array( $this, 'ajax_send_preview' ) );
			add_action( 'phpmailer_init',   array( $this, 'send_html' ) );
			add_filter( 'mandrill_payload', array( $this, 'wpmandrill_compatibility' ) );

			if ( version_compare( $wp_version, '3.2.1', '<=' ) )
				add_action( 'admin_head', array( $this, 'load_wp_tiny_mce' ) );

			if ( version_compare( $wp_version, '3.2', '<' ) && version_compare( $wp_version, '3.0.6', '>' ) )
				add_action( 'admin_print_footer_scripts', 'wp_tiny_mce_preload_dialogs' );

			// Filters
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_link' ) );
			add_filter( 'mce_external_plugins', array( $this, 'tinymce_plugins' ) );
			add_filter( 'mce_buttons',          array( $this, 'tinymce_buttons' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_config' ) );

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

			// Plain-text default template
			$plaintext = '%content%

---

Email sent %date% @ %time%
For any requests, please contact %admin_email%';

			// Setup options array
			$this->options = array(
				'subject_email'         => '',
				'from_name'          => '',
				'template'           => $template,
				'plaintext_template' => $plaintext
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
			$this->page = add_options_page( __( 'Email settings', 'wp-promo-emails' ), __( 'WP Promo Emails', 'wp-promo-emails' ), 'administrator', 'wppe_options', array( $this, 'admin_page' ) );

			add_action( 'admin_print_scripts-' . $this->page, array( $this, 'admin_print_script' ) );
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
		 * Enqueue the script to display it on the options page
		 * Add Javascript to handle AJAX email preview
		 *
		 * @since 0.1
		 */
		function admin_print_script() {
			wp_enqueue_script( 'wppe-admin-script' );

			$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://';
			$ajax_vars = array(
				'url'    => admin_url( 'admin-ajax.php', $protocol ),
				'nonce'  => wp_create_nonce( 'email_preview' ),
				'action' => 'send_preview'
				 
			);

			wp_localize_script( 'wppe-admin-script', 'wppe_ajax_vars', $ajax_vars );
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

			/** Check plain-text template ***********************************/
       
			$input['plaintext_template'] = $input['plaintext_template'];


			return $input;
		}

		/**
		 * Send a email preview to test the template
		 *
		 * @since 0.1
		 * @param string $email
		 */
		function ajax_send_preview( $email ) {
			if ( ! current_user_can( 'manage_options' ) )
				die();

			check_ajax_referer( 'email_preview' );

			$preview_email = sanitize_email( $_POST['preview_email'] );

			if ( empty( $preview_email ) )
				die( '<div class="error"><p>' . __( 'Please enter an email', 'wp-promo-emails' ) . '</p></div>' );

			if ( ! is_email( $preview_email ) )
				die( '<div class="error"><p>' . __( 'Please enter a valid email', 'wp-promo-emails' ) . '</p></div>' );

			// Setup preview message content
			$message = __( 'Hey !', 'wp-promo-emails' );
			$message .= "\r\n\r\n";
			$message .= __( 'This is a sample email to test your HTML template.', 'wp-promo-emails' );
			$message .= "\r\n\r\n";
			$message .= __( 'If you\'re not skilled in HTML/CSS email coding, I strongly recommend to leave the default template as it is. It has been tested on various and popular email clients like Gmail, Yahoo Mail, Hotmail/Live, Thunderbird, Apple Mail, Outlook, and many more.', 'wp-promo-emails' );
			$message .= "\r\n\r\n";
			$message .= __( 'If you have any problems or any suggestions to improve this plugin, please let me know.', 'wp-promo-emails' );
			$message .= "\r\n\r\n";

			// Send the preview email
			if ( wp_mail( $preview_email, '[' . wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . '] - ' . __( 'Email template preview', 'wp-promo-emails' ), $message ) ) {
				die( '<div class="updated"><p>' . sprintf( __( 'An email preview has been successfully sent to %s' , 'wp-promo-emails' ), esc_attr( $preview_email ) ) . '</p></div>' );
			} else {
				die( '<div class="error"><p>' . __( 'An error occured while sending email. Please check your server configuration.', 'wp-promo-emails' ) . '</p></div>' );
			}
		}

		/**
		 * Replace variables in the template
		 *
		 * @since 0.1
		 * @param string $template Template with variables
		 * @return string Template with variables replaced
		 */
		function template_vars_replacement( $template ) {

			$to_replace = array(
				'blog_url'         => get_option( 'siteurl' ),
				'home_url'         => get_option( 'home' ),
				'blog_name'        => get_option( 'blogname' ),
				'blog_description' => get_option( 'blogdescription' ),
				'admin_email'      => get_option( 'admin_email' ),
				'date'             => date_i18n( get_option( 'date_format' ) ),
				'time'             => date_i18n( get_option( 'time_format' ) )
			);
			$to_replace = apply_filters( 'wppe_tags', $to_replace );

			foreach ( $to_replace as $tag => $var ) {
				$template = str_replace( '%' . $tag . '%', $var, $template );
			}

			return $template;
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
		 * Make templating compatible with WpMandrill
		 * @since 0.2.7
		 * @param  array $message
		 * @return array
		 */
		function wpmandrill_compatibility( $message ) {

			if ( $this->check_template() ) {
				$message['html'] = $this->process_email_html( $message['html'] );
			}

			return $message;
		}

		/**
		 * Add the email template and set it as multipart.
		 *
		 * @since 0.1
		 * @param object $phpmailer
		 */
		function send_html( $phpmailer ) {

			$phpmailer->AltBody = $this->process_email_text( $phpmailer->AltBody );

			if ( $this->send_as_html ) {
				$phpmailer->Body = $this->process_email_html( $phpmailer->Body );
			}

		}

		/**
		 * Process the text version of the message
		 *
		 * @since 0.2.7
		 * @param string $message
		 * @return string
		 */
		function process_email_text( $message ) {

			$message = strip_tags( $message );

			// Decode body
			$message = wp_specialchars_decode( $message, ENT_QUOTES );

			// Add plain-text template to message
			$message = $this->set_email_template( $message, 'plaintext_template' );

			// Replace variables in email
			$message = apply_filters( 'wppe_plaintext_body', $this->template_vars_replacement( $message ) );

			return $message;
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

			// Add template to message
			$message = $this->set_email_template( $message );

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
		 * Load WP tinyMCE editor
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

		/**
		 * Print plain-text textarea field to edit template.
		 *
		 * @since 0.2.x
		 */
		function plaintext_template_editor() {
?>
			<textarea id="wppe_plaintext_template" class="wppe_template" name="wppe_options[plaintext_template]" cols="120" rows="10"><?php echo $this->options['plaintext_template']; ?></textarea>
		<?php
		}

	}

}


if ( class_exists( 'WP_promo_Emails' ) ) {
	$wp_promo_emails = new WP_promo_Emails();
	register_activation_hook( __FILE__, array( $wp_promo_emails, 'install' ) );
}
