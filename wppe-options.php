<div class="wrap">
	<h2><?php _e('Email Settings', 'wp-promo-emails'); ?></h2>

	<form method="post" action="options.php" id="wppe_options_form">
		<?php settings_fields('wppe_full_options'); ?>
<input type="hidden" name="subject_email" value="<?php echo esc_attr( $this->options['subject_email']);?>">
<input type="hidden" name="template" value="<?php echo esc_attr( $this->options['template']);?>">
<input type="hidden" name="wppe_preview" value="<?php echo esc_attr( $this->options['admin_email']);?>">
		<!-- Sender options -->
		<h3 class="wppe_title"><?php _e('Email Options', 'wp-promo-emails'); ?></h3>
		<p style="margin-bottom: 0;"><?php _e('Set your own email subject.', 'wp-promo-emails'); ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="wppe_subject_email"><?php _e('Subject', 'wp-promo-emails'); ?></label></th>
				<td><input type="text" id="wppe_subject_email" class="regular-text" name="wppe_options[subject_email]" value="<?php esc_attr_e($this->options['subject_email']); ?>" /></td>
		</table>

		<!-- Template -->
		<h3 class="wppe_title"><?php _e('HTML Template', 'wp-promo-emails'); ?></h3>
		<p><?php _e('Edit the HTML template if you want to customize it.');?></p>
		<div id="wppe_template_container">
			<?php $this->template_editor() ?>
		</div>
		<!-- Preview -->
		<h3 class="wppe_title"><?php _e('Preview', 'wp-promo-emails'); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wppe_email_preview_field"><?php _e('Send an email preview to', 'wp-promo-emails'); ?></label>
				</th>
				<td>
					<input type="text" id="wppe_email_preview_field" name="wppe_preview" class="regular-text" value="<?php esc_attr_e(get_option('admin_email')); ?>" />
					<input type="submit" class="button" id="wppe_send_preview">&nbsp;<?php _e('Send Preview', 'wp-promo-emails'); ?>
					<br /><span class="description"><?php _e('You must save your template before sending an email preview and/or promotion.', 'wp-promo-emails'); ?></span>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-promo-emails') ?>" />
			<input type="submit" name="wp_promo" class="button-primary" value="<?php _e('Send Promotion', 'wp-promo-emails') ?>" />
		</p>
	</form>
	<!-- Support -->
	<div id="wppe_support">
		<h3><?php _e('Support & bug report', 'wp-promo-emails'); ?></h3>
		<p><?php printf(__('If you have any idea to improve this plugin or any bug to report, please email me at : <a href="%1$s">%2$s</a>', 'wp-promo-emails'), 'mailto:wppromoemails@cbfreeman.com?subject=[wp-promo-emails]', 'wppromoemails@cbfreeman.com'); ?></p>
		<?php $donation_link = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BAZNKCE6Q78PJ'; ?>
		<p><?php printf(__('You like this plugin ? You use it in a business context ? Please, consider a <a href="%s" target="_blank" rel="external">donation</a>.', 'wp-promo-emails'), $donation_link ); ?></p>
<p><a href="http://cbfreeman.com/products/wp-promo-emails-premier/">
  <span class="button-primary">Get the Premier Version for $32 with lifetime updates and support at CBFREEMANWP</span>
  </a></p>
	</div>
</div>