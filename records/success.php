<?php
$eemail_abspath = dirname(__FILE__);
$eemail_abspath_1 = str_replace('wp-content/plugins/wp-promo-emails/records', '', $eemail_abspath);
$eemail_abspath_1 = str_replace('wp-content\plugins\wp-promo-emails\records', '', $eemail_abspath_1);
require_once($eemail_abspath_1 .'wp-config.php');
$blogname = get_option('blogname');
$url = $_SERVER['REQUEST_URI'] ;
$array = explode('=',$url);
$source = end($array);
$record = prev($array);
$subj = rawurldecode($source);
?>
<html>
<head>
<title><?php echo $blogname; ?></title>
</head>
<body>
<?php
global $wpdb;

$archive = $wpdb->get_var( "SELECT nopen FROM {$wpdb->prefix}wppenewsletter WHERE nsubject = '$subj'");
$store = ($archive + 1);

$wpdb->query("UPDATE {$wpdb->prefix}wpperecords SET ropen = 'ropen'+ 1  WHERE rsubject = '$subj' and remail = '$record'");

$wpdb->query("UPDATE {$wpdb->prefix}wppenewsletter SET nopen = '$store'  WHERE nsubject = '$subj'");

?>
</body>
</html>

