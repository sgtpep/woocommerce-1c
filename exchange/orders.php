<?php
if (!defined('ABSPATH')) exit;

function wc1c_orders_start_element_handler($is_full, $names, $depth, $name, $attrs) {
  global $wc1c_document;

  if (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'КоммерческаяИнформация' && $name == 'Документ') {
    $wc1c_document = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Документ' && $name == 'Контрагенты') {
    $wc1c_document['Контрагенты'] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Контрагенты' && $name == 'Контрагент') {
    $wc1c_document['Контрагенты'][] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Документ' && $name == 'Товары') {
    $wc1c_document['Товары'] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Товары' && $name == 'Товар') {
    $wc1c_document['Товары'][] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Товар' && $name == 'ЗначенияРеквизитов') {
    $i = count($wc1c_document['Товары']) - 1;
    $wc1c_document['Товары'][$i]['ЗначенияРеквизитов'] = array();
  }
  elseif (array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'Товар' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ЗначенияРеквизитов' && $name == 'ЗначениеРеквизита') {
    $i = count($wc1c_document['Товары']) - 1;
    $wc1c_document['Товары'][$i]['ЗначенияРеквизитов'][] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Товар' && $name == 'ХарактеристикиТовара') {
    $i = count($wc1c_document['Товары']) - 1;
    $wc1c_document['Товары'][$i]['ХарактеристикиТовара'] = array();
  }
  elseif (array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'Товар' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ХарактеристикиТовара' && $name == 'ХарактеристикаТовара') {
    $i = count($wc1c_document['Товары']) - 1;
    $wc1c_document['Товары'][$i]['ХарактеристикиТовара'][] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Документ' && $name == 'ЗначенияРеквизитов') {
    $wc1c_document['ЗначенияРеквизитов'] = array();
  }
  elseif (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ЗначенияРеквизитов' && $name == 'ЗначениеРеквизита') {
    $wc1c_document['ЗначенияРеквизитов'][] = array();
  }
}

function wc1c_orders_character_data_handler($is_full, $names, $depth, $name, $data) {
  global $wc1c_document;

  if (array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'КоммерческаяИнформация' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Документ' && !in_array($name, array('Контрагенты', 'Товары', 'ЗначенияРеквизитов'))) {
    isset($wc1c_document[$name]) || $wc1c_document[$name] = '';
    $wc1c_document[$name] .= $data;
  }
  elseif (array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'Контрагенты' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Контрагент') {
    $i = count($wc1c_document['Контрагенты']) - 1;
    isset($wc1c_document['Контрагенты'][$i][$name]) || $wc1c_document['Контрагенты'][$i][$name] = '';
    $wc1c_document['Контрагенты'][$i][$name] .= $data;
  }
  elseif (array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'Товары' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'Товар' && !in_array($name, array('СтавкиНалогов', 'ЗначенияРеквизитов', 'ХарактеристикиТовара'))) {
    $i = count($wc1c_document['Товары']) - 1;
    isset($wc1c_document['Товары'][$i][$name]) || $wc1c_document['Товары'][$i][$name] = '';
    $wc1c_document['Товары'][$i][$name] .= $data;
  }
  elseif (array_key_exists($depth - 3, $names) && $names[$depth - 3] == 'Товар' && array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'ЗначенияРеквизитов' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ЗначениеРеквизита') {
    $i = count($wc1c_document['Товары']) - 1;
    $j = count($wc1c_document['Товары'][$i]['ЗначенияРеквизитов']) - 1;
    isset($wc1c_document['Товары'][$i]['ЗначенияРеквизитов'][$j][$name]) || $wc1c_document['Товары'][$i]['ЗначенияРеквизитов'][$j][$name] = '';
    $wc1c_document['Товары'][$i]['ЗначенияРеквизитов'][$j][$name] .= $data;
  }
  elseif (array_key_exists($depth - 3, $names) && $names[$depth - 3] == 'Товар' && array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'ХарактеристикиТовара' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ХарактеристикаТовара') {
    $i = count($wc1c_document['Товары']) - 1;
    $j = count($wc1c_document['Товары'][$i]['ХарактеристикиТовара']) - 1;
    isset($wc1c_document['Товары'][$i]['ХарактеристикиТовара'][$j][$name]) || $wc1c_document['Товары'][$i]['ХарактеристикиТовара'][$j][$name] = '';
    $wc1c_document['Товары'][$i]['ХарактеристикиТовара'][$j][$name] .= $data;
  }
  elseif (array_key_exists($depth - 3, $names) && $names[$depth - 3] == 'Документ' && array_key_exists($depth - 2, $names) && $names[$depth - 2] == 'ЗначенияРеквизитов' && array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'ЗначениеРеквизита') {
    $i = count($wc1c_document['ЗначенияРеквизитов']) - 1;
    isset($wc1c_document['ЗначенияРеквизитов'][$i][$name]) || $wc1c_document['ЗначенияРеквизитов'][$i][$name] = '';
    $wc1c_document['ЗначенияРеквизитов'][$i][$name] .= $data;
  }
}

