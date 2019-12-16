<?php
if (!defined('ABSPATH')) exit;

if (!defined('WC1C_CURRENCY')) define('WC1C_CURRENCY', null);

WC();
$order_statuses = array_keys(wc_get_order_statuses());
$order_posts = get_posts(array(
  'post_type' => 'shop_order',
  'post_status' => $order_statuses,
  'meta_query' => array(
    array(
      'key' => 'wc1c_queried',
      'compare' => "NOT EXISTS",
    ),
  ),
));

$order_post_ids = array();
$documents = array();
foreach ($order_posts as $order_post) {
  $order = wc_get_order($order_post);
  if (!$order) wc1c_error("Failed to get order");

  $order_post_ids[] = $order_post->ID;

  $order_line_items = $order->get_items();

  // $has_missing_item = false;
  foreach ($order_line_items as $key => $order_line_item) {
    $product_id = $order_line_item['variation_id'] ? $order_line_item['variation_id'] : $order_line_item['product_id'];
    $guid = get_post_meta($product_id, '_wc1c_guid', true);
    // if (!$guid) {
    //   $has_missing_item = true;
    //   break;
    // }

    $order_line_items[$key]['wc1c_guid'] = $guid;
  }
  // if ($has_missing_item) continue;

  $order_shipping_items = $order->get_shipping_methods();

  $order_meta = get_post_meta($order_post->ID, null, true);
  foreach ($order_meta as $meta_key => $meta_value) {
    $order_meta[$meta_key] = $meta_value[0];
  }

  $address_items = array(
    'postcode' => "Почтовый индекс",
    'country_name' => "Страна",
    'state' => "Регион",
    'city' => "Город",
  );
  $contact_items = array(
    'email' => "Почта",
    'phone' => "ТелефонРабочий",
  );

  $contragent_meta = get_post_meta($order_post->ID, 'wc1c_contragent', true);
  $contragents = array();
  foreach (array('billing', 'shipping') as $type) {
    $contragent = array();

    $name = array();
    foreach (array('last_name', 'first_name') as $name_key) {
      $meta_key = "_{$type}_$name_key";
      if (empty($order_meta[$meta_key])) continue;

      $name[] = $order_meta[$meta_key];
      $contragent[$name_key] = $order_meta[$meta_key];
    }

    $name = implode(' ', $name);
    if (!$name) {
      $contragent['name'] = $contragent_meta ? $contragent_meta : "Гость";
      $contragent['user_id'] = 0;
    }
    else {
      $contragent['name'] = $name;
      $contragent['user_id'] = $order_post->post_author;
    }

    if (!empty($order_meta["_{$type}_country"])) {
      $country_code = $order_meta["_{$type}_country"];
      $order_meta["_{$type}_country_name"] = WC()->countries->countries[$country_code];
    }

    $full_address = array();
    foreach (array('postcode', 'country_name', 'state', 'city', 'address_1', 'address_2') as $address_key) {
      $meta_key = "_{$type}_$address_key";
      if (!empty($order_meta[$meta_key])) $full_address[] = $order_meta[$meta_key];
    }
    $contragent['full_address'] = implode(", ", $full_address);

    $contragent['address'] = array();
    foreach ($address_items as $address_key => $address_item_name) {
      if (empty($order_meta["_{$type}_$address_key"])) continue;

      $contragent['address'][$address_item_name] = $order_meta["_{$type}_$address_key"];
    }

    $contragent['contacts'] = array();
    foreach ($contact_items as $contact_key => $contact_item_name) {
      if (empty($order_meta["_{$type}_$contact_key"])) continue;

      $contragent['contacts'][$contact_item_name] = $order_meta["_{$type}_$contact_key"];
    }

    $contragents[$type] = $contragent;
  }

  $products = array();
  foreach ($order_line_items as $order_line_item) {
    $products[] = array(
      'guid' => $order_line_item['wc1c_guid'],
      'name' => $order_line_item['name'],
      'price_per_item' => $order_line_item['line_total'] / $order_line_item['qty'],
      'quantity' => $order_line_item['qty'],
      'total' => $order_line_item['line_total'],
      'type' => "Товар",
    );
  }

  foreach ($order_shipping_items as $order_shipping_item) {
    if (!$order_shipping_item['cost']) continue;

    $products[] = array(
      'guid' => 'ORDER_DELIVERY',
      'name' => $order_shipping_item['name'],
      'price_per_item' => $order_shipping_item['cost'],
      'quantity' => 1,
      'total' => $order_shipping_item['cost'],
      'type' => "Услуга",
    );
  }

  $statuses = array(
    'cancelled' => "Отменен",
    'trash' => "Удален",
  );
  $status = $order->get_status();
  if (array_key_exists($status, $statuses)) {
    $order_status_name = $statuses[$status];
  }
  else {
    $order_status_name = wc_get_order_status_name($status);
  }

  if (WC1C_CURRENCY) $document_currency = WC1C_CURRENCY;
  else $document_currency = get_option('wc1c_currency', @$order_meta['_order_currency']);

  $document = array(
    'order_id' => $order_post->ID,
    'currency' => $document_currency,
    'total' => @$order_meta['_order_total'],
    'comment' => $order_post->post_excerpt,
    'contragents' => $contragents,
    'products' => $products,
    'payment_method_title' => @$order_meta['_payment_method_title'],
    'status' => $status,
    'status_name' => $order_status_name,
    'has_shipping' => count($order_shipping_items) > 0,
    'modified_at' => $order_post->post_modified,
  );
  list($document['date'], $document['time']) = explode(' ', $order_post->post_date, 2);

  $documents[] = $document;
}

$documents = apply_filters('wc1c_query_documents', $documents);

