<?php
if (!defined('ABSPATH')) exit;

if (!defined('WC1C_PRICE_TYPE')) define('WC1C_PRICE_TYPE', null);
if (!defined('WC1C_PRESERVE_PRODUCT_VARIATIONS')) define('WC1C_PRESERVE_PRODUCT_VARIATIONS', false);

function wc1c_offers_start_element_handler($is_full, $names, $depth, $name, $attrs) {
  global $wc1c_price_types, $wc1c_offer, $wc1c_price;

  if (@$names[$depth - 1] == 'ПакетПредложений' && $name == 'ТипыЦен') {
    $wc1c_price_types = array();
  }
  elseif (@$names[$depth - 1] == 'ТипыЦен' && $name == 'ТипЦены') {
    $wc1c_price_types[] = array();
  }
  elseif (@$names[$depth - 1] == 'Предложение' && $name == 'Склад') {
    @$wc1c_offer['КоличествоНаСкладе'] += $attrs['КоличествоНаСкладе'];
  }
  elseif (@$names[$depth - 1] == 'Предложения' && $name == 'Предложение') {
    $wc1c_offer = array(
      'ХарактеристикиТовара' => array(),
    );
  }
  elseif (@$names[$depth - 1] == 'ХарактеристикиТовара' && $name == 'ХарактеристикаТовара') {
    $wc1c_offer['ХарактеристикиТовара'][] = array();
  }
  elseif (@$names[$depth - 1] == 'Цены' && $name == 'Цена') {
    $wc1c_price = array();
  }
}