function wc1c_orders_end_element_handler($is_full, $names, $depth, $name) {
  global $wpdb, $wc1c_document;

  if (array_key_exists($depth - 1, $names) && $names[$depth - 1] == 'КоммерческаяИнформация' && $name == 'Документ') {
    wc1c_replace_document($wc1c_document);
  }
  elseif (!$depth && $name == 'КоммерческаяИнформация') {
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%'");
    wc1c_check_wpdb_error();

    do_action('wc1c_post_orders', $is_full);
  }
}

function wc1c_replace_document_products($order, $document_products) {
  $line_items = $order->get_items();
  $line_item_ids = array();
  foreach ($document_products as $i => $document_product) {
    $product_id = wc1c_post_id_by_meta('_wc1c_guid', $document_product['Ид']);
    if (!$product_id) continue;

    $product = wc_get_product($product_id);
    if (!$product) wc1c_error("Failed to get product");

    $document_products[$i]['product'] = $product;
    
    $current_line_item_id = null;
    foreach ($line_items as $line_item_id => $line_item) {
      if ($line_item['product_id'] != $product->get_id() || (int) $line_item['variation_id'] != $product->get_id()) continue;

      $current_line_item_id = $line_item_id;
      break;
    }
    $document_products[$i]['line_item_id'] = $current_line_item_id;

    if ($current_line_item_id) $line_item_ids[] = $current_line_item_id;
  }

  $old_line_item_ids = array_diff(array_keys($line_items), $line_item_ids);
  if ($old_line_item_ids) {
    $order->remove_order_items('line_item');

    foreach ($document_products as $i => $document_product) {
      $document_products[$i]['line_item_id'] = null;
    }
  }

  foreach ($document_products as $document_product) {
    $quantity = isset($document_product['Количество']) ? wc1c_parse_decimal($document_product['Количество']) : 1;
    $coefficient = isset($document_product['Коэффициент']) ? wc1c_parse_decimal($document_product['Коэффициент']) : 1;
    $quantity *= $coefficient;

    if (!empty($document_product['Сумма'])) {
      $total = wc1c_parse_decimal($document_product['Сумма']);
    }
    else {
      $price = wc1c_parse_decimal(isset($document_product['ЦенаЗаЕдиницу']) ? $document_product['ЦенаЗаЕдиницу'] : null);
      $total = $price * $quantity;
    }

    $args = array(
      'totals' => array(
        'subtotal' => $total,
        'total' => $total,
      ),
    );

    if (!isset($document_product['product'])) continue;
    $product = $document_product['product'];

    if ($product->variation_id) {
      $attributes = $product->get_variation_attributes();
      $variation = array();
      foreach ($attributes as $attribute_key => $attribute_value) {
        $variation[urldecode((string) $attribute_key)] = urldecode((string) $attribute_value);
      }
      $args['variation'] = $variation;
    }

    $line_item_id = $document_product['line_item_id'];
    if (!$line_item_id) {
      $line_item_id = $order->add_product($product, $quantity, $args);
      if (!$line_item_id) wc1c_error("Failed to add product to the order");
    }
    else {
      $args['qty'] = $quantity;

      $result = $order->update_product($line_item_id, $product, $args);
      if (!$result) wc1c_error("Failed to update product in the order");
    }
  }
}

function wc1c_replace_document_services($order, $document_services) {
  static $shipping_methods;

  $shipping_items = $order->get_shipping_methods();

  if ($shipping_items && !$document_services) {
    $order->remove_order_items('shipping');

    return;
  }

  if (!$shipping_methods) {
    if ($shipping = WC()->shipping) {
      $shipping->load_shipping_methods();
      $shipping_methods = $shipping->get_shipping_methods();
    }
  }

  $shipping_cost_sum = 0;
  foreach ($document_services as $document_service) {
    foreach ($shipping_methods as $shipping_method_id => $shipping_method) {
      if ($document_service['Наименование'] != $shipping_method->title) continue;

      $shipping_cost = wc1c_parse_decimal($document_service['Сумма']);
      $shipping_cost_sum += $shipping_cost;

      $method_title = isset($shipping_method->method_title) ? $shipping_method->method_title : '';
      $args = array(
        'method_id' => $shipping_method->id,
        'method_title' => $method_title,
        'cost' => $shipping_cost,
      );

      if (!$shipping_items) {
        $shipping_rate = new WC_Shipping_Rate($args['method_id'], $args['method_title'], $args['method_title'], null, $args['method_id']);

        $shipping_item_id = $order->add_shipping($shipping_rate);
        if (!$shipping_item_id) wc1c_error("Failed to add shippin to the order");
      }
      else {
        $shipping_item_id = key($shipping_items);
        $result = $order->update_shipping($shipping_item_id, $args);
        if (!$result) wc1c_error("Failed to add shippin to the order");
      }

      break;
    }
  }

  return $shipping_cost_sum;
}

function wc1c_woocommerce_new_order_data($order_data) {
  global $wc1c_document;

  $order_data['import_id'] = $wc1c_document['Номер'];

  return $order_data;
}
add_filter('woocommerce_new_order_data', 'wc1c_woocommerce_new_order_data');

