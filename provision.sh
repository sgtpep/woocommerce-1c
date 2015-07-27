#!/bin/bash
set -eu -o pipefail

[[ $USER == vagrant ]] || exec sudo -u vagrant bash -$- $0

packages=(apache2 mysql-server php5 php5-mysql php5-gd)
if ! dpkg -s ${packages[@]} &>/dev/null; then
  sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password root"
  sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password root"
  sudo DEBIAN_FRONTEND=noninteractive apt-get install -y ${packages[@]}
fi

sudo sed -i "s/\( APACHE_RUN_USER\)=.*/\1=vagrant/" /etc/apache2/envvars
sudo sed -i "s/\( APACHE_RUN_GROUP\)=.*/\1=vagrant/" /etc/apache2/envvars

sudo sed -i "s|^\(\s*DocumentRoot\) .*|\1 /var/www|" /etc/apache2/sites-available/000-default.conf

sudo a2enmod -q rewrite

sudo apachectl restart

sudo rm -fr /var/www/html
sudo chown vagrant:vagrant /var/www

[[ -x /usr/local/bin/wp ]] || sudo curl -s -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
sudo chmod +x /usr/local/bin/wp

cd /var/www

if ! wp core is-installed 2>/dev/null; then
  wp core download --locale=ru_RU
  wp core config --dbname=wordpress --dbuser=root --dbpass=root
  wp db create
  wp core install --admin_user=admin --admin_password=admin --admin_email=admin@example.com --title=WordPress --url=http://localhost:8080/
  wp plugin update --all
fi

wp plugin is-installed adminer || wp plugin install adminer --activate

if ! wp plugin is-installed woocommerce; then
  wp plugin install woocommerce --activate
  wp core language update
  wp eval "WC_Install::create_pages();"
  wp option add woocommerce_allow_tracking no
  wp option update woocommerce_admin_notices "a:0:{}"
fi

wp theme is-installed storefront || wp theme install storefront --activate

if ! wp plugin is-installed woocommerce-and-1centerprise-data-exchange; then
  ln -s /vagrant/ /var/www/wp-content/plugins/woocommerce-and-1centerprise-data-exchange
  wp plugin activate woocommerce-and-1centerprise-data-exchange
fi

cat > /var/www/wp-cli.yml <<EOF
apache_modules:
  - mod_rewrite
EOF

[[ -f /var/www/.htaccess ]] || wp rewrite structure "/%year%/%monthnum%/%postname%/"

mkdir -p /vagrant/uploads
rm -fr /var/www/wp-content/uploads/woocommerce-1c
ln -s /vagrant/uploads /var/www/wp-content/uploads/woocommerce-1c
