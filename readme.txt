=== WooCommerce and 1C:Enterprise (1С:Предприятие) Data Exchange ===
Contributors: sgtpep
Donate link: http://
Tags: 1c, 1c-enterprise, commerceml, integration, e-commerce, ecommerce, commerce, shop, cart, woothemes, woocommerce
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 0.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".

== Description ==

Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".

Features:

* Product exchange: group (categories), attributes and values, product list and product variations, images, properties, requisites, prices, remains for products.
* Order exchange: two way exchange of order information between website and application.
* Partial and full syncronization.
* Effective usage of RAM on server.
* Support for compressed data exchange.
* Transactions and strict error checking: DB updates on successfull data exchange only.

= In Russian =

Предоставляет обмен данными между плагином для электронной коммерции WooCommerce и приложением для бизнеса "1C:Предприятие 8. Управление торговлей".

Особенности:

* Выгрузка товаров: группы (категории), свойства и значения, список товаров и вариантов, изображения, свойства, реквизиты, цены, остатки товаров.
* Обмен заказами: двусторонний обмен информацией о заказах на сайте и в приложении.
* Полная и частичная синхронизация.
* Экономичное использование оперативной памяти сервера.
* Поддержка передачи данных в сжатом виде.
* Транзакционность и строгая проверка на ошибки: данные обновляются в БД только в случае успешного обмена.

== Installation ==

First, you should install and activate WooCommerce plugin, as this plugin depends on it.

Also this plugin requires enabled permalinks. You should enable them in Settings → Permalinks panel.

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of the plugin, log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New". In the search field type part of the plugin name and click "Search Plugins". Once you’ve found the plugin you can view details about it and install it by simply clicking "Install Now".

You should provide website address for syncronization in "1C:Trade Management", like http://example.com/wc1c/exchange. You should also provide actual username and password of active website user with role "Shop Manager" or Administrator.

= In Russian =

Вначале вам необходимо установить и активировать плагин WooCommerce, т.к. этот плагин зависит от него.

Также этот плагин требует включенных постоянных ссылок. Вам следует включить их в панели: Настройки → Постоянные ссылки.

Автоматическая установка является простейшим способом, т.к. WordPress сам осуществит передачу файлов и вам не нужно будет покидать браузер. Чтобы произвести автоматическую установку плагина, зайдите в вашу панель управления WordPress, перейдите в меню плагинов и кликните "Добавить новый". В поисковом поле введите часть названия плагина и кликните "Искать плагины". После того, как вы найдете плагин, вы можете просмотреть информацию о нем и установить его, кликнув "Установить сейчас".

В "1С:Управление торговлей" в качестве адреса сайта для синхронизации необходимо указать адрес вида http://example.com/wp-content/plugins/woocommerce-1c/exchange.php. В качестве имени пользователя и пароля следует указать действующие на сайте имя и пароль активного пользователя с ролью "Shop Manager" или Администратор.

== Frequently Asked Questions ==

= Do you provide a support? =
Yes, feel free to get in touch with me using [contact form](http://danil.iamsync.com/).

= In Russian =

= Предоставляете ли вы поддержку? =
Да, со мной можно свободно связаться, используя [форму обратной связи](http://danil.iamsync.ru/).

== Screenshots ==

== Changelog ==

= 0.1.0 =
Initial release.

= In Russian =

= 0.1.0 =
Первая версия.
