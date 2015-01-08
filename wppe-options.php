<div class="wrap">
	<h2><?php _e('Email Settings', 'wp-promo-emails'); ?></h2>

	<form method="post" action="options.php" id="wppe_options_form">
		<?php settings_fields('wppe_full_options'); ?>
<input type="hidden" name="subject_email" value="<?php echo esc_attr( $this->options['subject_email']);?>">
<input type="hidden" name="template" value="<?php echo esc_attr( $this->options['template']);?>">
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
					<input type="text" id="wppe_email_preview_field" name="admin" class="regular-text" value="<?php esc_attr_e(get_option('admin_email')); ?>" />
					<input type="submit" class="button" name="wppe_preview" value="<?php _e('Send Preview', 'wp-promo-emails'); ?>"/>
					<br /><span class="description"><?php _e('You must save your template before sending an email preview and/or promotion.', 'wp-promo-emails'); ?></span>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="empty" class="button-primary" value="<?php _e('Save Changes', 'wp-promo-emails') ?>" />
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
   * Promotions Records
   * Email Receipts Table
   * Promotions Logs
   */
  
  global $wpdb;
  
  $data1=$wpdb->get_var("SELECT COUNT(nid) FROM {$wpdb->prefix}wppenewsletter" );
  if ($data1< 1000) {
    $nid = number_format($data1);
  }elseif ($data1 < 1000000) {
    $nid = number_format($data1 / 1000) . 'K';
  } else if ($data1 < 1000000000) {
    // Anything less than a billion
    $nid = number_format($data1 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $nid = number_format($data1 / 1000000000, 1) . 'B';
   }
   
  $data2=$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cf7dbplugin_submits WHERE field_name='your-email' ");
  if ($data2< 1000) {
    $members = number_format($data2);
  }elseif ($data2 < 1000000) {
    $members = number_format($data2 / 1000) . 'K';
  } else if ($data2 < 1000000000) {
    // Anything less than a billion
    $members = number_format($data2 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $members = number_format($data2 / 1000000000, 1) . 'B';
   }
   
  $data3 = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}cf7dbplugin_submits WHERE field_name='your-email' and form_name='Subscribe'");
  if ($data3< 1000) {
    $subs = number_format($data3);
  }elseif ($data3 < 1000000) {
    $subs = number_format($data3 / 1000) . 'K';
  } else if ($data3 < 1000000000) {
    // Anything less than a billion
    $subs = number_format($data3 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $subs = number_format($data3 / 1000000000, 1) . 'B';
   }
   
  $data4 = $wpdb->get_var("SELECT SUM(nsent) FROM {$wpdb->prefix}wppenewsletter ");
  if ($data4< 1000) {
    $sent = number_format($data4);
  }elseif ($data4 < 1000000) {
    $sent = number_format($data4 / 1000) . 'K';
  } else if ($data4 < 1000000000) {
    // Anything less than a billion
    $sent = number_format($data4 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $sent = number_format($data4 / 1000000000, 1) . 'B';
   }
   
  $data5 = $wpdb->get_var("SELECT SUM(nopen) FROM {$wpdb->prefix}wppenewsletter ");
  if ($data5< 1000) {
    $open = number_format($data5);
  }elseif ($data5 < 1000000) {
    $open = number_format($data5 / 1000) . 'K';
  } else if ($data5 < 1000000000) {
    // Anything less than a billion
    $open = number_format($data5 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $open = number_format($data5 / 1000000000, 1) . 'B';
   }
  
   $data6 = $wpdb->get_var("SELECT SUM(nsent) FROM {$wpdb->prefix}wppenewsletter ");
   $waiting = ($data6 - $open);
  
  
?>
		<h3 class="wppe_title"><?php _e('WP Promo Emails Dashboard', 'wp-promo-emails'); ?></h3>
		<p><?php _e('Take a look at promotion reports.');?></p>
		<table class="wp-list-table widefat fixed media">
	<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column">
		<label>Promotions</label></th>
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
	<!-- Promotion Logs -->
		<h3 class="wppe_title"><?php _e('Promotions Logs', 'wp-promo-emails'); ?></h3>
<p><?php _e('Total Archive:', 'wp-promo-emails'); ?> <?php echo $nid;?> | <?php _e('Display: 12pp', 'wp-promo-emails'); ?></p>

<?php
// Resend HTML Email

  global $wpdb;
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

 
?>


		<form  name="wppe_resent" action="" method="POST">
	<input type="hidden" name="wppe_resend" value="<?php echo esc_attr($nid2);?>">
	<div class="holder"></div>
<div id="wppecontainer">
	
	<?php
	global $wpdb;
  $sql = "SELECT nid,ndate,nsubject,nfrom,nsent,nopen FROM {$wpdb->prefix}wppenewsletter ORDER by nid DESC";
  $results = $wpdb->get_results($sql);
  foreach ($results as $row) {
  $nid2 = $row->nid;
  $ndate = $row->ndate;
  $ndate = date('M j Y g:i A', strtotime($ndate));
  $nsub = $row->nsubject;
  $nfrom = $row->nfrom;
  $data7 = $row->nsent;
  if ($data7< 1000) {
    $nsent = number_format($data7);
  }elseif ($data7 < 1000000) {
    $nsent = number_format($data7 / 1000) . 'K';
  } else if ($data7 < 1000000000) {
    // Anything less than a billion
    $nsent = number_format($data7 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $nsent = number_format($data7 / 1000000000, 1) . 'B';
   }
  $data8 = $row->nopen;
   if ($data8< 1000) {
    $nopen = number_format($data8);
  }elseif ($data8 < 1000000) {
    $nopen = number_format($data8 / 1000) . 'K';
  } else if ($data8 < 1000000000) {
    // Anything less than a billion
    $nopen = number_format($data8 / 1000000, 1) . 'M';
  } else {
    // At least a billion
    $nopen = number_format($data8 / 1000000000, 1) . 'B';
   }
		
	
                echo "<div class='wppe_header'>";
		echo "<div class='wppe_col1'>" . $ndate . "</div>";
		echo "<div class='wppe_col2'>" . $nsub . "</div>";
		echo "<div class='wppe_col5'>"  . "Sent: "  . $nsent . "</div>";
		echo "<div class='wppe_col5'>"  . "Open: "  . $nopen . "</div>";
		echo "<div class='wppe_col4'>"  . "<button class='button-primary' id='wppe_submit' name='wppe_niddle' type='submit' value='$nid2'>" . "resend" . "       </button></div>";
		
		echo "</div>";
		
}

		?>
		</form>
</div>
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