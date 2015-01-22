<?php
if (!defined('ABSPATH')) exit;

function wc1c_offers_start_element_handler($is_full, $names, $depth, $name, $attrs) {
  global $wc1c_price_type, $wc1c_offer, $wc1c_price;

  if (@$names[$depth - 1] == 'ТипыЦен' && $name == 'ТипЦены') {
    if (!isset($wc1c_price_type)) $wc1c_price_type = array();
  }
  elseif (@$names[$depth - 1] == 'Предложения' && $name == 'Предложение') {
    $wc1c_offer = array();
  }
  elseif (@$names[$depth - 1] == 'Предложение' && $name == 'ХарактеристикиТовара') {
    $wc1c_offer['ХарактеристикиТовара'] = array();
  }
  elseif (@$names[$depth - 1] == 'ХарактеристикиТовара' && $name == 'ХарактеристикаТовара') {
    $wc1c_offer['ХарактеристикиТовара'][] = array();
  }
  elseif (@$names[$depth - 1] == 'Цены' && $name == 'Цена') {
    $wc1c_price = array();
  }
}

function wc1c_offers_character_data_handler($is_full, $names, $depth, $name, $data) {
  global $wc1c_price_type, $wc1c_offer, $wc1c_price;

  if (@$names[$depth - 2] == 'ТипыЦен' && @$names[$depth - 1] == 'ТипЦены' && $name != 'Налог') {
    if (!isset($wc1c_price_type[$name])) @$wc1c_price_type[$name] .= $data;
  }
  elseif (@$names[$depth - 2] == 'Предложения' && @$names[$depth - 1] == 'Предложение' && !in_array($name, array('ХарактеристикиТовара', 'Цены'))) {
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
  global $wc1c_price_type, $wc1c_offer, $wc1c_offer_guid, $wc1c_suboffers, $wc1c_price;

  if (@$names[$depth - 1] == 'ПакетПредложений' && $name == 'ТипыЦен') {
    wc1c_update_currency($wc1c_price_type['Валюта']);
  }
  elseif (@$names[$depth - 1] == 'Цены' && $name == 'Цена') {
    if ($wc1c_price['ИдТипаЦены'] == $wc1c_price_type['Ид']) $wc1c_offer['Цена'] = $wc1c_price;
  }
  elseif (@$names[$depth - 1] == 'ХарактеристикаТовара' && $name == 'Наименование') {
    $i = count($wc1c_offer['ХарактеристикиТовара']) - 1;
    $wc1c_offer['ХарактеристикиТовара'][$i]['Наименование'] = preg_replace("/\s+\(.*\)$/", '', $wc1c_offer['ХарактеристикиТовара'][$i]['Наименование']);
  }
  elseif (@$names[$depth - 1] == 'Предложения' && $name == 'Предложение') {
    if (strpos($wc1c_offer['Ид'], '#') === false) {
      wc1c_replace_offer($wc1c_offer['Ид'], @$wc1c_price['ЦенаЗаЕдиницу'], @$wc1c_offer['Количество'], @$wc1c_price['Коэффициент']);
    }
    else {
      $guid = $wc1c_offer['Ид'];
      list($offer_guid, ) = explode('#', $guid, 2);

      if ($wc1c_offer_guid != $offer_guid) {
        if ($wc1c_offer_guid) wc1c_replace_suboffers($wc1c_offer_guid, $wc1c_suboffers);
        $wc1c_suboffers = array();
      }
      $wc1c_offer_guid = $offer_guid;

      $wc1c_suboffers[] = array(
        'guid' => $wc1c_offer['Ид'],
        'price' => @$wc1c_price['ЦенаЗаЕдиницу'],
        'quantity' => @$wc1c_offer['Количество'],
        'coefficient' => @$wc1c_price['Коэффициент'],
        'characteristics' => isset($wc1c_offer['ХарактеристикиТовара']) ? $wc1c_offer['ХарактеристикиТовара'] : array(),
      );
    }
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

function wc1c_replace_product_meta($post_id, $price, $quantity, $coefficient, $attributes = array()) {
  if (isset($price)) $price = (float) $price;

  $post_meta = array(
    '_price' => $price,
    '_regular_price' => $price,
  );

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

  foreach ($post_meta as $meta_key => $meta_value) {
    $current_meta_value = @$current_post_meta[$meta_key];
    if ($meta_value !== '' && $current_meta_value == $meta_value) continue;
    if ($meta_value === '' && $current_meta_value === $meta_value) continue;

    update_post_meta($post_id, $meta_key, $meta_value);
  }

  $quantity = isset($quantity) ? (float) $quantity : 0;
  if (isset($coefficient)) $quantity *= (float) $coefficient;
  wc_update_product_stock($post_id, $quantity);
}

function wc1c_replace_offer($guid, $price, $quantity, $coefficient) {
  $post_id = wc1c_post_id_by_meta('wc1c_guid', $guid);
  if (!$post_id) wc1c_error("Failed to get offer post");

  wc1c_replace_product_meta($post_id, $price, $quantity, $coefficient);
}

function wc1c_replace_product_variation($guid, $parent_post_id, $order) {
  $post_id = wc1c_post_id_by_meta('wc1c_guid', $guid);

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
    wc1c_check_wp_error($post_id);

    update_post_meta($post_id, 'wc1c_guid', $guid);

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

function wc1c_replace_suboffers($offer_guid, $suboffers) {
  $post_id = wc1c_post_id_by_meta('wc1c_guid', $offer_guid);
  if (!$post_id) wc1c_error("Failed to get parent post ID");

  $result = wp_set_post_terms($post_id, 'variable', 'product_type');
  wc1c_check_wp_error($result);

  $offer_characteristics = array();
  foreach ($suboffers as $suboffer) {
    foreach ($suboffer['characteristics'] as $suboffer_characteristic) {
      $characteristic_name = $suboffer_characteristic['Наименование'];
      if (!isset($offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name] = array();

      $characteristic_value = $suboffer_characteristic['Значение'];
      if (!in_array($characteristic_value, $offer_characteristics[$characteristic_name])) $offer_characteristics[$characteristic_name][] = $characteristic_value;
    }
  }

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
    foreach ($suboffer['characteristics'] as $suboffer_characteristic) {
      $attributes[$suboffer_characteristic['Наименование']] = $suboffer_characteristic['Значение'];
    }

    wc1c_replace_product_meta($product_variation_id, $suboffer['price'], $suboffer['quantity'], $suboffer['coefficient'], $attributes);
  }

  $deleted_product_variation_ids = array_diff($current_product_variation_ids, $product_variation_ids);
  foreach ($deleted_product_variation_ids as $deleted_product_variation_id) {
    wp_delete_post($deleted_product_variation_id, true);
  }
}
