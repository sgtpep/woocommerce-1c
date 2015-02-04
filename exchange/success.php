<?php
if (!defined('ABSPATH')) exit;

$order_statuses = array_keys(wc_get_order_statuses());
$order_posts = get_posts(array(
  'post_type' => 'shop_order',
  'post_status' => $order_statuses,
  'meta_query' => array(
    array(
      'key' => 'wc1c_querying',
      'value' => 1,
    ),
    array(
      'key' => 'wc1c_queried',
      'compare' => "NOT EXISTS",
    ),
  ),
));

foreach ($order_posts as $order_post) {
  update_post_meta($order_post->ID, 'wc1c_queried', 1);
}
