<?php
if (!defined('ABSPATH')) exit;

function wc1c_admin_init() {
  global $wc_product_attributes;

  if (!isset($wc_product_attributes)) return;

  $taxonomies = array_merge(array('product_cat'), array_keys($wc_product_attributes));
  foreach ($taxonomies as $taxonomy) {
    add_filter("manage_edit-{$taxonomy}_columns", 'wc1c_manage_edit_taxonomy_columns');
    add_filter("manage_{$taxonomy}_custom_column", 'wc1c_manage_taxonomy_custom_column', 10, 3);
  }
}
add_action('init', 'wc1c_admin_init');

function wc1c_manage_edit_taxonomy_columns($columns) {
  $columns_after = array(
    'wc1c_guid' => __("1C Id", 'woocommerce-1c'),
  );

  return array_merge($columns, $columns_after);
}

function wc1c_manage_taxonomy_custom_column($columns, $column, $id) {
  if ($column == 'wc1c_guid') {
    $guid = get_woocommerce_term_meta($id, 'wc1c_guid');
    list($taxonomy, $guid) = explode('::', $guid);
    $columns .= $guid ? "<small>$guid</small>" : '<span class="na">–</span>';
  }

  return $columns;
}

function wc1c_woocommerce_attribute_taxonomy_compare($a, $b) {
  foreach (array('a', 'b') as $arg) {
    $$arg = property_exists($$arg, 'wc1c_order') ? $$arg->wc1c_order : 1000 + $$arg->attribute_id;
  }

  if ($a == $b) return 0;
  return $a < $b ? -1 : 1;
}

function wc1c_woocommerce_attribute_taxonomies($attribute_taxonomies) {
  if (is_admin() && @$_GET['page'] == 'product_attributes') {
    $guids = get_option('wc1c_guid_attributes', array());
    $attribute_ids = array_values($guids);

    foreach ($attribute_taxonomies as $attribute_taxonomy) {
      $guid = array_search($attribute_taxonomy->attribute_id, $guids);
      if ($guid !== false) $attribute_taxonomy->attribute_label .= sprintf(" [%s: %s]", __("1C Id", 'woocommerce-1c'), $guid);
    }
  }

  $orders = get_option('wc1c_order_attributes', array());
  foreach ($attribute_taxonomies as $attribute_taxonomy) {
    $order = array_search($attribute_taxonomy->attribute_id, $orders);
    if ($order !== false) $attribute_taxonomy->wc1c_order = $order;
  }
  usort($attribute_taxonomies, 'wc1c_woocommerce_attribute_taxonomy_compare');

  return $attribute_taxonomies;
}
add_filter('woocommerce_attribute_taxonomies', 'wc1c_woocommerce_attribute_taxonomies');

function wc1c_manage_edit_product_columns($columns) {
  $columns_after = array(
    'wc1c_guid' => __("1C Id", 'woocommerce-1c'),
  );

  return array_merge($columns, $columns_after);
}
add_filter('manage_edit-product_columns', 'wc1c_manage_edit_product_columns');

function wc1c_manage_product_posts_custom_column($column) {
  global $post;

  if ($column == 'wc1c_guid') {
    $guid = get_post_meta($post->ID, '_wc1c_guid', true);
    echo $guid ? "<small>$guid</small>" : '<span class="na">–</span>';
  }
}
add_action('manage_product_posts_custom_column', 'wc1c_manage_product_posts_custom_column');

function wc1c_manage_edit_shop_order_columns($columns) {
  $columns_after = array(
    'wc1c_guid' => __("1C Id", 'woocommerce-1c'),
  );

  return array_merge($columns, $columns_after);
}
add_filter('manage_edit-shop_order_columns', 'wc1c_manage_edit_shop_order_columns');

