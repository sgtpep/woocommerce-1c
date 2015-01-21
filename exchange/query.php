<?php
if (!defined('ABSPATH')) exit;

header("Content-Type: text/xml; charset=UTF-8");

echo file_get_contents("/tmp/orders.xml");
exit;

$documents = array();
$order_posts = get_posts("post_type=shop_order&post_status=any,trash");
foreach ($order_posts as $order_post) {
  $order = wc_get_order($order_post);
  if (!$order) wc1c_error("Failed to get order");

  $order_line_items = $order->get_items();

  $has_missing_item = false;
  foreach ($order_line_items as $key => $order_line_item) {
    $product_id = $order_line_item['variation_id'] ? $order_line_item['variation_id'] : $order_line_item['product_id'];
    $guid = get_post_meta($product_id, 'wc1c_guid', true);
    if (!$guid) {
      $has_missing_item = true;
      break;
    }

    $order_line_items[$key]['wc1c_guid'] = $guid;
  }
  if ($has_missing_item) continue;

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
    'phone' => "Телефон мобильный",
  );

  $contragents = array();
  foreach (array('billing', 'shipping') as $type) {
    $contragent = array();

    $full_name = array();
    foreach (array('first_name', 'last_name') as $name_key) {
      $meta_key = "_{$type}_$name_key";
      if (empty($order_meta[$meta_key])) continue;

      $full_name[] = $order_meta[$meta_key];
      $contragent[$name_key] = $order_meta[$meta_key];
    }

    $full_name = implode(' ', $full_name);
    if (!$full_name) $full_name = "Гость";
    $contragent['full_name'] = $full_name;
    $contragent['user_id'] = $full_name ? $order_post->post_author : 0;

    if (isset($order_meta["_{$type}_country"])) {
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
  if (array_key_exists($order->status, $statuses)) {
    $order_status_name = $statuses[$order->status];
  }
  else {
    $order_status_name = wc_get_order_status_name($order->status);
  }

  $document = array(
    'order_id' => $order_post->ID,
    'currency' => @$order_meta['_order_currency'],
    'total' => @$order_meta['_order_total'],
    'comment' => $order_post->post_excerpt,
    'contragents' => $contragents,
    'products' => $products,
  );
  list($document['date'], $document['time']) = explode(' ', $order_post->post_date, 2);

  $documents[] = $document;
}

function wc1c_query_output_callback($buffer) {
  global $wc1c_is_error;

  if ($wc1c_is_error) return $buffer;

  header("Content-Type: text/xml; charset=windows-1251");

  return iconv('utf8', 'cp1251', $buffer);
}
ob_start('wc1c_query_output_callback');

echo '<?xml version="1.0" encoding="windows-1251"?>';
?>

<КоммерческаяИнформация ВерсияСхемы="2.08" ДатаФормирования="<?php echo date("Y-m-d", WC1C_TIMESTAMP) ?>T<?php echo date("H:i:s", WC1C_TIMESTAMP) ?>">
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
            <?php if (!empty($contragent['full_name'])): ?>
              <Наименование><?php echo $contragent['full_name'] ?></Наименование>
              <ПолноеНаименование><?php echo $contragent['full_name'] ?></ПолноеНаименование>
            <?php endif ?>
            <?php if (!empty($contragent['first_name'])): ?>
              <Имя><?php echo $contragent['first_name'] ?></Имя>
            <?php endif ?>
            <?php if (!empty($contragent['last_name'])): ?>
              <Фамилия><?php echo $contragent['last_name'] ?></Фамилия>
            <?php endif ?>
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
            <?php foreach ($contragent['contacts'] as $contact_item_name => $contact_item_value): ?>
              <Контакты>
                <КонтактВид><?php echo $contact_item_name ?></КонтактВид>
                <Значение><?php echo $contact_item_value ?></Значение>
              </Контакты>
            <?php endforeach ?>
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
          </Контрагент>
        <?php endforeach ?>
      </Контрагенты>
      <Товары>
        <?php foreach ($products as $product): ?>
          <Товар>
            <?php if (!empty($product['guid'])): ?>
              <Ид><?php echo $product['guid'] ?></Ид>
            <?php endif ?>
            <Наименование><?php echo $product['name'] ?></Наименование>
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
        <?php if (!empty($order_meta['_payment_method_title'])): ?>
          <ЗначениеРеквизита>
            <Наименование>Метод оплаты</Наименование>
            <Значение><?php echo $order_meta['_payment_method_title'] ?></Значение>
          </ЗначениеРеквизита>
        <?php endif ?>
        <ЗначениеРеквизита>
          <Наименование>Заказ оплачен</Наименование>
          <Значение><?php echo !in_array($order_post->post_status, array('wc-on-hold', 'wc-pending')) ? 'true' : 'false' ?></Значение>
        </ЗначениеРеквизита>
        <ЗначениеРеквизита>
          <Наименование>Доставка разрешена</Наименование>
          <Значение><?php echo count($order_shipping_items) ? 'true' : 'false' ?></Значение>
        </ЗначениеРеквизита>
        <ЗначениеРеквизита>
          <Наименование>Отменен</Наименование>
          <Значение><?php echo $order_post->post_status == 'wc-cancelled' ? 'true' : 'false' ?></Значение>
        </ЗначениеРеквизита>
        <ЗначениеРеквизита>
          <Наименование>Финальный статус</Наименование>
          <Значение><?php echo !in_array($order_post->post_status, array('trash', 'wc-on-hold', 'wc-pending', 'wc-processing')) ? 'true' : 'false' ?></Значение>
        </ЗначениеРеквизита>
        <ЗначениеРеквизита>
          <Наименование>Статус заказа</Наименование>
          <Значение><?php echo $order_status_name ?></Значение>
        </ЗначениеРеквизита>
        <ЗначениеРеквизита>
          <Наименование>Дата изменения статуса</Наименование>
          <Значение><?php echo $order_post->post_modified ?></Значение>
        </ЗначениеРеквизита>
      </ЗначенияРеквизитов>
    </Документ>
  <?php endforeach ?>
</КоммерческаяИнформация>
