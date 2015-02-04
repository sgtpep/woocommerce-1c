=== WooCommerce and 1C:Enterprise (1С:Предприятие) Data Exchange ===
Contributors: sgtpep
Donate link: http://
Tags: 1c, 1c-enterprise, commerceml, integration, e-commerce, ecommerce, commerce, shop, cart, woothemes, woocommerce
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.5
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Provides data exchange between WooCommerce plugin and business application "1C:Enterprise 8. Trade Management" (and compatible ones).

== Description ==

= In Russian =

Предоставляет обмен данными между плагином для электронной коммерции WooCommerce и приложением для бизнеса "1C:Предприятие 8. Управление торговлей" (и совместимыми).

Особенности:

* Выгрузка товаров: группы (категории), свойства и значения, список товаров и вариантов, изображения, свойства, реквизиты, цены, остатки товаров.
* Обмен заказами: двусторонний обмен информацией о заказах на сайте и в приложении.
* Полная и частичная синхронизация.
* Экономичное использование оперативной памяти сервера.
* Поддержка передачи данных в сжатом виде.
* Транзакционность и строгая проверка на ошибки: данные обновляются в БД только в случае успешного обмена.

Пожалуйста, перед использованием прочитайте [инструкцию по установке](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/installation/) и [часто задаваемые вопросы](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/faq/).

По всем вопросам и предложениям, пожалуйста, [свяжитесь с автором](http://danil.iamsync.com/).

= In English =

Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".

Features:

* Product exchange: group (categories), attributes and values, product list and product variations, images, properties, requisites, prices, remains for products.
* Order exchange: two way exchange of order information between website and application.
* Partial and full syncronization.
* Effective usage of RAM on server.
* Support for compressed data exchange.
* Transactions and strict error checking: DB updates on successfull data exchange only.

Please, read [installation instructions](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/installation/) and [frequently asked questions](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/faq/) before use.

If you have any question or proposal, please [contact the author](http://danil.iamsync.com/).

== Installation ==

Вначале вам необходимо установить и активировать плагин WooCommerce, т.к. этот плагин зависит от него. Для этого зайдите в панель управления WordPress, выберите "Плагины" → "Добавить новый". В поисковом поле введите название плагина (или часть) и кликните "Искать плагины". Установите найденный плагин, кликнув "Установить сейчас".

В 1С в качестве адреса в настройках обмена с сайтом необходимо один из адресов вида:

* http://example.com/wp-content/plugins/woocommerce-and-1centerprise-data-exchange/exchange.php
* или http://example.com/wc1c/exchange/, если на сайте включены постоянные ссылки ("Настройки" → "Постоянные ссылки")

где example.com – доменное имя сайта интернет-магазина.

В качестве имени пользователя и пароля в 1С следует указать действующие на сайте имя и пароль активного пользователя с ролью "Shop Manager" или Администратор.

= Технические рекомендации =

Рекомендуется изменить тип хранилища всех таблиц базы данных сайта на InnoDB. Это добавит транзакционность в процесс обмена данными: изменения в базе данных сайта будут применяться только в случае успешного завершения процесса обмена.

Выполнение PHP на сервере необходимо настроить так, чтобы не было лимитов на время исполнения скриптов плагина. В случае использования связки Apache + mod_php (рекомендуется) при дефолтных настройках лимита не будет. В случае использования FastCGI и/или nginx может потребоваться дополнительная их настройка для снятия лимитов на время исполнения.

Если PHP на сервере работает в режиме FastCGI, а 1С при проверке соединения с сервером просит проверить имя пользователя и пароль, хотя они указаны верно, то необходимо в файл .htaccess перед строкой:
`RewriteRule . /index.php [L]`
вставить строку:
`RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]`

== Frequently Asked Questions ==

= Предоставляете ли вы поддержку? =
Да, с автором можно свободно связаться, используя [форму обратной связи](http://danil.iamsync.ru/).

== Screenshots ==

1. Список выгруженных из 1С в WooCommerce товаров с колонкой идентификатора позиции номенклатуры в 1С.
2. Карточка товара WooCommerce с выгруженными из 1С наименованием, описанием для сайта, присоединенными изображениями, артикулом и ценой.
3. Варианты настраиваемого товара, сформированные из нескольких предложений одного товара в 1С.
4. Пример отображения выгруженного настраиваемого товара на сайте с выпадающими списками доступных опций.
5. Свойства товара, сформированные из значений свойств и реквизитов товара в 1С.
6. Дерево категорий товаров с колонкой идентификатора группы номенклатуры в 1С.
7. Общие свойства товаров WooCommerce, сформированные по выгруженным из 1С свойствам и значениям свойств товаров, с колонкой идентификатора.

== Changelog ==

= 0.5 =
Добавлена в API возможность предотвращения перезаписи заголовка, краткого описания, полного описания и галереи продукта при каждом последующем обмене. Изменена логика обмена заказами.

= 0.4 =
Добавлено приведение наименований реквизитов к человекочитаемому виду. Добавлена поддержка импорта из поля 1С "Файл описания для сайта". Значение поля 1С "Текстовое описание" помещается в "Краткое описние товара" WooCommerce.

= 0.3 =
Добавлена поддержка распаковки архивов средствами системы. Добавлена возможность указания прямого адреса скрипта без необходимости включения постоянных ссылок. Значение поля 1С "Наименование для печати" используется для заголовка товара вместо значения поля "Рабочее название".

= 0.2 =
Добавлено базовое API с помощью фильтров и действий.

= 0.1 =
Первая версия.
