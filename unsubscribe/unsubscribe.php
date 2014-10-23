<?php
$eemail_abspath = dirname(__FILE__);
$eemail_abspath_1 = str_replace('wp-content/plugins/wp-promo-emails/unsubscribe', '', $eemail_abspath);
$eemail_abspath_1 = str_replace('wp-content\plugins\wp-promo-emails\unsubscribe', '', $eemail_abspath_1);
require_once($eemail_abspath_1 .'wp-config.php');
$blogname = get_option('blogname');
$void = htmlspecialchars($_GET["member_0_0_0_0"]);
?>
<html>
<head>
<title><?php echo $blogname; ?></title>
</head>
<body>
<?php
global $wpdb;
$wpdb->query(
	"UPDATE $wpdb->users SET subscriber ='Z' WHERE user_email ='$void' LIMIT 1 ");
?>
<div style="background:#FFF;border:1px solid #ddd;border-radius:6px;max-width:580px;margin:0 auto;padding:34px 0 24px;width:580px;">
<h2>You will be missed</h2>
The following email address <strong>(<?php echo $void;?>)</strong> has been removed from our mailing list.
Thank for visiting with us. We hope you will come back soon and bring a friend.
            <br>
            <br>
            <br>
            Thanks,
            <br>
           <?php echo $blogname;?>
            </div>
</body>
</html>