function wc1c_offers_character_data_handler($is_full, $names, $depth, $name, $data) {
  global $wc1c_price_types, $wc1c_offer, $wc1c_price;

  if (@$names[$depth - 2] == 'ТипыЦен' && @$names[$depth - 1] == 'ТипЦены' && $name != 'Налог') {
    $i = count($wc1c_price_types) - 1;
    @$wc1c_price_types[$i][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Предложения' && @$names[$depth - 1] == 'Предложение' && !in_array($name, array('БазоваяЕдиница', 'ХарактеристикиТовара', 'Цены'))) {
    @$wc1c_offer[$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'ХарактеристикиТовара' && @$names[$depth - 1] == 'ХарактеристикаТовара') {
    $i = count($wc1c_offer['ХарактеристикиТовара']) - 1;
    @$wc1c_offer['ХарактеристикиТовара'][$i][$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Цены' && @$names[$depth - 1] == 'Цена') {
    @$wc1c_price[$name] .= $data;
  }
}

function wc1c_offers_end_element_handler($is_full, $names, $depth, $name) {
  global $wpdb, $wc1c_price_types, $wc1c_price_type, $wc1c_price_type, $wc1c_offer, $wc1c_suboffers, $wc1c_price;

  if (@$names[$depth - 1] == 'ПакетПредложений' && $name == 'ТипыЦен') {
    if (!WC1C_PRICE_TYPE) {
      $wc1c_price_type = $wc1c_price_types[0];
    }
    else {
      foreach ($wc1c_price_types as $price_type) {
        if (@$price_type['Ид'] != WC1C_PRICE_TYPE && @$price_type['Наименование'] != WC1C_PRICE_TYPE) continue;

        $wc1c_price_type = $price_type;
        break;
      }
      if (!isset($wc1c_price_type)) wc1c_error("Failed to match price type");
    }

    if (!empty($wc1c_price_type['Валюта'])) {
      wc1c_update_currency($wc1c_price_type['Валюта']);
      update_option('wc1c_currency', $wc1c_price_type['Валюта']);
    }
  }
  elseif (@$names[$depth - 1] == 'Цены' && $name == 'Цена') {
    if (!isset($wc1c_offer['Цена']) && (!isset($wc1c_price['ИдТипаЦены']) || $wc1c_price['ИдТипаЦены'] == $wc1c_price_type['Ид'])) $wc1c_offer['Цена'] = $wc1c_price;
    else $wc1c_offer["Цена_{$wc1c_price['ИдТипаЦены']}"] = $wc1c_price;
  }
  elseif (@$names[$depth - 1] == 'ХарактеристикаТовара' && $name == 'Наименование') {
    $i = count($wc1c_offer['ХарактеристикиТовара']) - 1;
    $wc1c_offer['ХарактеристикиТовара'][$i]['Наименование'] = preg_replace("/\s+\(.*\)$/", '', $wc1c_offer['ХарактеристикиТовара'][$i]['Наименование']);
  }
  elseif (@$names[$depth - 1] == 'Предложения' && $name == 'Предложение') {
    if (strpos($wc1c_offer['Ид'], '#') === false || WC1C_DISABLE_VARIATIONS) {
      $guid = $wc1c_offer['Ид'];
      $_post_id = wc1c_replace_offer($is_full, $guid, $wc1c_offer);
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
      $guid = $wc1c_offer['Ид'];
      list($product_guid, ) = explode('#', $guid, 2);

      if (empty($wc1c_suboffers) || $wc1c_suboffers[0]['product_guid'] != $product_guid) {
        if ($wc1c_suboffers) wc1c_replace_suboffers($is_full, $wc1c_suboffers);
        $wc1c_suboffers = array();
      }

      $wc1c_suboffers[] = array(
        'guid' => $wc1c_offer['Ид'],
        'product_guid' => $product_guid,
        'offer' => $wc1c_offer,
      );
    }
  }
  elseif (@$names[$depth - 1] == 'ПакетПредложений' && $name == 'Предложения') {
    if ($wc1c_suboffers) wc1c_replace_suboffers($is_full, $wc1c_suboffers);
  }
  elseif (!$depth && $name == 'КоммерческаяИнформация') {
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
    wc1c_check_wpdb_error();

    do_action('wc1c_post_offers', $is_full);
  }
}

function wc1c_update_currency($currency) {
  if (!array_key_exists($currency, get_woocommerce_currencies())) return;

  update_option('woocommerce_currency', $currency);

  $currency_position = array(
    'RUB' => 'right_space',
    'USD' => 'left',
  );
  if (isset($currency_position[$currency])) update_option('woocommerce_currency_pos', $currency_position[$currency]);
}

function wc1c_replace_offer_post_meta($is_full, $post_id, $offer, $attributes = array()) {
  $price = isset($offer['Цена']['ЦенаЗаЕдиницу']) ? wc1c_parse_decimal($offer['Цена']['ЦенаЗаЕдиницу']) : null;
  if (!is_null($price)) {
    $coefficient = isset($offer['Цена']['Коэффициент']) ? wc1c_parse_decimal($offer['Цена']['Коэффициент']) : null;
    if (!is_null($coefficient)) $price *= $coefficient;
  }

  $post_meta = array();
  if (!is_null($price)) {
    $post_meta['_regular_price'] = $price;
    $post_meta['_manage_stock'] = 'yes';
  }

  if ($attributes) {
    foreach ($attributes as $attribute_name => $attribute_value) {
      $meta_key = 'attribute_' . sanitize_title($attribute_name);
      $post_meta[$meta_key] = $attribute_value;
    }

    $current_post_meta = get_post_meta($post_id);
    foreach ($current_post_meta as $meta_key => $meta_value) {
      $current_post_meta[$meta_key] = $meta_value[0];
    }

    foreach ($current_post_meta as $meta_key => $meta_value) {
      if (strpos($meta_key, 'attribute_') !== 0 || array_key_exists($meta_key, $post_meta)) continue;

      delete_post_meta($post_id, $meta_key);
    }
  }

  if (!is_null($price)) {
    $sale_price = @$current_post_meta['_sale_price'];
    $sale_price_from = @$current_post_meta['_sale_price_dates_from'];
    $sale_price_to = @$current_post_meta['_sale_price_dates_to'];
    if (empty($current_post_meta['_sale_price'])) {
      $post_meta['_price'] = $price;
    }
    else {
      if (empty($sale_price_from) && empty($sale_price_to)) {
        $post_meta['_price'] = $current_post_meta['_sale_price'];
      }
      else {
        $now = strtotime('now', current_time('timestamp'));
        if (!empty($sale_price_from) && strtotime($sale_price_from) < $now) {
          $post_meta['_price'] = $current_post_meta['_sale_price'];
        }
        if (!empty($sale_price_to) && strtotime($sale_price_to) < $now) {
          $post_meta['_price'] = $price;
          $post_meta['_sale_price_dates_from'] = '';
          $post_meta['_sale_price_dates_to'] = '';
        }
      }
    }
  }

  foreach ($post_meta as $meta_key => $meta_value) {
    $current_meta_value = @$current_post_meta[$meta_key];
    if ($meta_value !== '' && $current_meta_value == $meta_value) continue;
    if ($meta_value === '' && $current_meta_value === $meta_value) continue;

    update_post_meta($post_id, $meta_key, $meta_value);
  }

  $quantity = isset($offer['Количество']) ? $offer['Количество'] : @$offer['КоличествоНаСкладе'];
  if (!is_null($quantity)) {
    $quantity = wc1c_parse_decimal($quantity);
    wc_update_product_stock($post_id, $quantity);

    $stock_status = $quantity > 0 ? 'instock' : WC1C_OUTOFSTOCK_STATUS;
    @wc_update_product_stock_status($post_id, $stock_status);
  }

  do_action('wc1c_post_offer_meta', $post_id, $offer, $is_full);
}

function wc1c_replace_offer($is_full, $guid, $offer) {
  $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);
  if ($post_id) wc1c_replace_offer_post_meta($is_full, $post_id, $offer);

  do_action('wc1c_post_offer', $post_id, $offer, $is_full);
  return $post_id;
}

function wc1c_replace_product_variation($guid, $parent_post_id, $order) {
  $post_id = wc1c_post_id_by_meta('_wc1c_guid', $guid);

  $args = array(
    'menu_order'=> $order,
  );

  if (!$post_id) {
    $args = array_merge($args, array(
      'post_type' => 'product_variation',
      'post_parent' => $parent_post_id,
      'post_title' => "Product #$parent_post_id Variation",
      'post_status' => 'publish',
    ));
    $post_id = wp_insert_post($args, true);
    wc1c_check_wpdb_error();
    wc1c_check_wp_error($post_id);

    update_post_meta($post_id, '_wc1c_guid', $guid);

    $is_added = true;
  }

  $post = get_post($post_id);
  if (!$post) wc1c_error("Failed to get post");

  if (empty($is_added)) {
    foreach ($args as $key => $value) {
      if ($post->$key == $value) continue;

      $is_changed = true;
      break;
    }

    if (!empty($is_changed)) {
      $args = array_merge($args, array(
        'ID' => $post_id,
      ));
      $post_id = wp_update_post($args, true);
      wc1c_check_wp_error($post_id);
    }
  }

  return $post_id;
}

function wc1c_replace_suboffers($is_full, $suboffers, $are_products = false) {
  if (!$suboffers) return;

  $product_guid = $suboffers[0]['product_guid'];
  $post_id = wc1c_post_id_by_meta('_wc1c_guid', $product_guid);
  if (!$post_id && !$are_products) return;

  if ($are_products) {
    $product = $suboffers[0]['product'];
    $product['Ид'] = $product_guid;
    $post_id = wc1c_replace_product($suboffers[0]['is_full'], $product_guid, $product);
  }

  if (!WC1C_DISABLE_VARIATIONS) {
    $result = wp_set_post_terms($post_id, 'variable', 'product_type');
    wc1c_check_wp_error($result);
  }

  $offer_characteristics = array();
  foreach ($suboffers as $suboffer) {
    if (isset($suboffer['offer']['ХарактеристикиТовара'])) {
      foreach ($suboffer['offer']['ХарактеристикиТовара'] as $suboffer_characteristic) {
        $characteristic_name = $suboffer_characteristic['Наименование'];
        if (!isset($offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name] = array();

        $characteristic_value = @$suboffer_characteristic['Значение'];
        if (!in_array($characteristic_value, $offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name][] = $characteristic_value;
      }
    }
  }

  if ($offer_characteristics) {
    ksort($offer_characteristics);
    foreach ($offer_characteristics as $characteristic_name => &$characteristic_values) {
      sort($characteristic_values);
    }

    $current_product_attributes = get_post_meta($post_id, '_product_attributes', true);
    if (!$current_product_attributes) $current_product_attributes = array();

    $product_attributes = array();
    foreach ($current_product_attributes as $current_product_attribute_key => $current_product_attribute) {
      if (!$current_product_attribute['is_variation']) $product_attributes[$current_product_attribute_key] = $current_product_attribute;
    }

    foreach ($offer_characteristics as $offer_characteristic_name => $offer_characteristic_values) {
      $product_attribute_key = sanitize_title($offer_characteristic_name);
      $product_attribute_position = count($product_attributes);
      $product_attributes[$product_attribute_key] = array(
        'name' => wc_clean($offer_characteristic_name),
        'value' => implode(" | ", $offer_characteristic_values),
        'position' => $product_attribute_position,
        'is_visible' => 1,
        'is_variation' => 1,
        'is_taxonomy' => 0,
      );
    }

    ksort($current_product_attributes);
    $product_attributes_copy = $product_attributes;
    ksort($product_attributes_copy);
    if ($current_product_attributes != $product_attributes_copy) {
      update_post_meta($post_id, '_product_attributes', $product_attributes);
    }
  }

  $current_product_variation_ids = array();
  $product_variation_posts = get_children("post_parent=$post_id&post_type=product_variation");
  foreach ($product_variation_posts as $product_variation_post) {
    $current_product_variation_ids[] = $product_variation_post->ID;
  }

  $product_variation_ids = array();
  foreach ($suboffers as $i => $suboffer) {
    $product_variation_id = wc1c_replace_product_variation($suboffer['guid'], $post_id, $i + 1);
    $product_variation_ids[] = $product_variation_id;

    $attributes = array_fill_keys(array_keys($offer_characteristics), '');
    if (isset($suboffer['offer']['ХарактеристикиТовара'])) {
      foreach ($suboffer['offer']['ХарактеристикиТовара'] as $suboffer_characteristic) {
        $suboffer_characteristic_value = @$suboffer_characteristic['Значение'];
        if ($suboffer_characteristic_value) $attributes[$suboffer_characteristic['Наименование']] = $suboffer_characteristic_value;
      }
    }

    if ($are_products) {
      wc1c_replace_offer_post_meta($is_full, $product_variation_id, array(), $attributes);
    }
    else {
      wc1c_replace_offer_post_meta($is_full, $product_variation_id, $suboffer['offer'], $attributes);
    }
  }

  if (!WC1C_PRESERVE_PRODUCT_VARIATIONS) {
    $deleted_product_variation_ids = array_diff($current_product_variation_ids, $product_variation_ids);
    foreach ($deleted_product_variation_ids as $deleted_product_variation_id) {
      wp_delete_post($deleted_product_variation_id, true);
    }
  }
}
