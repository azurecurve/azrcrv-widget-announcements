<?php
// Check that code was called from ClassicPress with uninstallation constant declared
if (! defined('WP_UNINSTALL_PLUGIN')){
	exit;
}

// Options to remove
$options = array(
    'azrcrv-wa'
);

foreach ($options as $option){
	delete_option($option);
}