echo '<?xml version="1.0" encoding="' . WC1C_XML_CHARSET . '"?>';
?>

<КоммерческаяИнформация ВерсияСхемы="2.05" ДатаФормирования="<?php echo date("Y-m-dTH:i:s", WC1C_TIMESTAMP) ?>">
  <?php foreach ($documents as $document): ?>
    <Документ>
      <Ид>wc1c#order#<?php echo $document['order_id'] ?></Ид>
      <Номер><?php echo $document['order_id'] ?></Номер>
      <Дата><?php echo $document['date'] ?></Дата>
      <Время><?php echo $document['time'] ?></Время>
      <ХозОперация>Заказ товара</ХозОперация>
      <Роль>Продавец</Роль>
      <Валюта><?php echo $document['currency'] ?></Валюта>
      <Сумма><?php echo $document['total'] ?></Сумма>
      <Комментарий><?php echo $document['comment'] ?></Комментарий>
      <Контрагенты>
        <?php foreach ($document['contragents'] as $type => $contragent): ?>
          <Контрагент>
            <Ид>wc1c#user#<?php echo $contragent['user_id'] ?></Ид>
            <Роль><?php echo $type == 'billing' ? "Плательщик" : "Получатель" ?></Роль>
            <?php if (!empty($contragent['name'])): ?>
              <Наименование><?php echo $contragent['name'] ?></Наименование>
              <ПолноеНаименование><?php echo $contragent['name'] ?></ПолноеНаименование>
            <?php endif ?>
            <?php if (!empty($contragent['first_name'])): ?>
              <Имя><?php echo $contragent['first_name'] ?></Имя>
            <?php endif ?>
            <?php if (!empty($contragent['last_name'])): ?>
              <Фамилия><?php echo $contragent['last_name'] ?></Фамилия>
            <?php endif ?>
            <?php if (!empty($contragent['full_address']) || $contragent['address']): ?>
              <АдресРегистрации>
                <?php if (!empty($contragent['full_address'])): ?>
                  <Представление><?php echo $contragent['full_address'] ?></Представление>  
                <?php endif ?>
                <?php foreach ($contragent['address'] as $address_item_name => $address_item_value): ?>
                  <АдресноеПоле>
                    <Тип><?php echo $address_item_name ?></Тип>
                    <Значение><?php echo $address_item_value ?></Значение>
                  </АдресноеПоле>
                <?php endforeach ?>
              </АдресРегистрации>
            <?php endif ?>
            <Контакты>
              <?php foreach ($contragent['contacts'] as $contact_item_name => $contact_item_value): ?>
                <Контакт>
                  <Тип><?php echo $contact_item_name ?></Тип>
                  <Значение><?php echo $contact_item_value ?></Значение>
                </Контакт>
              <?php endforeach ?>
            </Контакты>
            <?php /*
            <Представители>
              <Представитель>
                <Контрагент>
                  <Отношение>Контактное лицо</Отношение>
                  <Ид><?php echo md5($contragent['user_id']) ?></Ид>
                  <?php if ($contragent['full_name']): ?>
                    <Наименование><?php echo $contragent['full_name'] ?></Наименование>
                  <?php endif ?>
                </Контрагент>
              </Представитель>
            </Представители>
            */ ?>
          </Контрагент>
        <?php endforeach ?>
      </Контрагенты>
      <Товары>
        <?php foreach ($document['products'] as $product): ?>
          <Товар>
            <?php if (!empty($product['guid'])): ?>
              <Ид><?php echo $product['guid'] ?></Ид>
            <?php endif ?>
            <Наименование><?php echo $product['name'] ?></Наименование>
            <БазоваяЕдиница Код="796" НаименованиеПолное="Штука" МеждународноеСокращение="PCE">шт</БазоваяЕдиница>
            <ЦенаЗаЕдиницу><?php echo $product['price_per_item'] ?></ЦенаЗаЕдиницу>
            <Количество><?php echo $product['quantity'] ?></Количество>
            <Сумма><?php echo $product['total'] ?></Сумма>
            <ЗначенияРеквизитов>
              <ЗначениеРеквизита>
                <Наименование>ТипНоменклатуры</Наименование>
                <Значение><?php echo $product['type'] ?></Значение>
              </ЗначениеРеквизита>
            </ЗначенияРеквизитов>
          </Товар>
        <?php endforeach ?>
      </Товары>
      <ЗначенияРеквизитов>
        <?php
        $requisites = array(
          'Заказ оплачен' => !in_array($document['status'], array('on-hold', 'pending')) ? 'true' : 'false',
          'Доставка разрешена' => $document['has_shipping'] ? 'true' : 'false',
          'Отменен' => $document['status'] == 'cancelled' ? 'true' : 'false',
          'Финальный статус' => !in_array($document['status'], array('trash', 'on-hold', 'pending', 'processing')) ? 'true' : 'false',
          'Статус заказа' => $document['status_name'],
          'Дата изменения статуса' => $document['modified_at'],
        );
        if ($document['payment_method_title']) $requisites['Метод оплаты'] = $document['payment_method_title'];
        $requisites = apply_filters('wc1c_query_order_requisites', $requisites, $document);
        foreach ($requisites as $requisite_key => $requisite_value): ?>
          <ЗначениеРеквизита>
            <Наименование><?php echo $requisite_key ?></Наименование>
            <Значение><?php echo $requisite_value ?></Значение>
          </ЗначениеРеквизита>
        <?php endforeach; ?>
      </ЗначенияРеквизитов>
    </Документ>
  <?php endforeach ?>
</КоммерческаяИнформация>

<?php
foreach ($order_post_ids as $order_post_id) {
  update_post_meta($order_post_id, 'wc1c_querying', 1);
}
?>
