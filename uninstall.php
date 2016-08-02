<?php
if (!defined('WP_UNINSTALL_PLUGIN') && !defined('WP_CLI')) exit;

if (!defined('WC1C_PLUGIN_DIR')) define('WC1C_PLUGIN_DIR', __DIR__ . '/');
if (!defined('WC1C_DATA_DIR')) {
  $upload_dir = wp_upload_dir();
  define('WC1C_DATA_DIR', "{$upload_dir['basedir']}/woocommerce-1c/");
}

require WC1C_PLUGIN_DIR . "exchange.php";
wc1c_disable_time_limit();

global $wpdb;

if (is_dir(WC1C_DATA_DIR)) {
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(WC1C_DATA_DIR, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
  foreach ($iterator as $path => $item) {
    $item->isDir() ? rmdir($path) : unlink($path);
  }
  rmdir(WC1C_DATA_DIR);
}

// $term_meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->termmeta WHERE meta_key LIKE 'wc1c_%'");
// if ($term_meta_keys) {
//   foreach ($term_meta_keys as $term_meta_key) {
//     delete_woocommerce_term_meta(null, $term_meta_key, null, true);
//   }
// }
//
// $option_names = $wpdb->get_col("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'wc1c_%'");
// foreach ($option_names as $option_name) {
//   delete_option($option_name);
// }
//
// $post_meta_keys = $wpdb->get_col("SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '_wc1c_%' OR meta_key LIKE 'wc1c_%'");
// foreach ($post_meta_keys as $post_meta_key) {
//   delete_post_meta_by_key($post_meta_key);
// }

$index_table_names = array(
  $wpdb->postmeta,
  $wpdb->termmeta,
  $wpdb->usermeta,
);
foreach ($index_table_names as $index_table_name) {
  $index_name = 'wc1c_meta_key_meta_value';
  $result = $wpdb->get_var("SHOW INDEX FROM $index_table_name WHERE Key_name = '$index_name';");
  if (!$result) continue;

  $wpdb->query("DROP INDEX $index_name ON $index_table_name");
}