function wc1c_replace_document($document) {
  global $wpdb;

  if ($document['ХозОперация'] != "Заказ товара" || $document['Роль'] != "Продавец") return;

  $order = wc_get_order($document['Номер']);

  if (!$order) {
    $args = array(
      'status' => 'on-hold',
      'customer_note' => isset($document['Комментарий']) ? $document['Комментарий'] : null,
    );

    $contragent_name = isset($document['Контрагенты'][0]['Наименование']) ? $document['Контрагенты'][0]['Наименование'] : null;
    if ($contragent_name == "Гость") {
      $user_id = 0;
    }
    elseif (str_contains((string) $contragent_name, ' ')) {
      list($first_name, $last_name) = explode(' ', (string) $contragent_name, 2);
      $result = $wpdb->get_var($wpdb->prepare("SELECT u1.user_id FROM $wpdb->usermeta u1 JOIN $wpdb->usermeta u2 ON u1.user_id = u2.user_id WHERE (u1.meta_key = 'billing_first_name' AND u1.meta_value = %s AND u2.meta_key = 'billing_last_name' AND u2.meta_value = %s) OR (u1.meta_key = 'shipping_first_name' AND u1.meta_value = %s AND u2.meta_key = 'shipping_last_name' AND u2.meta_value = %s)", $first_name, $last_name, $first_name, $last_name));
      wc1c_check_wpdb_error();
      if ($result) $user_id = $result;
    }
    if (isset($user_id)) $args['customer_id'] = $user_id;

    $order = wc_create_order($args);
    wc1c_check_wp_error($order);

    if (!isset($user_id)) update_post_meta($order->get_id(), 'wc1c_contragent', $contragent_name);

    $args = array(
      'ID' => $order->get_id(),
    );

    $date = isset($document['Дата']) ? $document['Дата'] : null;
    if ($date && !empty($document['Время'])) $date .= " {$document['Время']}";
    $timestamp = strtotime((string) $date);
    $args['post_date'] = date("Y-m-d H:i:s", $timestamp);

    $result = wp_update_post($args);
    wc1c_check_wp_error($result);
    if (!$result) wc1c_error("Failed to update order post");

    update_post_meta($order->get_id(), '_wc1c_guid', $document['Ид']);
  }
  else {
    $args = array(
      'order_id' => $order->get_id(),
    );

    foreach ($document['ЗначенияРеквизитов'] as $requisite) {
      if ($requisite['Наименование'] != 'Статуса заказа ИД' || !in_array($requisite['Значение'], array("pending", "processing", "on-hold", "completed", "cancelled", "refunded", "failed"))) continue;
      
      $args['status'] = $requisite['Значение'];
      break;
    }
	
	foreach ($document['ЗначенияРеквизитов'] as $requisite) {
     if ($requisite['Наименование'] != 'Отменен' || $requisite['Значение'] != 'true') continue;
       
     $args['status'] = 'cancelled';
     break;
    }

    $order = wc_update_order($args);
    wc1c_check_wp_error($order);
  }

  $is_deleted = false;
  foreach ($document['ЗначенияРеквизитов'] as $requisite) {
    if ($requisite['Наименование'] != 'ПометкаУдаления' || $requisite['Значение'] != 'true') continue;
      
    $is_deleted = true;
    break;
  }

  if ($is_deleted && $order->get_status() != 'trash') {
    wp_trash_post($order->get_id());
  }
  elseif (!$is_deleted && $order->get_status() == 'trash') {
    wp_untrash_post($order->get_id());
  }

  $post_meta = array();
  if (isset($document['Валюта'])) $post_meta['_order_currency'] = $document['Валюта'];
  if (isset($document['Сумма'])) $post_meta['_order_total'] = wc1c_parse_decimal($document['Сумма']);

  $document_products = array();
  $document_services = array();
  if (isset($document['Товары'])) {
    foreach ($document['Товары'] as $i => $document_product) {
      foreach ($document_product['ЗначенияРеквизитов'] as $document_product_requisite) {
        if ($document_product_requisite['Наименование'] != 'ТипНоменклатуры') continue;

        if ($document_product_requisite['Значение'] == 'Услуга') {
          $document_services[] = $document_product;
        }
        else {
          $document_products[] = $document_product;
        }
        break;
      }
    }
  }

  wc1c_replace_document_products($order, $document_products);
  $post_meta['_order_shipping'] = wc1c_replace_document_services($order, $document_services);

  $current_post_meta = get_post_meta($order->get_id());
  foreach ($current_post_meta as $meta_key => $meta_value) {
    $current_post_meta[$meta_key] = $meta_value[0];
  }
 
  foreach ($post_meta as $meta_key => $meta_value) {
    $current_meta_value = isset($current_post_meta[$meta_key]) ? $current_post_meta[$meta_key] : null;
    if ($current_meta_value == $meta_value) continue;

    update_post_meta($order->get_id(), $meta_key, $meta_value);
  }
}
