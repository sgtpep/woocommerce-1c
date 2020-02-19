<?php
if (!defined('ABSPATH')) exit;

require_once ABSPATH . "wp-admin/includes/media.php";
require_once ABSPATH . "wp-admin/includes/file.php";
require_once ABSPATH . "wp-admin/includes/image.php";

if (!defined('WC1C_PRODUCT_DESCRIPTION_TO_CONTENT')) define('WC1C_PRODUCT_DESCRIPTION_TO_CONTENT', false);
if (!defined('WC1C_PREVENT_CLEAN')) define('WC1C_PREVENT_CLEAN', false);

function wc1c_import_start_element_handler($is_full, $names, $depth, $name, $attrs) {
  global $wc1c_groups, $wc1c_group_depth, $wc1c_group_order, $wc1c_property, $wc1c_property_order, $wc1c_requisite_properties, $wc1c_product;

  if (!$depth && $name != 'КоммерческаяИнформация') {
    wc1c_error("XML parser misbehavior.");
  }
  elseif (@$names[$depth - 1] == 'Классификатор' && $name == 'Группы') {
    $wc1c_groups = array();
    $wc1c_group_depth = -1;
    $wc1c_group_order = 1;
  }
  elseif (@$names[$depth - 1] == 'Группы' && $name == 'Группа') {
    $wc1c_group_depth++;
    $wc1c_groups[] = array('ИдРодителя' => @$wc1c_groups[$wc1c_group_depth - 1]['Ид']);
  }
  elseif (@$names[$depth - 1] == 'Группа' && $name == 'Группы') {
    $result = wc1c_replace_group($is_full, $wc1c_groups[$wc1c_group_depth], $wc1c_group_order, $wc1c_groups);
    if ($result) $wc1c_group_order++;

    $wc1c_groups[$wc1c_group_depth]['Группы'] = true;
  }
  elseif (@$names[$depth - 1] == 'Классификатор' && $name == 'Свойства') {
    $wc1c_property_order = 1;
    $wc1c_requisite_properties = array();
  }
  elseif (@$names[$depth - 1] == 'Свойства' && $name == 'Свойство') {
    $wc1c_property = array();
  }
  elseif (@$names[$depth - 1] == 'Свойство' && $name == 'ВариантыЗначений') {
    $wc1c_property['ВариантыЗначений'] = array();
  }
  elseif (@$names[$depth - 1] == 'ВариантыЗначений' && $name == 'Справочник') {
    $wc1c_property['ВариантыЗначений'][] = array();
  }
  elseif (@$names[$depth - 1] == 'Товары' && $name == 'Товар') {
    $wc1c_product = array(
      'ХарактеристикиТовара' => array(),
      'ЗначенияСвойств' => array(),
      'ЗначенияРеквизитов' => array(),
    );
  }
  elseif (@$names[$depth - 1] == 'Товар' && $name == 'Группы') {
    $wc1c_product['Группы'] = array();
  }
  elseif (@$names[$depth - 1] == 'Группы' && $name == 'Ид') {
    $wc1c_product['Группы'][] = '';
  }
  elseif (@$names[$depth - 1] == 'Товар' && $name == 'Картинка') {
    if (!isset($wc1c_product['Картинка'])) $wc1c_product['Картинка'] = array();
    $wc1c_product['Картинка'][] = '';
  }
  elseif (@$names[$depth - 1] == 'Товар' && $name == 'Изготовитель') {
    $wc1c_product['Изготовитель'] = array();
  }
  elseif (@$names[$depth - 1] == 'ХарактеристикиТовара' && $name == 'ХарактеристикаТовара') {
    $wc1c_product['ХарактеристикиТовара'][] = array();
  }
  elseif (@$names[$depth - 1] == 'ЗначенияСвойств' && $name == 'ЗначенияСвойства') {
    $wc1c_product['ЗначенияСвойств'][] = array();
  }
  elseif (@$names[$depth - 1] == 'ЗначенияСвойства' && $name == 'Значение') {
    $i = count($wc1c_product['ЗначенияСвойств']) - 1;
    if (!isset($wc1c_product['ЗначенияСвойств'][$i]['Значение'])) $wc1c_product['ЗначенияСвойств'][$i]['Значение'] = array();
    $wc1c_product['ЗначенияСвойств'][$i]['Значение'][] = '';
  }
  elseif (@$names[$depth - 1] == 'ЗначенияРеквизитов' && $name == 'ЗначениеРеквизита') {
    $wc1c_product['ЗначенияРеквизитов'][] = array();
  }
  elseif (@$names[$depth - 1] == 'ЗначениеРеквизита' && $name == 'Значение') {
    $i = count($wc1c_product['ЗначенияРеквизитов']) - 1;
    if (!isset($wc1c_product['ЗначенияРеквизитов'][$i]['Значение'])) $wc1c_product['ЗначенияРеквизитов'][$i]['Значение'] = array();
    $wc1c_product['ЗначенияРеквизитов'][$i]['Значение'][] = '';
  }
}

