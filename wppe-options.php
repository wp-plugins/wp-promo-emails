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
   * Newsletter Records
   * Email Receipts Table
   * Last 5 Newsletters
   */
  
  global $wpdb;
  $url = site_url();
  
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
   
  $data2=$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}users");
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
   
  $data3 = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}users WHERE subscriber='Y'");
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
		echo "<td class='author'><br>emails</td>";
		echo "<td class='author'><br>emails</td>";
		echo "</tr>";
		?>
		
	</tbody>
</table>
	<!-- Last 5 Newsletters -->
		<h3 class="wppe_title"><?php _e('5 Lastest Sent Newsletters', 'wp-promo-emails'); ?></h3>
		<form  name="wppe_resent" action="" method="POST">
<table class="wp-list-table widefat fixed media">
	<tbody>
	<tr>
  <a href="http://cbfreeman.com/downloads/wp-promo-emails-premier/" border="0"><img src="<?php echo $url;?>/wp-content/plugins/wp-promo-emails/sample_data.png"></a>
  </tr>
	</tbody>
</table>
	<!-- dashboard ends -->
	<br>
	<!-- Support -->
	<div id="wppe_support">
		<h3><?php _e('Support & bug report', 'wp-promo-emails'); ?></h3>
		<p><?php printf(__('If you have any idea to improve this plugin or any bug to report, please email me at : <a href="%1$s">%2$s</a>', 'wp-promo-emails'), 'mailto:wppromoemails@cbfreeman.com?subject=[wp-promo-emails]', 'wppromoemails@cbfreeman.com'); ?></p>
		
	</div>
</div>