function wc1c_manage_shop_order_posts_custom_column($column) {
  global $post;

  if ($column == 'wc1c_guid') {
    $guid = get_post_meta($post->ID, '_wc1c_guid', true);
    echo $guid ? "<small>$guid</small>" : '<span class="na">–</span>';
  }
}
add_action('manage_shop_order_posts_custom_column', 'wc1c_manage_shop_order_posts_custom_column');

function wc1c_woocommerce_attribute_deleted($attribute_id, $attribute_name, $taxonomy) {
  $guids = get_option('wc1c_guid_attributes', array());
  $guid = array_search($attribute_id, $guids);
  if ($guid === false) return;

  if (isset($guids[$guid])) {
    unset($guids[$guid]);
    update_option('wc1c_guid_attributes', $guids);
  }

  $timestamps = get_option('wc1c_timestamp_attributes', array());
  if (isset($timestamps[$guid])) {
    unset($timestamps[$guid]);
    update_option('wc1c_timestamp_attributes', $timestamps);
  }

  $orders = get_option('wc1c_order_attributes', array());
  $order_index = array_search($attribute_id, $orders);
  if ($order_index !== false) {
    unset($orders[$order_index]);
    update_option('wc1c_order_attributes', $orders);
  }
}
add_action('woocommerce_attribute_deleted', 'wc1c_woocommerce_attribute_deleted', 10, 3);

function wc1c_plugin_row_meta($plugin_meta, $plugin_file) {
  if ($plugin_file != WC1C_PLUGIN_BASENAME) return (array) $plugin_meta;

  $plugin_data = get_plugin_data(__FILE__);
  $plugin_meta_after = array(
    // 'support' => sprintf('<a href="%s" title="%s">%s</a>', esc_url("{$plugin_data['PluginURI']}support"), esc_attr(__("Request For Premium Customer Support", 'woocommerce-1c')), __("Premium Support", 'woocommerce-1c')),
    'donate' => sprintf('<a href="%s" title="%s" target="_blank">%s</a>', "https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=1000&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=", esc_attr(__("Say thank you to plugin author", 'woocommerce-1c')), __("Say thank you!", 'woocommerce-1c')),
  );

  return array_merge($plugin_meta, $plugin_meta_after);
}
//add_filter('plugin_row_meta', 'wc1c_plugin_row_meta', 10, 2);

function wc1c_plugin_action_links($actions) {
  $actions_before = array(
    // 'settings' => sprintf('<a href="%s" title="%s">%s</a>', admin_url("admin.php?page=woocommerce-1c"), esc_attr(__("View Settings", 'woocommerce-1c')), __("Settings", 'woocommerce-1c')),
    'donate' => sprintf('<a href="%s" title="%s" target="_blank">%s</a>', "https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=1000&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=", esc_attr(__("Say thank you to plugin author", 'woocommerce-1c')), __("Say thank you!", 'woocommerce-1c')),
  );

  return array_merge($actions_before, $actions);
}
add_filter('plugin_action_links_' . WC1C_PLUGIN_BASENAME, 'wc1c_plugin_action_links');

function wc1c_admin_menu() {
  add_menu_page(__("1C", 'woocommerce-1c'), __("1C", 'woocommerce-1c'), 'manage_woocommerce', 'woocommerce-1c', 'wc1c_admin_menu_page_settings', null, 100);
  add_submenu_page('woocommerce-1c', __("Settings", 'woocommerce-1c'), __("Settings", 'woocommerce-1c'), 'manage_woocommerce', 'woocommerce-1c');
  add_submenu_page('woocommerce-1c', __("TODO", 'woocommerce-1c'), __("TODO", 'woocommerce-1c'), 'manage_woocommerce', 'woocommerce-1c-todo', 'wc1c_admin_menu_page_todo');
}
//add_action('admin_menu', 'wc1c_admin_menu');

function wc1c_admin_menu_page_settings() {
  echo "TODO";
}

function wc1c_admin_menu_page_todo() {
  echo "TODO";
}
