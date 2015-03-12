=== WooCommerce and 1C:Enterprise (1С:Предприятие) Data Exchange ===
Contributors: sgtpep
Donate link: https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=500&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: 1c, 1c-enterprise, commerceml, integration, e-commerce, ecommerce, commerce, shop, cart, woothemes, woocommerce
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.5

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

Пожалуйста, перед использованием плагина прочитайте следующее:

* [инструкцию по установке](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/installation/)
* [часто задаваемые вопросы](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/faq/)

По возникшим вопросам и предложениям можно [связаться с автором](http://danil.iamsync.com/).

Поддержать разработку и автора можно взносом через [банковскую карту или Яндекс.Деньги](https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=500&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=).

= In English =

Provides data exchange between eCommerce plugin WooCommerce and business application "1C:Enterprise 8. Trade Management".

Features:

* Product exchange: group (categories), attributes and values, product list and product variations, images, properties, requisites, prices, remains for products.
* Order exchange: two way exchange of order information between website and application.
* Partial and full syncronization.
* Effective usage of RAM on server.
* Support for compressed data exchange.
* Transactions and strict error checking: DB updates on successfull data exchange only.

Please, read the following before using this plugin:

* [installation instructions](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/installation/)
* [frequently asked questions](https://wordpress.org/plugins/woocommerce-and-1centerprise-data-exchange/faq/)

If you have any question or proposal, please [contact the author](http://danil.iamsync.com/).

== Installation ==

Необходимо учесть, что для обмена большими объемами данных может понадобиться произвести дополнительную настройку веб-сервера. На недорогих shared-хостингах часто такой возможности нет, а настроены они под крайне консервативный режим работы. Поэтому рекомендуется использовать VPS/VDS-хостинги. Например, от [DigitalOcean](https://www.digitalocean.com/?refcode=4f1711dd3d2c) (реферальная ссылка, регистрирующийся получает $10 на счет).

Главное требование для работы плагина с большими выгрузками, отсекающее возможность использования большой части shared-хостингов, – это возможность неограниченного времени исполнения PHP-скриптов, т.к. для первой полной выгрузки может понадобиться больше времени, чем разрешено на сервере.

= Настройка =

Вначале вам необходимо установить и активировать плагин WooCommerce, т.к. этот плагин зависит от него. Для этого зайдите в панель управления WordPress, выберите "Плагины" → "Добавить новый". В поисковом поле введите название плагина (или часть) и кликните "Искать плагины". Установите найденный плагин, кликнув "Установить сейчас".

В 1С в качестве адреса в настройках обмена с сайтом необходимо один из адресов вида:

* http://example.com/wp-content/plugins/woocommerce-and-1centerprise-data-exchange/exchange.php
* или http://example.com/wc1c/exchange/, если на сайте включены постоянные ссылки ("Настройки" → "Постоянные ссылки")

где example.com – доменное имя сайта интернет-магазина.

В качестве имени пользователя и пароля в 1С следует указать действующие на сайте имя и пароль активного пользователя с ролью "Shop Manager" или Администратор.

Весь процесс настройки 1С:Предприятия для обмена данными с сайтом хорошо описан в руководстве к одному из коммерческих движков интернет-магазина: http://www.cs-cart.ru/docs/4.1.x/rus_build_pack/1c/instruction/index.html#id3.

= Технические рекомендации =

Рекомендуется изменить тип хранилища всех таблиц базы данных сайта на InnoDB. Это добавит транзакционность в процесс обмена данными: изменения в базе данных сайта будут применяться только в случае успешного завершения процесса обмена.

Выполнение PHP на сервере необходимо настроить так, чтобы не было лимитов на время исполнения скриптов плагина. В случае использования связки Apache + mod_php (рекомендуется как наиболее простая связка) при дефолтных настройках лимита не будет. В случае использования FastCGI и/или nginx может потребоваться дополнительная их настройка для снятия лимитов на время исполнения (например, изменение FcgidConnectTimeout для mod_fcgid; request_terminate_timeout, fastcgi_read_timeout для nginx).

1С закачивает на сервер выгрузку с помощью POST-запроса. Возможно, понадобится увеличить лимит объема данных, отправляемых по POST. В php.ini за это отвечает значение post_max_size. В случае использования FastCGI и/или nginx может понадобится увеличить этот лимит также в их настройках (например, FcgidMaxRequestLen для mod_fcgid; client_max_body_size, send_timeout для nginx).

Если PHP выполняется в режиме FastCGI, а 1С при проверке соединения с сервером просит проверить имя пользователя и пароль, хотя они указаны верно, то необходимо в файл .htaccess перед строкой "# BEGIN WordPress" вставить:
`
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
`

== Frequently Asked Questions ==

= Предоставляете ли вы поддержку? =
Данный плагин является свободной некоммерческой разработкой и не приносит автору прямой прибыли. Полноценная поддержка же пользователей стоит времени и денег. Поэтому не стоит ожидать оказания бесплатной поддержки на уровне коммерческих продуктов. Предполагается, что пользователь, как минимум, самостоятельно попробует разобраться с установкой и настройкой продуктов 1С, WooCommerce и данного плагина, используя имеющуюся документацию к плагину и информацию в интернете. С автором можно связаться через [форму обратной связи](http://danil.iamsync.com/) в случае, если:

* возникли определенные непреодолимые трудности в процессе установки или настройки плагина;
* в процессе обмена данными возникают ошибки;
* не устраивает текущий алгоритм обмена данными плагина, и есть представление, что и как должно работать иначе.

= Как удалить с сайта все данные, созданные в процессе обмена? =
Если вы используете [WP-CLI](http://wp-cli.org/), то можно из директории плагина выполнить команду `wp eval-file ./clean.php`. Также можно, будучи авторизованным в WordPress, перейти по адресу http://example.com/wp-content/plugins/woocommerce-and-1centerprise-data-exchange/clean.php (где exchange.com – домен сайта) и нажать на отобразивушуюся кнопку.

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
