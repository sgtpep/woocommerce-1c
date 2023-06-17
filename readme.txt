=== WooCommerce and 1C:Enterprise/1С:Предприятие Data Exchange ===
Contributors: sgtpep
Donate link: https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=1000&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: 1c, 1c-enterprise, commerceml, integration, e-commerce, ecommerce, commerce, shop, cart, woothemes, woocommerce
Requires at least: 3.8
Tested up to: 5.4
Stable tag: 0.9.20

Provides data exchange between WooCommerce plugin and business application "1C:Enterprise 8. Trade Management" (and compatible ones).

== Description ==

= In Russian =

Предоставляет обмен данными между плагином для электронной коммерции WooCommerce и приложением для бизнеса "1C:Предприятие 8. Управление торговлей" (и совместимыми).

> Для достижения корректной работы плагина могут потребоваться базовые навыки администрирования веб-серверов (просмотр логов, изменение настроек php и веб-серверов и др.) А настройка плагина осуществляется добавлением констант в `wp-config.php` (посмотреть доступные можно командой: `grep -r "define('WC1C_"`) и функций [фильтров и действий](https://codex.wordpress.org/Plugin_API) в `functions.php` в папке активной темы (посмотреть доступные можно командой: `grep -r "do_action\|apply_filters"`).

Особенности:

* Выгрузка товаров: группы (категории), свойства и значения, список товаров и вариантов, изображения, свойства, реквизиты, цены, остатки товаров.
* Обмен заказами: двусторонний обмен информацией о заказах на сайте и в приложении.
* Полная и частичная синхронизация.
* Экономичное использование оперативной памяти сервера.
* Поддержка передачи данных в сжатом виде.
* Транзакционность и строгая проверка на ошибки: данные обновляются в БД только в случае успешного обмена.

Пожалуйста, перед использованием плагина прочитайте следующее:

* [инструкцию по установке](./installation/)
* [часто задаваемые вопросы](./faq/)

Поддержать разработку и автора можно взносом через [банковскую карту или Яндекс.Деньги](https://money.yandex.ru/embed/donate.xml?account=410011766586472&quickpay=donate&payment-type-choice=on&default-sum=1000&targets=%D0%9F%D0%BB%D0%B0%D0%B3%D0%B8%D0%BD+%22%D0%9E%D0%B1%D0%BC%D0%B5%D0%BD+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83+WooCommerce+%D0%B8+1%D0%A1%3A%D0%9F%D1%80%D0%B5%D0%B4%D0%BF%D1%80%D0%B8%D1%8F%D1%82%D0%B8%D0%B5%D0%BC%22&target-visibility=on&project-name=&project-site=https%3A%2F%2Fwordpress.org%2Fplugins%2Fwoocommerce-and-1centerprise-data-exchange%2F&button-text=05&fio=on&mail=on&successURL=).

Соавторы: Максим Дубовик [@lufton](https://github.com/lufton), [@chrme](https://github.com/chrme), [@shsl](https://github.com/shsl), Арсений Дугин [@sklazer](https://github.com/sklazer), Геннадий Ковшенин [@soulseekah](https://github.com/soulseekah), Vladyslav [@qwave](https://github.com/qwave), Александр Воробьев [@Alex01d](https://github.com/Alex01d).

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

* [installation instructions](./installation/)
* [frequently asked questions](./faq/)

Contributors: Maksim Dubovik [@lufton](https://github.com/lufton), [@chrme](https://github.com/chrme), [@shsl](https://github.com/shsl), Arseny Dugin [@sklazer](https://github.com/sklazer), Gennady Kovshenin [@soulseekah](https://github.com/soulseekah), Vladyslav [@qwave](https://github.com/qwave), Alexander Vorobyev [@Alex01d](https://github.com/Alex01d).

= License =

"WooCommerce and 1C:Enterprise Data Exchange" is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
"WooCommerce and 1C:Enterprise Data Exchange" is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with "WooCommerce and 1C:Enterprise Data Exchange". If not, see http://www.gnu.org/licenses/gpl-3.0.html.

== Installation ==

Необходимо учесть, что для обмена большими объемами данных может понадобиться произвести дополнительную настройку веб-сервера. На недорогих shared-хостингах часто такой возможности нет, а настроены они под крайне консервативный режим работы. Поэтому рекомендуется использовать VPS/VDS-хостинги. Например, от [DigitalOcean](https://www.digitalocean.com/?refcode=4f1711dd3d2c) (реферальная ссылка, регистрирующийся получает $10 на счет).

Главное требование для работы плагина с большими выгрузками, отсекающее возможность использования большой части shared-хостингов, – это возможность неограниченного времени исполнения PHP-скриптов, т.к. для первой полной выгрузки может понадобиться больше времени, чем разрешено на сервере.

= Настройка =

Вначале вам необходимо установить и активировать плагин WooCommerce, т.к. этот плагин зависит от него. Для этого зайдите в панель управления WordPress, выберите "Плагины" → "Добавить новый". В поисковом поле введите название плагина (или часть) и кликните "Искать плагины". Установите найденный плагин, кликнув "Установить сейчас".

В 1С в качестве адреса в настройках обмена с сайтом необходимо один из адресов вида:

* http://example.com/?wc1c=exchange
* или http://example.com/wc1c/exchange/, если на сайте включены постоянные ссылки ("Настройки" → "Постоянные ссылки")

где example.com – доменное имя сайта интернет-магазина.

В качестве имени пользователя и пароля в 1С следует указать действующие на сайте имя и пароль активного пользователя с ролью "Shop Manager" или Администратор.

Весь процесс настройки 1С:Предприятия для обмена данными с сайтом хорошо описан в инструкции к одному из коммерческих движков интернет-магазина: http://www.cs-cart.ru/docs/4.1.x/rus_build_pack/1c/instruction/index.html#id3, которой можно следовать до раздела "Настройки в интернет-магазине".

Обратите внимание, что если вы собираетесь учитывать остатки товаров в магазине, необходимо включитб управление запасами в WooCommerce: "Настройки" → "Товары" → "Запасы" → "Включить управление запасами".

= Технические рекомендации =

Рекомендуется изменить тип хранилища всех таблиц базы данных сайта на InnoDB. Это добавит транзакционность в процесс обмена данными: изменения в базе данных сайта будут применяться только в случае успешного завершения процесса обмена.

Выполнение PHP на сервере необходимо настроить так, чтобы не было лимитов на время исполнения скриптов плагина. В случае использования связки Apache + mod_php (рекомендуется как наиболее простая связка) при дефолтных настройках лимита не будет. В случае использования FastCGI и/или nginx может потребоваться дополнительная их настройка для снятия лимитов на время исполнения (например, изменение FcgidConnectTimeout для mod_fcgid; request_terminate_timeout, fastcgi_read_timeout для nginx).

1С закачивает на сервер выгрузку с помощью POST-запроса. Возможно, понадобится увеличить лимит объема данных, отправляемых по POST. В php.ini за это отвечает значение post_max_size. В случае использования FastCGI и/или nginx может понадобится увеличить этот лимит также в их настройках (например, FcgidMaxRequestLen для mod_fcgid; client_max_body_size, send_timeout для nginx).

Если PHP выполняется в режиме FastCGI, а 1С при проверке соединения с сервером просит проверить имя пользователя и пароль, хотя они указаны верно, то необходимо в файл .htaccess после строки `RewriteEngine On` вставить строку `RewriteRule . - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]`, а также попробовать оба варианта адреса обмена (полный и короткий). Необходимо учесть, что изменения в .htaccess перезатираются при сохранении настроек постоянных ссылок и некоторых плагинов из админки WordPress.

Пример конфига пула для PHP7-FPM:
```
php_admin_value[post_max_size]=1000M
php_admin_value[upload_max_filesize]=1000M
php_admin_value[request_terminate_timeout]=0
php_admin_value[max_execution_time]=9000000s
php_admin_value[memory_limit]=512M
```

Пример конфига nginx:
```
server {
client_max_body_size 1000m;
# ... etc configs
location ~ \.php$ {
# ... etc configs
fastcgi_read_timeout 60000s;
}
```

== Frequently Asked Questions ==

= Предоставляете ли вы поддержку? =

Данный плагин является свободной некоммерческой разработкой и не приносит автору прямой прибыли. Полноценная поддержка же пользователей стоит времени и денег. Поэтому не стоит ожидать оказания бесплатной поддержки на уровне коммерческих продуктов. Предполагается, что пользователь, как минимум, самостоятельно попробует разобраться с установкой и настройкой продуктов 1С, WooCommerce и данного плагина, используя имеющуюся документацию к плагину и информацию в интернете. С автором можно связаться через [форму обратной связи](http://danil.iamsync.com/) в случае, если:

* возникли определенные непреодолимые трудности в процессе установки или настройки плагина;
* в процессе обмена данными возникают ошибки;
* не устраивает текущий алгоритм обмена данными плагина, и есть представление, что и как должно работать иначе.

= Почему не работает авторизация даже после добавления рекомендуемой строки в .htaccess? =

Если вы используете Windows, то необходимо установить утилиту cURL. В OS X и Linux она, как правило, уже присутствует в системе. Необходимо в терминале выполнить команду:
`curl -D - -u "логин:пароль" "http://адрес-обмена?type=catalog&mode=checkauth"`
Замените `логин`, `пароль`, `адрес-обмена` на соответствующие значения. В выводе команды будет содержаться сообщение об ошибке от сервера.

= Как удалить с сайта все данные, созданные в процессе обмена? =

Если вы используете [WP-CLI](http://wp-cli.org/), то можно из директории плагина выполнить команду `wp eval-file ./clean.php`. Также можно, будучи авторизованным в WordPress, перейти по адресу http://example.com/?wc1c=clean или http://example.com/wc1c/clean (где exchange.com – домен сайта) и нажать на появившуюся кнопку.

= Как вручную воспроизвести импорт товаров? =

После обмена с 1С плагин сохраняет полученные файлы импорта (до следующего обмена) в директории `wp-content/uploads/woocommerce-1c/catalog` в файлах вида `import.xml` (информация о группах, свойствах и товарах) и `offers.xml` (информация о ценах и вариантах предложений). Для отладки в процессе интеграции плагина полезно иметь возможность вручную повторить импорт из этих файлов. Для этого в браузере можно последовательно перейти по следующим адресам, будучи авторизованным как администратор или менеджер магазина:

* `<адрес обмена>?type=catalog&mode=import&filename=import.xml`
* `<адрес обмена>?type=catalog&mode=import&filename=offers.xml`

Имена XML-файлов, возможно, придется заменить на актуальные из директории `wp-content/uploads/woocommerce-1c/catalog`.

= Как осуществляется обмен заказами? =

1. 1С запрашивает с сайта заказы, которые еще не запрашивались им ранее, и создает по ним несогласованные и непроведенные заказы у себя.
2. Если в 1С есть заказы, ранее полученные с сайта, но неполученные на предыдущем этапе, то 1С передает их на сайт.
3. Плагин сайта ищет для каждого заказа соответствующий заказ у себя и либо создает новый заказ, либо обновляет имеющийся.
4. Плагин сайта совершает следующие изменения в заказе:
  * Если заказ был помечен к удалению в 1С, то – помещает соответствующий на сайте заказ в корзину, иначе – восстанавливает.
  * Если у заказа в 1С был выставлен статус отличный от "Не согласован", то у заказа на сайте выставляется статус "В обработке".
  * Если заказ в 1С проведен, то у заказа на сайте выставляется статус "Выполнен".
  * Иначе – оставляет у заказа на сайте статус по умолчанию "На удержании".

= Где можно найти исходники плагина? =

Исходники размещаются в предоставляемом WordPress.org [svn-репозитории](../developers/). Также имеется [git-зеркало](https://github.com/sgtpep/woocommerce-1c).

= Как работать с репозиторием через git-svn? =

Инициализация:

* `git clone git@github.com:sgtpep/woocommerce-1c.git`
* `cd ./woocommerce-1c`
* `git svn init https://plugins.svn.wordpress.org/woocommerce-and-1centerprise-data-exchange/trunk/`
* `git update-ref refs/remotes/git-svn master`
* `git svn rebase --log-window-size=100000`

Коммит:

* `git commit`
* `git svn dcommit`
* `git push`

== Screenshots ==

1. Список выгруженных из 1С в WooCommerce товаров с колонкой идентификатора позиции номенклатуры в 1С.
2. Карточка товара WooCommerce с выгруженными из 1С наименованием, описанием для сайта, присоединенными изображениями, артикулом и ценой.
3. Варианты настраиваемого товара, сформированные из нескольких предложений одного товара в 1С.
4. Пример отображения выгруженного настраиваемого товара на сайте с выпадающими списками доступных опций.
5. Свойства товара, сформированные из значений свойств и реквизитов товара в 1С.
6. Дерево категорий товаров с колонкой идентификатора группы номенклатуры в 1С.
7. Общие свойства товаров WooCommerce, сформированные по выгруженным из 1С свойствам и значениям свойств товаров, с колонкой идентификатора.

== Changelog ==

= 0.9.17 =

- Максим Дубовик [@lufton](https://github.com/lufton) исправил использование использование устаревших геттеров и `OUTOFSTOCK_STATUS`.

= 0.9.18 =

- Максим Дубовик [@lufton](https://github.com/lufton) исправил синхронизацию статусов заказов.
- [@Lomerill](https://github.com/Lomerill) исправил передачу ID контрагента.
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность переопределять slug опций с помощью константы `WC1C_USE_GUID_AS_PROPERTY_OPTION_SLUG`.
- Максим Дубовик [@lufton](https://github.com/lufton) реализовал очистку файлов, остающихся после обмена (orders-*.xml, import_fileas и т.д.)
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность сопоставлять атрибуты по заголовку с помощью констант `WC1C_MATCH_PROPERTIES_BY_TITLE` и `WC1C_MATCH_PROPERTY_OPTIONS_BY_TITLE`.
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность сопоставлять категории по заголовку с помощью константы `WC1C_MATCH_CATEGORIES_BY_TITLE`.
- Максим Дубовик [@lufton](https://github.com/lufton) исправил отображение символа UAH.
- Максим Дубовик [@lufton](https://github.com/lufton) ускорил импорт товаров, удалив дублирующий код.
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность сопоставлять номенклатуру с товарами в WooCommerce по артикулу с помощью константы `WC1C_MATCH_BY_SKU`.
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность отключать управление остатками с помощью константы `WC1C_MANAGE_STOCK`.
- Арсений Дугин [@sklazer](https://github.com/sklazer) исправил ошибку "Failed open archive %s with error code 19" при обмене данными с архивом большого размера.
- [@Lomerill](https://github.com/Lomerill) добавил поддержку чтения статуса товара из элемента `Товар.Статус`.

= 0.9.5 =

- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность обновлять постоянную ссылку продукта при каждом импорте.
- [@krakazyabra](https://github.com/krakazyabra) заменил устаревшую функцию `update_woocommerce_term_meta` на `update_term_meta`.
- Максим Дубовик [@lufton](https://github.com/lufton) добавил возможность переопределять/расширять реквизиты заказов через фильтр 'wc1c_query_order_requisites' и исправил формирование имен контрагентов и получение цен.
- Vladyslav [@qwave](https://github.com/qwave) исправил ошибку при получении статусов заказов.

= 0.9 =

Максим Дубовик [@lufton](https://github.com/lufton) исправил передачу следующих вещей: контакты клиентов, дополнительные типы цен, габариты товаров и добавил возможность переопределять статус "нет в наличии" с помощью константы `WC1C_OUTOFSTOCK_STATUS`.

= 0.8 =

Исправлена проблема со статусом невыполненого заказа для WooCommerce новее 3.x, когда включено управление запасами (спасибо, [@chrme](https://github.com/chrme)). Исправлено некорректное использование Rewrite API, негативно влиявшее на производительность (спасибо, Геннадий Ковшенин [@soulseekah](https://github.com/soulseekah).

= 0.7 =

Добавлена в API возможность переопределения очистки категорий. Добавлена в API возможность определять, является ли обмен полным или частичным. Отключена очистка мета-данных при удалении плагина. 

= 0.6 =

Добавлена ссылка для сбора пожертований. Добавлено сохранение заданных пользователем из WooCommerce изображений товаров, для которых отсутствуют изображения в 1С. Добавлена возможность очистки всех данных магазина (см. FAQ). Добавлена в API возможность переопределения передаваемых в 1С заказов.

= 0.5 =

Добавлена в API возможность предотвращения перезаписи заголовка, краткого описания, полного описания и галереи продукта при каждом последующем обмене. Изменена логика обмена заказами.

= 0.4 =

Добавлено приведение наименований реквизитов к человекочитаемому виду. Добавлена поддержка импорта из поля 1С "Файл описания для сайта". Значение поля 1С "Текстовое описание" помещается в "Краткое описание товара" WooCommerce.

= 0.3 =

Добавлена поддержка распаковки архивов средствами системы. Добавлена возможность указания прямого адреса скрипта без необходимости включения постоянных ссылок. Значение поля 1С "Наименование для печати" используется для заголовка товара вместо значения поля "Рабочее название".

= 0.2 =

Добавлено базовое API с помощью фильтров и действий.

= 0.1 =

Первая версия.