function wc1c_import_character_data_handler($is_full, $names, $depth, $name, $data) {
  global $wc1c_groups, $wc1c_group_depth, $wc1c_property, $wc1c_product;

  if (@$names[$depth - 2] == 'Группы' && @$names[$depth - 1] == 'Группа' && $name != 'Группы') {
    @$wc1c_groups[$wc1c_group_depth][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Свойства' && @$names[$depth - 1] == 'Свойство' && $name != 'ВариантыЗначений') {
    @$wc1c_property[$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'ХарактеристикиТовара' && @$names[$depth - 1] == 'ХарактеристикаТовара') {
    $i = count($wc1c_product['ХарактеристикиТовара']) - 1;
    @$wc1c_product['ХарактеристикиТовара'][$i][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'ВариантыЗначений' && @$names[$depth - 1] == 'Справочник') {
    $i = count($wc1c_property['ВариантыЗначений']) - 1;
    @$wc1c_property['ВариантыЗначений'][$i][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Товары' && @$names[$depth - 1] == 'Товар' && !in_array($name, array('Группы', 'Картинка', 'Изготовитель', 'ХарактеристикиТовара', 'ЗначенияСвойств', 'СтавкиНалогов', 'ЗначенияРеквизитов'))) {
    @$wc1c_product[$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'БазоваяЕдиница' && @$names[$depth - 1] == 'Пересчет') {
    @$wc1c_product['Пересчет'][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Товар' && @$names[$depth - 1] == 'Группы' && $name == 'Ид') {
    $i = count($wc1c_product['Группы']) - 1;
    @$wc1c_product['Группы'][$i] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Товары' && @$names[$depth - 1] == 'Товар' && $name == 'Картинка') {
    $i = count($wc1c_product['Картинка']) - 1;
    @$wc1c_product['Картинка'][$i] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Товар' && @$names[$depth - 1] == 'Изготовитель') {
    @$wc1c_product['Изготовитель'][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'ЗначенияСвойств' && @$names[$depth - 1] == 'ЗначенияСвойства') {
    $i = count($wc1c_product['ЗначенияСвойств']) - 1;
    if ($name != 'Значение') {
      @$wc1c_product['ЗначенияСвойств'][$i][$name] .= $data;
    }
    else {
      $j = count($wc1c_product['ЗначенияСвойств'][$i]['Значение']) - 1;
      @$wc1c_product['ЗначенияСвойств'][$i]['Значение'][$j] .= $data;
    }
  }
  elseif (@$names[$depth - 2] == 'ЗначенияРеквизитов' && @$names[$depth - 1] == 'ЗначениеРеквизита') {
    $i = count($wc1c_product['ЗначенияРеквизитов']) - 1;
    if ($name != 'Значение') {
      @$wc1c_product['ЗначенияРеквизитов'][$i][$name] .= $data;
    }
    else {
      $j = count($wc1c_product['ЗначенияРеквизитов'][$i]['Значение']) - 1;
      @$wc1c_product['ЗначенияРеквизитов'][$i]['Значение'][$j] .= $data;
    }
  }
}

function wc1c_import_end_element_handler($is_full, $names, $depth, $name) {
  global $wpdb, $wc1c_groups, $wc1c_group_depth, $wc1c_group_order, $wc1c_property, $wc1c_property_order, $wc1c_requisite_properties, $wc1c_product, $wc1c_subproducts;

  if (@$names[$depth - 1] == 'Группы' && $name == 'Группа') {
    if (empty($wc1c_groups[$wc1c_group_depth]['Группы'])) {
      $result = wc1c_replace_group($is_full, $wc1c_groups[$wc1c_group_depth], $wc1c_group_order, $wc1c_groups);
      if ($result) $wc1c_group_order++;
    }

    array_pop($wc1c_groups);
    $wc1c_group_depth--;
  }
  if (@$names[$depth - 1] == 'Классификатор' && $name == 'Группы') {
    wc1c_clean_woocommerce_categories($is_full);
  }
  elseif (@$names[$depth - 1] == 'Свойства' && $name == 'Свойство') {
    $result = wc1c_replace_property($is_full, $wc1c_property, $wc1c_property_order);
    if ($result) {
      $attribute_taxonomy = $result;
      $wc1c_property_order++;

      wc1c_clean_woocommerce_attribute_options($is_full, $attribute_taxonomy);
    }
    else {
      $wc1c_requisite_properties[$wc1c_property['Ид']] = $wc1c_property;
    }
  }
  elseif (@$names[$depth - 1] == 'Классификатор' && $name == 'Свойства') {
    wc1c_clean_woocommerce_attributes($is_full);

    delete_transient('wc_attribute_taxonomies');
  }
  elseif (@$names[$depth - 1] == 'Товары' && $name == 'Товар') {
    if ($wc1c_requisite_properties) {
      foreach ($wc1c_product['ЗначенияСвойств'] as $product_property) {
        if (!array_key_exists($product_property['Ид'], $wc1c_requisite_properties)) continue;

        $property = $wc1c_requisite_properties[$product_property['Ид']];
        $wc1c_product['ЗначенияРеквизитов'][] = array(
          'Наименование' => $property['Наименование'],
          'Значение' => $product_property['Значение'],
        );
      }
    }

    if (strpos($wc1c_product['Ид'], '#') === false || WC1C_DISABLE_VARIATIONS) {
      $guid = $wc1c_product['Ид'];
      wc1c_replace_product($is_full, $guid, $wc1c_product);
      $_post_id = wc1c_replace_product($is_full, $guid, $wc1c_product);
      if ($_post_id) {
        $_product = wc_get_product($_post_id);
        $_qnty = $_product->get_stock_quantity();
        if (!$_qnty) {
          update_post_meta($_post_id, '_stock_status', WC1C_OUTOFSTOCK_STATUS);
        }
        unset($_product, $_qnty);
      }
      unset($_post_id);
    }
    else {
      $guid = $wc1c_product['Ид'];
      list($product_guid, ) = explode('#', $guid, 2);

      if (empty($wc1c_subproducts) || $wc1c_subproducts[0]['product_guid'] != $product_guid) {
        if ($wc1c_subproducts) wc1c_replace_subproducts($is_full, $wc1c_subproducts);
        $wc1c_subproducts = array();
      }

      $wc1c_subproducts[] = array(
        'guid' => $wc1c_product['Ид'],
        'product_guid' => $product_guid,
        'characteristics' => $wc1c_product['ХарактеристикиТовара'],
        'is_full' => $is_full,
        'product' => $wc1c_product,
      );
    }
  }
  elseif (@$names[$depth - 1] == 'Каталог' && $name == 'Товары') {
    if ($wc1c_subproducts) wc1c_replace_subproducts($is_full, $wc1c_subproducts);

    wc1c_clean_products($is_full);
    wc1c_clean_product_terms();
  }
  elseif (!$depth && $name == 'КоммерческаяИнформация') {
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
    wc1c_check_wpdb_error();

    do_action('wc1c_post_import', $is_full);
  }
}

function wc1c_term_id_by_meta($key, $value) {
  global $wpdb;

  if ($value === null) return;

  $cache_key = "wc1c_term_id_by_meta-$key-$value";
  $term_id = wp_cache_get($cache_key);
  if ($term_id === false) {
    $term_id = $wpdb->get_var($wpdb->prepare("SELECT tm.term_id FROM $wpdb->termmeta tm JOIN $wpdb->terms t ON tm.term_id = t.term_id WHERE meta_key = %s AND meta_value = %s", $key, $value));
    wc1c_check_wpdb_error();

    if ($term_id) wp_cache_set($cache_key, $term_id);
  }

  return $term_id;
}

function wc1c_unique_term_name($name, $taxonomy, $parent = null) {
  global $wpdb;

  $name = htmlspecialchars($name);

  $sql = "SELECT * FROM $wpdb->terms NATURAL JOIN $wpdb->term_taxonomy WHERE name = %s AND taxonomy = %s AND parent = %d LIMIT 1";
  if (!$parent) $parent = 0;
  $term = $wpdb->get_row($wpdb->prepare($sql, $name, $taxonomy, $parent));
  wc1c_check_wpdb_error();
  if (!$term) return $name;

  $number = 2;
  while (true) {
    $new_name = "$name ($number)";
    $number++;

    $term = $wpdb->get_row($wpdb->prepare($sql, $new_name, $taxonomy, $parent));
    wc1c_check_wpdb_error();
    if (!$term) return $new_name;
  }
}

function wc1c_unique_term_slug($slug, $taxonomy, $parent = null) {
  global $wpdb;

  while (true) {
    $sanitized_slug = sanitize_title($slug);
    if (strlen($sanitized_slug) <= 195) break;

    $slug = mb_substr($slug, 0, mb_strlen($slug) - 3);
  }

  $sql = "SELECT * FROM $wpdb->terms NATURAL JOIN $wpdb->term_taxonomy WHERE slug = %s AND taxonomy = %s AND parent = %d LIMIT 1";
  if (!$parent) $parent = 0;
  $term = $wpdb->get_row($wpdb->prepare($sql, $sanitized_slug, $taxonomy, $parent));
  wc1c_check_wpdb_error();
  if (!$term) return $slug;

  $number = 2;
  while (true) {
    $new_slug = "$slug-$number";
    $new_sanitized_slug = "$sanitized_slug-$number";
    $number++;

    $term = $wpdb->get_row($wpdb->prepare($sql, $new_sanitized_slug, $taxonomy, $parent));
    wc1c_check_wpdb_error();
    if (!$term) return $new_slug;
  }
}

function wc1c_wp_unique_term_slug($slug, $term, $original_slug) {
  if (mb_strlen($slug) <= 200) return $slug;

  do {
    $slug = urldecode($slug);
    $slug = mb_substr($slug, 0, mb_strlen($slug) - 1);
    $slug = urlencode($slug);
    $slug = wp_unique_term_slug($slug, $term);
  }
  while (mb_strlen($slug) > 200);

  return $slug;
}
add_filter('wp_unique_term_slug', 'wc1c_wp_unique_term_slug', 10, 3);

function wc1c_replace_term($is_full, $guid, $parent_guid, $name, $taxonomy, $order, $use_guid_as_slug = false) {
  global $wpdb;

  $term_id = wc1c_term_id_by_meta('wc1c_guid', "$taxonomy::$guid");
  if ($term_id) $term = get_term($term_id, $taxonomy);

  $parent = $parent_guid ? wc1c_term_id_by_meta('wc1c_guid', "$taxonomy::$parent_guid") : null;

  if (!$term_id || !$term) {
    $name = wc1c_unique_term_name($name, $taxonomy, $parent);
    $slug = wc1c_unique_term_slug($name, $taxonomy, $parent);
    $args = array(
      'slug' => $slug,
      'parent' => $parent,
    );
    if ($use_guid_as_slug) $args['slug'] = $guid;
    $result = wp_insert_term($name, $taxonomy, $args);
    wc1c_check_wpdb_error();
    wc1c_check_wp_error($result);

    $term_id = $result['term_id'];
    update_term_meta($term_id, 'wc1c_guid', "$taxonomy::$guid");

    $is_added = true;
  }

  if (empty($is_added)) {
    if (trim($name) != $term->name) $name = wc1c_unique_term_name($name, $taxonomy, $parent);
    $parent = $parent_guid ? wc1c_term_id_by_meta('wc1c_guid', "$taxonomy::$parent_guid") : null;
    $args = array(
      'name' => $name,
      'parent' => $parent,
    );

    $result = wp_update_term($term_id, $taxonomy, $args);
    wc1c_check_wp_error($result);
  }

  if ($is_full) wc_set_term_order($term_id, $order, $taxonomy);

  update_term_meta($term_id, 'wc1c_timestamp', WC1C_TIMESTAMP);
}

function wc1c_replace_group($is_full, $group, $order, $groups) {
  $parent_groups = array_slice($groups, 0, -1);
  $group = apply_filters('wc1c_import_group_xml', $group, $parent_groups, $is_full);
  if (!$group) return;

  $group_name = isset($group['Наименование']) ? $group['Наименование'] : $group['Ид'];
  wc1c_replace_term($is_full, $group['Ид'], $group['ИдРодителя'], $group_name, 'product_cat', $order);

  return true;
}

function wc1c_unique_woocommerce_attribute_name($attribute_label) {
  global $wpdb;

  $attribute_name = wc_sanitize_taxonomy_name($attribute_label);
  $max_length = 32 - strlen('pa_') - strlen('-00');
  while (strlen($attribute_name) > $max_length) {
    $attribute_name = mb_substr($attribute_name, 0, mb_strlen($attribute_name) - 1);
  }

  $sql = "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s";
  $attribute = $wpdb->get_row($wpdb->prepare($sql, $attribute_name));
  wc1c_check_wpdb_error();
  if (!$attribute) return $attribute_name;

  $number = 2;
  while (true) {
    $new_attribute_name = "$attribute_name-$number";
    $number++;

    $attribute = $wpdb->get_row($wpdb->prepare($sql, $new_attribute_name));
    if (!$attribute) return $new_attribute_name;
  }
}

function wc1c_replace_woocommerce_attribute($is_full, $guid, $attribute_label, $attribute_type, $order, $preserve_fields) {
  global $wpdb;

  $guids = get_option('wc1c_guid_attributes', array());
  $attribute_id = @$guids[$guid];

  if ($attribute_id) {
    $attribute_id = $wpdb->get_var($wpdb->prepare("SELECT attribute_id FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d", $attribute_id));
    wc1c_check_wpdb_error();
  }

  $data = compact('attribute_label', 'attribute_type');

  if (!$attribute_id) {
    $attribute_name = wc1c_unique_woocommerce_attribute_name($attribute_label);
    $data = array_merge($data, array(
      'attribute_name' => $attribute_name,
      'attribute_orderby' => 'menu_order',
    ));
    $wpdb->insert("{$wpdb->prefix}woocommerce_attribute_taxonomies", $data);
    wc1c_check_wpdb_error();

    $attribute_id = $wpdb->insert_id;
    $is_added = true;

    $guids[$guid] = $attribute_id;
    update_option('wc1c_guid_attributes', $guids);
  }

  if (empty($is_added)) {
    if (in_array('label', $preserve_fields)) unset($data['attribute_label']);
    if (in_array('type', $preserve_fields)) unset($data['attribute_type']);

    $wpdb->update("{$wpdb->prefix}woocommerce_attribute_taxonomies", $data, compact('attribute_id'));
    wc1c_check_wpdb_error();
  }

  if ($is_full) {
    $orders = get_option('wc1c_order_attributes', array());
    $order_index = array_search($attribute_id, $orders) or 0;
    if ($order_index !== false) unset($orders[$order_index]);
    array_splice($orders, $order, 0, $attribute_id);
    update_option('wc1c_order_attributes', $orders);
  }

  $timestamps = get_option('wc1c_timestamp_attributes', array());
  $timestamps[$guid] = WC1C_TIMESTAMP;
  update_option('wc1c_timestamp_attributes', $timestamps);

  return $attribute_id;
}

function wc1c_replace_property_option($property_option, $attribute_taxonomy, $order) {
  if (!isset($property_option['ИдЗначения'], $property_option['Значение'])) return;

  wc1c_replace_term(true, $property_option['ИдЗначения'], null, $property_option['Значение'], $attribute_taxonomy, $order, true);
}

function wc1c_replace_property($is_full, $property, $order) {
  $property = apply_filters('wc1c_import_property_xml', $property, $is_full);
  if (!$property) return;

  $preserve_fields = apply_filters('wc1c_import_preserve_property_fields', array(), $property, $is_full);

  $attribute_name = !empty($property['Наименование']) ? $property['Наименование'] : $property['Ид'];
  $attribute_type = (empty($property['ТипЗначений']) || $property['ТипЗначений'] == 'Справочник' || defined('WC1C_MULTIPLE_VALUES_DELIMETER')) ? 'select' : 'text';
  $attribute_id = wc1c_replace_woocommerce_attribute($is_full, $property['Ид'], $attribute_name, $attribute_type, $order, $preserve_fields);

  $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
  if (!$attribute) wc1c_error("Failed to get attribute");

  register_taxonomy($attribute['taxonomy'], null);

  if ($attribute_type == 'select' && !empty($property['ВариантыЗначений'])) {
    foreach ($property['ВариантыЗначений'] as $i => $property_option) {
      wc1c_replace_property_option($property_option, $attribute['taxonomy'], $i + 1);
    }
  }

  return $attribute['taxonomy'];
}

function wc1c_replace_post($guid, $post_type, $is_deleted, $is_draft, $post_title, $post_name, $post_excerpt, $post_content, $post_meta, $category_taxonomy, $category_guids, $preserve_fields) {
  $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);

  if (!$post_excerpt) $post_excerpt = '';
  if (WC1C_PRODUCT_DESCRIPTION_TO_CONTENT) {
    $post_content = $post_excerpt;
    $post_excerpt = '';
  }

  $args = compact('post_type', 'post_title', 'post_excerpt', 'post_content');

  if (!$post_id) {
    $args = array_merge($args, array(
      'post_name' => $post_name,
      'post_status' => $is_draft ? 'draft' : 'publish',
    ));
    $post_id = wp_insert_post($args, true);
    wc1c_check_wpdb_error();
    wc1c_check_wp_error($post_id);

    update_post_meta($post_id, '_visibility', 'visible');
    update_post_meta($post_id, '_wc1c_guid', $guid);

    $is_added = true;
  }
  else {
    $is_added = false;
  }

  $post = get_post($post_id);
  if (!$post) wc1c_error("Failed to get post");

  if (!$is_added) {
    if (in_array('title', $preserve_fields)) unset($args['post_title']);
    if (in_array('excerpt', $preserve_fields)) unset($args['post_excerpt']);
    if (in_array('body', $preserve_fields)) unset($args['post_content']);

    foreach ($args as $key => $value) {
      if ($post->$key == $value) continue;

      $is_changed = true;
      break;
    }

    if (!empty($is_changed)) {
      $post_date = current_time('mysql');
      $args = array_merge($args, array(
        'ID' => $post_id,
        'post_date' => $post_date,
        'post_date_gmt' => get_gmt_from_date($post_date),
      ));
      $post_id = wp_update_post($args, true);
      wc1c_check_wp_error($post_id);
    }
  }

  if ($is_deleted && $post->post_status != 'trash') {
    wp_trash_post($post_id);
  }
  elseif (!$is_deleted && $post->post_status == 'trash') {
    wp_untrash_post($post_id);
  }

  $current_post_meta = get_post_meta($post_id);
  foreach ($current_post_meta as $meta_key => $meta_value) {
    $current_post_meta[$meta_key] = $meta_value[0];
  }

  foreach ($post_meta as $meta_key => $meta_value) {
    $current_meta_value = @$current_post_meta[$meta_key];
    if ($current_meta_value == $meta_value) continue;

    update_post_meta($post_id, $meta_key, $meta_value);
  }

  if (!in_array('categories', $preserve_fields)) {
    $current_category_ids = wp_get_post_terms($post_id, $category_taxonomy, "fields=ids");
    wc1c_check_wp_error($current_category_ids);

    $category_ids = array();
    if ($category_guids) {
      foreach ($category_guids as $category_guid) {
        $category_id = wc1c_term_id_by_meta('wc1c_guid', "product_cat::$category_guid");
        if ($category_id) $category_ids[] = $category_id;
      }
    }

    sort($current_category_ids);
    sort($category_ids);
    if ($current_category_ids != $category_ids) {
      $result = wp_set_post_terms($post_id, $category_ids, $category_taxonomy);
      wc1c_check_wp_error($result);
    }
  }

  update_post_meta($post_id, '_wc1c_timestamp', WC1C_TIMESTAMP);

  return array($is_added, $post_id, $current_post_meta);
}

function wc1c_replace_post_attachments($post_id, $attachments) {
  $data_dir = WC1C_DATA_DIR . "catalog";

  $attachment_path_by_hash = array();
  foreach ($attachments as $attachment_path => $attachment) {
    $attachment_path = "$data_dir/$attachment_path";
    if (!file_exists($attachment_path)) continue;

    $attachment_hash = basename($attachment_path) . md5_file($attachment_path);
    $attachment_path_by_hash[$attachment_hash] = $attachment_path;
  }
  $attachment_hash_by_path = array_flip($attachment_path_by_hash);

  $post_attachments = get_attached_media('image', $post_id);
  $post_attachment_id_by_hash = array();
  foreach ($post_attachments as $post_attachment) {
    $post_attachment_path = get_attached_file($post_attachment->ID, true);
    if (file_exists($post_attachment_path)) {
      $post_attachment_hash = basename($post_attachment_path) . md5_file($post_attachment_path);
      $post_attachment_id_by_hash[$post_attachment_hash] = $post_attachment->ID;
      if (isset($attachment_path_by_hash[$post_attachment_hash])) {
        unset($attachment_path_by_hash[$post_attachment_hash]);
        continue;
      }
    }

    $result = wp_delete_attachment($post_attachment->ID);
    if ($result === false) wc1c_error("Failed to delete post attachment");
  }

  $attachment_ids = array();
  foreach ($attachments as $attachment_path => $attachment) {
    $attachment_path = "$data_dir/$attachment_path";
    if (!file_exists($attachment_path)) continue;

    $attachment_hash = $attachment_hash_by_path[$attachment_path];
    $attachment_id = @$post_attachment_id_by_hash[$attachment_hash];
    if (!$attachment_id) {
      $file = array(
        'tmp_name' => $attachment_path,
        'name' => basename($attachment_path),
      );
      $attachment_id = @media_handle_sideload($file, $post_id, @$attachment['description']);
      wc1c_check_wp_error($attachment_id);
      
      $uploaded_attachment_path = get_attached_file($attachment_id);
      if ($uploaded_attachment_path) copy($uploaded_attachment_path, $attachment_path);
    }

    $attachment_ids[] = $attachment_id;
  }

  return $attachment_ids;
}

function wc1c_replace_requisite_name_callback($matches) {
  return ' ' . mb_convert_case($matches[0], MB_CASE_LOWER, "UTF-8");
}

function wc1c_replace_product($is_full, $guid, $product) {
  global $wc1c_is_moysklad;

  $product = apply_filters('wc1c_import_product_xml', $product, $is_full);
  if (!$product) return;

  $preserve_fields = apply_filters('wc1c_import_preserve_product_fields', array(), $product, $is_full);

  $is_deleted = @$product['Статус'] == 'Удален';
  $is_draft = @$product['Статус'] == 'Черновик';

  $post_title = @$product['Наименование'];
  if (!$post_title) return;

  $post_content = '';

  $post_meta = array(
    '_sku' => @$product['Артикул'],
    '_manage_stock' => 'yes',
  );

  foreach ($product['ЗначенияРеквизитов'] as $i => $requisite) {
    $value = @$requisite['Значение'][0];
	if (!$value) continue;
    if ($requisite['Наименование'] == "Полное наименование") {
      if ($wc1c_is_moysklad) $post_content = $value;
      else $post_title = $value;
      unset($product['ЗначенияРеквизитов'][$i]);
    }
    elseif ($requisite['Наименование'] == "ОписаниеВФорматеHTML") {
      $post_content = $value;
      unset($product['ЗначенияРеквизитов'][$i]);
    }
    elseif ($requisite['Наименование'] == "Длина") {
      $post_meta['_length'] = floatval($value);
      unset($product['ЗначенияРеквизитов'][$i]);
    }
    elseif ($requisite['Наименование'] == "Ширина") {
      $post_meta['_width'] = floatval($value);
      unset($product['ЗначенияРеквизитов'][$i]);
    }
    elseif ($requisite['Наименование'] == "Высота") {
      $post_meta['_height'] = floatval($value);
      unset($product['ЗначенияРеквизитов'][$i]);
    }
    elseif ($requisite['Наименование'] == "Вес") {
      $post_meta['_weight'] = floatval($value);
      unset($product['ЗначенияРеквизитов'][$i]);
    }
  }

  $post_name = sanitize_title($post_title);
  $post_name = apply_filters('wc1c_import_product_slug', $post_name, $product, $is_full);

  $description = isset($product['Описание']) ? $product['Описание'] : '';
  list($is_added, $post_id, $post_meta) = wc1c_replace_post($guid, 'product', $is_deleted, $is_draft, $post_title, $post_name, $description, $post_content, $post_meta, 'product_cat', @$product['Группы'], $preserve_fields);

  // if (isset($product['Пересчет']['Единица'])) {
  //   $quantity = wc1c_parse_decimal($product['Пересчет']['Единица']);
  //   if (isset($product['Пересчет']['Коэффициент'])) $quantity *= wc1c_parse_decimal($product['Пересчет']['Коэффициент']);
  //   wc_update_product_stock($post_id, $quantity);
  //
  //   $stock_status = $quantity > 0 ? 'instock' : WC1C_OUTOFSTOCK_STATUS;
  //   wc_update_product_stock_status($post_id, $stock_status);
  // }

  $current_product_attributes = isset($post_meta['_product_attributes']) ? maybe_unserialize($post_meta['_product_attributes']) : array();

  $current_product_attribute_variations = array(); 
  foreach ($current_product_attributes as $current_product_attribute_key => $current_product_attribute) {
    if (!$current_product_attribute['is_variation']) continue;

    unset($current_product_attributes[$current_product_attribute_key]);
    $current_product_attribute_variations[$current_product_attribute_key] = $current_product_attribute;
  }

  $product_attributes = array();

  $product_attribute_values = array();
  if (!empty($product['Изготовитель']['Наименование'])) $product_attribute_values["Наименование изготовителя"] = $product['Изготовитель']['Наименование'];
  if (!empty($product['БазоваяЕдиница']) && trim($product['БазоваяЕдиница'])) $product_attribute_values["Базовая единица"] = trim($product['БазоваяЕдиница']);

  foreach ($product_attribute_values as $product_attribute_name => $product_attribute_value) {
    $product_attribute_key = sanitize_title($product_attribute_name);
    $product_attribute_position = count($product_attributes);
    $product_attributes[$product_attribute_key] = array(
      'name' => wc_clean($product_attribute_name),
      'value' => $product_attribute_value,
      'position' => $product_attribute_position,
      'is_visible' => 0,
      'is_variation' => 0,
      'is_taxonomy' => 0,
    );
  }

  if ($product['ЗначенияСвойств']) {
    $attribute_guids = get_option('wc1c_guid_attributes', array());
    $terms = array();
    foreach ($product['ЗначенияСвойств'] as $property) {
      $attribute_guid = $property['Ид'];
      $attribute_id = @$attribute_guids[$attribute_guid];
      if (!$attribute_id) continue;

      $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
      if (!$attribute) wc1c_error("Failed to get attribute");

      $attribute_terms = array();
      $attribute_values = array();
      $property_values = @$property['Значение'];
      if ($property_values) {
        foreach ($property_values as $property_value) {
          if (!$property_value) continue;

          if ($attribute['attribute_type'] == 'select' && preg_match("/^\w+-\w+-\w+-\w+-\w+$/", $property_value)) {
            $term_id = wc1c_term_id_by_meta('wc1c_guid', "{$attribute['taxonomy']}::$property_value");
            if ($term_id) $attribute_terms[] = (int) $term_id;
          }
          else {
            if (!defined('WC1C_MULTIPLE_VALUES_DELIMETER')) {
              $attribute_values[] = $property_value;
            }
            else {
              $term_names = explode(WC1C_MULTIPLE_VALUES_DELIMETER, $property_value);
              $term_names = array_map('trim', $term_names);
              foreach ($term_names as $term_name) {
                $result = get_term_by('name', $term_name, $attribute['taxonomy'], ARRAY_A);
                if (!$result) {
                  $slug = wc1c_unique_term_slug($term_name, $attribute['taxonomy']);
                  $args = array(
                    'slug' => $slug,
                  );
                  $result = wp_insert_term($term_name, $attribute['taxonomy'], $args);
                  wc1c_check_wpdb_error();
                  wc1c_check_wp_error($result);
                }
                $attribute_terms[] = $result['term_id'];
              }
            }
          }
        }
      }

      if ($attribute_terms || $attribute_values) {
        $product_attribute = array(
          'name' => null,
          'value' => '',
          'position' => count($product_attributes),
          'is_visible' => 1,
          'is_variation' => 0,
          'is_taxonomy' => 0,
        );

        if ($attribute_terms) {
          $product_attribute['name'] = $attribute['taxonomy'];
          $product_attribute['is_taxonomy'] = 1;
        }
        elseif ($attribute_values) {
          $product_attribute['name'] = $attribute['attribute_label'];
          $product_attribute['value'] = implode(" | ", $attribute_values);
        }

        $product_attribute_key = sanitize_title($attribute['taxonomy']);
        $product_attributes[$product_attribute_key] = $product_attribute;
      }

      if ($attribute_terms) {
        if (!isset($terms[$attribute['taxonomy']])) $terms[$attribute['taxonomy']] = array();
        $terms[$attribute['taxonomy']] = array_merge($terms[$attribute['taxonomy']], $attribute_terms);
      }
    }

    foreach ($terms as $attribute_taxonomy => $attribute_terms) {
      register_taxonomy($attribute_taxonomy, null);
      $result = wp_set_post_terms($post_id, $attribute_terms, $attribute_taxonomy);
      wc1c_check_wp_error($result);
    }
  }

  foreach ($product['ЗначенияРеквизитов'] as $requisite) {
    $attribute_values = @$requisite['Значение'];
    if (!$attribute_values) continue;
    if (strpos($attribute_values[0], "import_files/") === 0) continue;

    $requisite_name = $requisite['Наименование'];
    $product_attribute_name = strpos($requisite_name, ' ') === false ? preg_replace_callback("/(?<!^)\p{Lu}/u", 'wc1c_replace_requisite_name_callback', $requisite_name) : $requisite_name;
    $product_attribute_key = sanitize_title($requisite_name);
    $product_attribute_position = count($product_attributes);
    $product_attributes[$product_attribute_key] = array(
      'name' => wc_clean($product_attribute_name),
      'value' => implode(" | ", $attribute_values),
      'position' => $product_attribute_position,
      'is_visible' => 0,
      'is_variation' => 0,
      'is_taxonomy' => 0,
    );
  }

  foreach ($product['ХарактеристикиТовара'] as $characteristic) {
    $attribute_value = @$characteristic['Значение'];
    if (!$attribute_value) continue;

    $product_attribute_name = $characteristic['Наименование'];
    $product_attribute_key = sanitize_title($product_attribute_name);
    $product_attribute_position = count($product_attributes);
    $product_attributes[$product_attribute_key] = array(
      'name' => wc_clean($product_attribute_name),
      'value' => $attribute_value,
      'position' => $product_attribute_position,
      'is_visible' => 1,
      'is_variation' => 0,
      'is_taxonomy' => 0,
    );
  }

  if (!in_array('attributes', $preserve_fields)) {
    $old_product_attributes = array_diff_key($current_product_attributes, $product_attributes);
    $old_taxonomies = array();
    foreach ($old_product_attributes as $old_product_attribute) {
      if ($old_product_attribute['is_taxonomy']) {
        $old_taxonomies[] = $old_product_attribute['name'];
      }
      else {
        $key = array_search($old_product_attribute, $product_attributes);
        if ($key !== false) unset($product_attributes[$key]);
      }
    }
    foreach ($old_taxonomies as $old_taxonomy) {
      register_taxonomy($old_taxonomy, null);
    }
    wp_delete_object_term_relationships($post_id, $old_taxonomies);

    ksort($current_product_attributes);
    $product_attributes_copy = $product_attributes;
    ksort($product_attributes_copy);
    if ($current_product_attributes != $product_attributes_copy) {
      $product_attributes = array_merge($product_attributes, $current_product_attribute_variations);
      update_post_meta($post_id, '_product_attributes', $product_attributes);
    }
  }

  if (!in_array('attachments', $preserve_fields)) {
    $attachments = array();
    if (!empty($product['Картинка'])) {
      $attachments = array_filter($product['Картинка']);
      $attachments = array_fill_keys($attachments, array());
    }

    if ($product['ЗначенияРеквизитов']) {
      $attachment_keys = array(
        'ОписаниеФайла' => 'description',
      );
      foreach ($product['ЗначенияРеквизитов'] as $requisite) {
        $attribute_name = $requisite['Наименование'];
        if (!isset($attachment_keys[$attribute_name])) continue;

        $attribute_values = @$requisite['Значение'];
        if (!$attribute_values) continue;

        $attribute_value = $attribute_values[0];
        if (strpos($attribute_value, "import_files/") !== 0) continue;
          
        list($picture_path, $attribute_value) = explode('#', $attribute_value, 2);
        if (!isset($attachments[$picture_path])) continue;

        $attachment_key = $attachment_keys[$attribute_name];
        $attachments[$picture_path][$attachment_key] = $attribute_value;
      }
    }

    if ($attachments) {
      $attachment_ids = wc1c_replace_post_attachments($post_id, $attachments);

      $new_post_meta = array(
        '_product_image_gallery' => implode(',', array_slice($attachment_ids, 1)),
        '_thumbnail_id' => @$attachment_ids[0],
      );
      foreach ($new_post_meta as $meta_key => $meta_value) {
        if ($meta_value != @$post_meta[$meta_key]) update_post_meta($post_id, $meta_key, $meta_value);
      }
    }
  }

  do_action('wc1c_post_product', $post_id, $is_added, $product, $is_full);

  return $post_id;
}

function wc1c_replace_subproducts($is_full, $subproducts) {
  require_once sprintf(WC1C_PLUGIN_DIR . "exchange/offers.php");

  wc1c_replace_suboffers($is_full, $subproducts, true);
}

function wc1c_clean_woocommerce_categories($is_full) {
  global $wpdb;

  if (!$is_full || WC1C_PREVENT_CLEAN) return;

  $term_ids = $wpdb->get_col($wpdb->prepare("SELECT tm.term_id FROM $wpdb->termmeta tm JOIN $wpdb->term_taxonomy tt ON tm.term_id = tt.term_id WHERE taxonomy = 'product_cat' AND meta_key = 'wc1c_timestamp' AND meta_value != %d", WC1C_TIMESTAMP));
  wc1c_check_wpdb_error();

  $term_ids = apply_filters('wc1c_clean_categories', $term_ids);
  if (!$term_ids) return;

  foreach ($term_ids as $term_id) {
    $result = wp_delete_term($term_id, 'product_cat');
    wc1c_check_wp_error($result);
  }
}

function wc1c_clean_woocommerce_attributes($is_full) {
  global $wpdb;

  if (!$is_full || WC1C_PREVENT_CLEAN) return;

  $timestamps = get_option('wc1c_timestamp_attributes', array());
  if (!$timestamps) return;

  $guids = get_option('wc1c_guid_attributes', array());

  $attribute_ids = array();
  foreach ($timestamps as $guid => $timestamp) {
    if ($timestamp != WC1C_TIMESTAMP) $attribute_ids[] = $guids[$guid];
  }

  $attribute_ids = apply_filters('wc1c_clean_attributes', $attribute_ids);
  if (!$attribute_ids) return;

  foreach ($attribute_ids as $attribute_id) {
    $attribute = wc1c_woocommerce_attribute_by_id($attribute_id);
    if (!$attribute) continue;

    wc1c_delete_woocommerce_attribute($attribute_id);
    
    unset($guids[$guid]);
    unset($timestamps[$guid]);

    $is_deleted = true;
  }

  if (!empty($is_deleted)) {
    $orders = get_option('wc1c_order_attributes', array());
    $order_index = array_search($attribute_id, $orders);
    if ($order_index !== false) {
      unset($orders[$order_index]);
      update_option('wc1c_order_attributes', $orders);
    }

    update_option('wc1c_guid_attributes', $guids);
    update_option('wc1c_timestamp_attributes', $timestamps);
  }
}

function wc1c_clean_woocommerce_attribute_options($is_full, $attribute_taxonomy) {
  global $wpdb;

  if (!$is_full || WC1C_PREVENT_CLEAN) return;

  $term_ids = $wpdb->get_col($wpdb->prepare("SELECT tm.term_id FROM $wpdb->termmeta tm JOIN $wpdb->term_taxonomy tt ON tm.term_id = tt.term_id WHERE taxonomy = %s AND meta_key = 'wc1c_timestamp' AND meta_value != %d", $attribute_taxonomy, WC1C_TIMESTAMP));
  wc1c_check_wpdb_error();

  foreach ($term_ids as $term_id) {
    $result = wp_delete_term($term_id, $attribute_taxonomy);
    wc1c_check_wp_error($result);
  }
}

function wc1c_clean_posts($post_type) {
  global $wpdb;

  $post_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta JOIN $wpdb->posts ON post_id = ID WHERE post_type = %s AND meta_key = '_wc1c_timestamp' AND meta_value != %d", $post_type, WC1C_TIMESTAMP));
  wc1c_check_wpdb_error();

  foreach ($post_ids as $post_id) {
    wp_trash_post($post_id);
  }
}

function wc1c_clean_products($is_full) {
  if (!$is_full || WC1C_PREVENT_CLEAN) return;

  wc1c_clean_posts('product');
}

function wc1c_clean_product_terms() {
  global $wpdb;

  $wpdb->query("UPDATE $wpdb->term_taxonomy tt SET count = (SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = tt.term_taxonomy_id) WHERE taxonomy LIKE 'pa_%'");
  wc1c_check_wpdb_error();

  $rows = $wpdb->get_results("SELECT tm.term_id, taxonomy FROM $wpdb->term_taxonomy tt LEFT JOIN $wpdb->termmeta tm ON tt.term_id = tm.term_id AND meta_key = 'wc1c_guid' WHERE meta_value IS NULL AND taxonomy LIKE 'pa_%' AND count = 0");
  wc1c_check_wpdb_error();

  foreach ($rows as $row) {
    register_taxonomy($row->taxonomy, null);
    $result = wp_delete_term($row->term_id, $row->taxonomy);
    wc1c_check_wp_error($result);
  }
}
