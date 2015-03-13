<?php
/*
Plugin Name: WooCommerce and 1C:Enterprise (1С:Предприятие) Data Exchange
Version: 0.7
Description: Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".
Author: Danil Semelenov
Author URI: mailto:mail@danil.mobi
Plugin URI: 
Text Domain: woocommerce-1c
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (!defined('__DIR__')) define('__DIR__', dirname(__FILE__));

define('WC1C_PLUGIN_DIR', __DIR__ . '/');
define('WC1C_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WC1C_PLUGIN_BASEDIR', dirname(WC1C_PLUGIN_BASENAME) . '/');
$upload_dir = wp_upload_dir();
define('WC1C_DATA_DIR', "{$upload_dir['basedir']}/woocommerce-1c/");

function wc1c_init() {
  if (!is_plugin_active("woocommerce/woocommerce.php")) {
    function wc1c_woocommerce_admin_notices() {
      $plugin_data = get_plugin_data(__FILE__);
      $message = sprintf(__("Plugin <strong>%s</strong> requires plugin <strong>WooCommerce</strong> to be installed and activated.", 'woocommerce-1c'), $plugin_data['Name']);
      printf('<div class="updated"><p>%s</p></div>', $message);
    }
    add_action('admin_notices', 'wc1c_woocommerce_admin_notices');
  }
}
add_action('init', 'wc1c_init');

function wc1c_plugins_loaded() {
  $plugin_data = get_plugin_data(__FILE__);
  $languages_dir = WC1C_PLUGIN_BASEDIR . $plugin_data['DomainPath'];
  load_plugin_textdomain('woocommerce-1c', false, $languages_dir);

  $revision = trim(str_replace('Revision', '', '$Revision$'), "$: ");
  define('WC1C_VERSION', sprintf("%sr%s", $plugin_data['Version'], $revision));
}
add_action('plugins_loaded', 'wc1c_plugins_loaded');

function wc1c_activate() {
  global $wpdb;

  $index_table_names = array(
    $wpdb->postmeta,
    "{$wpdb->prefix}woocommerce_termmeta",
    $wpdb->usermeta,
  );
  foreach ($index_table_names as $index_table_name) {
    $index_name = 'wc1c_meta_key_meta_value';
    $result = $wpdb->get_var("SHOW INDEX FROM $index_table_name WHERE Key_name = '$index_name';");
    if ($result) continue;

    $wpdb->query("ALTER TABLE $index_table_name ADD INDEX $index_name (meta_key, meta_value(36))");
  }

  if (!is_dir(WC1C_DATA_DIR)) mkdir(WC1C_DATA_DIR);
  file_put_contents(WC1C_DATA_DIR . ".htaccess", "Deny from all");
  file_put_contents(WC1C_DATA_DIR . "index.html", '');
}
register_activation_hook(__FILE__, 'wc1c_activate');

function wc1c_delete_term($term_id, $tt_id, $taxonomy, $deleted_term) {
  global $wpdb;

  if ($taxonomy != 'product_cat' && strpos($taxonomy, 'pa_') !== 0) return;

  $wpdb->delete($wpdb->woocommerce_termmeta, array('woocommerce_term_id' => $term_id));
  if (function_exists('wc1c_check_wpdb_error')) wc1c_check_wpdb_error();
}
add_action('delete_term', 'wc1c_delete_term', 10, 4);

function wc1c_woocommerce_attribute_by_id($attribute_id) {
  global $wpdb;

  $cache_key = "wc1c_woocomerce_attribute_by_id-$attribute_id";
  $attribute = wp_cache_get($cache_key);
  if ($attribute === false) {
    $attribute = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d", $attribute_id), ARRAY_A);
    if (function_exists('wc1c_check_wpdb_error')) wc1c_check_wpdb_error();

    if ($attribute) {
      $attribute['taxonomy'] = wc_attribute_taxonomy_name($attribute['attribute_name']);

      wp_cache_set($cache_key, $attribute);
    }
  }

  return $attribute;
}

function wc1c_delete_woocommerce_attribute($attribute_id) {
  global $wpdb;

  $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);

  if (!$attribute) return false;

  delete_option("{$attribute['taxonomy']}_children");

  $terms = get_terms($attribute['taxonomy'], "hide_empty=0");
  foreach ($terms as $term) {
    wp_delete_term($term->term_id, $attribute['taxonomy']);
  }

  $wpdb->delete("{$wpdb->prefix}woocommerce_attribute_taxonomies", compact('attribute_id'));
  if (function_exists('wc1c_check_wpdb_error')) wc1c_check_wpdb_error();
}

require_once WC1C_PLUGIN_DIR . "admin.php";
require_once WC1C_PLUGIN_DIR . "exchange.php";
