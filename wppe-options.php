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
					<input type="submit" class="button" id="wppe_send_preview" name="wppe_send_preview">&nbsp;<?php _e('Send Preview', 'wp-promo-emails'); ?>
					<br /><span class="description"><?php _e('You must save your template before sending an email preview and/or promotion.', 'wp-promo-emails'); ?></span>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wp-promo-emails') ?>" />
			<input type="submit" name="wp_promo" class="button-primary" value="<?php _e('Send Promotion', 'wp-promo-emails') ?>" />
		</p>
	</form>
	<br>
	<hr>
	<br>
	<!-- Dashboard -->
<?php
	/**
   * Wp Promo Emails Subscriber Tables
   *
   * Newsletter Records
   * Email Receipts Table
   * Last 5 Newsletters
   */
  
  global $wpdb;
  $nid=$wpdb->get_var("SELECT COUNT(nid) FROM {$wpdb->prefix}wppenewsletter" );
  $members=$wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->prefix}users ");
  $subs = $wpdb->get_var("SELECT COUNT(ID) FROM {$wpdb->prefix}users WHERE subscriber='Y'");
  $sent = $wpdb->get_var("SELECT SUM(nsent) FROM {$wpdb->prefix}wppenewsletter ");
  $open = $wpdb->get_var("SELECT SUM(nopen) FROM {$wpdb->prefix}wppenewsletter ");
  $waiting = ($sent - $open);
   
  
?>
		<h3 class="wppe_title"><?php _e('WP Promo Emails Dashboard', 'wp-promo-emails'); ?></h3>
		<p><?php _e('Take a look at newsletter reports.');?></p>
		<h4><?php _e('Statistics');?></h4>
		<table class="wp-list-table widefat fixed media">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column">
		<label>Newsletters</label></th>
			<th scope="col" id="cb" class="manage-column">
		<label>Members</label></th>
			<th scope="col" id="cb" class="manage-column">
	<label>Subscribers</label></th>
			<th scope="col" id="cb" class="manage-column">
		<label>Sent</label></th>
		<th scope="col" id="cb" class="manage-column">
		<label>Open</label></th>
		<th scope="col" id="cb" class="manage-column">
		<label>Waiting</label></th>
		</tr>
	</thead>
	<tbody id="the-list">
	<?php
		echo "<tr class='alternate'>";
		echo "<td class='author'>" . $nid . "</td>";
		echo "<td class='author'>" . $members . "</td>";
		echo "<td class='author'>" . $subs . "</td>";
		echo "<td class='author'>" . $sent . "<br>emails</td>";
		echo "<td class='author'>" . $open . "<br>emails</td>";
		echo "<td class='author'>" . $waiting . "<br>emails</td>";
		echo "</tr>";
		?>
		
	</tbody>
</table>
	<!-- Last 5 Newsletters -->
		<h3 class="wppe_title"><?php _e('5 Lastest Sent Newsletters', 'wp-promo-emails'); ?></h3>
		<form  name="wppe_resent" action="" method="POST">
<table class="wp-list-table widefat fixed media">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column">
		<label>Date of Last Sent</label></th>
		<th scope="col" id="cb" class="manage-column">
		<label>Email Subject</label></th>
		<th scope="col" id="cb" class="manage-column">
		<label>Sent From</label></th>
			<th scope="col" id="cb" class="manage-column">
		<label>Sent To</label></th>
			<th scope="col" id="cb" class="manage-column">
		<label>Opened</label></th>
			<th scope="col" id="cb" class="manage-column">
		<label>Actions</label></th>
		</tr>
	</thead>
	<tbody id="the-list">
	<?php
	// Last 5 Newsletters database
   // Resend HTML Email
   
  
     
  if(isset($_POST['wppe_niddle'])) {
  $niddle = ($_POST['wppe_niddle']);
  global $wpdb;
  $name = get_bloginfo();
  $admin = get_option( 'admin_email' );
  $record = $wpdb->get_results("SELECT nsubject,ntemplate FROM {$wpdb->prefix}wppenewsletter WHERE nid='$niddle'");
  foreach ($record as $record) {
  $nidtemplate = $record->ntemplate;
  $nsub = $record->nsubject;
  }
  $resend = $wpdb->get_results(
  "
	SELECT ID, user_email
	FROM $wpdb->users WHERE subscriber='Y'"
  );
  foreach ( $resend as $resend )
 {
  $email = $resend->user_email;
  $unsubscribelink = get_option('siteurl') . "/wp-content/plugins/wp-promo-emails/unsubscribe/unsubscribe.php?member_0_0_0_0=$email";
  $content = "$nidtemplate<br>If you do not wish to receive this email please <a href='$unsubscribelink'>unsubscribe</a>.";
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

  $sql = "SELECT nid,ndate,nsubject,nfrom,nsent,nopen FROM {$wpdb->prefix}wppenewsletter ORDER by nid DESC LIMIT 5";
  $results = $wpdb->get_results($sql);
  foreach ($results as $row) {
  $nid2 = $row->nid;
  $ndate = $row->ndate;
  $nsub = $row->nsubject;
  $nfrom = $row->nfrom;
  $nsent = $row->nsent;
  $nopen = $row->nopen;

		echo "<tr class='alternate'>";
		echo "<td class='author'>" . $ndate . "</td>";
		echo "<td class='author'>" . $nsub. "</td>";
		echo "<td class='author'>" . $nfrom . "</td>";
		echo "<td class='author'>" . $nsent . "<br>members</td>";
		echo "<td class='author'>" . $nopen . "<br>members</td>";
                echo "<td class='author'>" . "<button class='button-primary' id='wppe_niddle_submit' name='wppe_niddle' type='submit' value='$nid2'>" . "resend" . "</button>";
		echo "</td></tr>";
		
}

		?>
	</tbody>
</table>
		</form>
	<!-- dashboard ends -->
	<br>
	<!-- Support -->
	<div id="wppe_support">
		<h3><?php _e('Support & bug report', 'wp-promo-emails'); ?></h3>
		<p><?php printf(__('If you have any idea to improve this plugin or any bug to report, please email me at : <a href="%1$s">%2$s</a>', 'wp-promo-emails'), 'mailto:wppromoemails@cbfreeman.com?subject=[wp-promo-emails]', 'wppromoemails@cbfreeman.com'); ?></p>
		<?php $donation_link = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BAZNKCE6Q78PJ'; ?>
		<p><?php printf(__('You like this plugin ? You use it in a business context ? Please, consider a <a href="%s" target="_blank" rel="external">donation</a>.', 'wp-promo-emails'), $donation_link ); ?></p>
	</div>
</div>