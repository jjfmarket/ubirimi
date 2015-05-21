# Install our dependencies

exec { "apt-get update":
  path => "/usr/bin",
}

package { "python-software-properties":
  ensure => present,
  before => Exec["add-apt-repository ppa:ondrej/php5"],
  require => Exec["apt-get update"],
}

exec { "add-apt-repository ppa:ondrej/php5":
  command => "/usr/bin/add-apt-repository ppa:ondrej/php5",
  require => Package["python-software-properties"]
}

exec { "apt-get update ppa:ondrej/php5":
  command => "/usr/bin/apt-get update",
  require => Exec["add-apt-repository ppa:ondrej/php5"],
}

exec { "add-apt-repository ppa:ondrej/mysql-5.6":
  command => "/usr/bin/add-apt-repository ppa:ondrej/mysql-5.6",
  require => Package["python-software-properties"]
}

exec { "apt-get update ppa:ondrej/mysql-5.6":
  command => "/usr/bin/apt-get update",
  require => Exec["add-apt-repository ppa:ondrej/mysql-5.6"],
}

exec { "add-apt-repository ppa:ondrej/apache2":
  command => "/usr/bin/add-apt-repository ppa:ondrej/apache2",
  require => Exec["add-apt-repository ppa:ondrej/php5"]
}

exec { "apt-get update ppa:ondrej/apache2":
  command => "/usr/bin/apt-get update",
  require => [Exec["add-apt-repository ppa:ondrej/apache2"], Exec["add-apt-repository ppa:ondrej/php5"]],
}

package { "curl":
  ensure => present,
  require => Exec["apt-get update ppa:ondrej/apache2"],
}

exec { 'install composer':
  command => '/usr/bin/curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer',
  require => Package['curl'],
}

package {"apache2":
  ensure => present,
  require => [Exec["apt-get update ppa:ondrej/php5"], Exec["apt-get update ppa:ondrej/apache2"]]
}

service { "apache2":
  ensure => "running",
  require => Package["apache2"]
}

package { "libapache2-svn":
  ensure => present,
  require => [Exec["apt-get update ppa:ondrej/apache2"],Package["apache2"]]
}

# mysql packages
package {["mysql-server", "mysql-client"]:
  ensure => installed,
  require => Exec["apt-get update ppa:ondrej/apache2", "apt-get update ppa:ondrej/mysql-5.6"]
}

service { "mysql":
  ensure  => running,
  require => Package["mysql-server"],
}

package { ["php5-common",
          "libapache2-mod-php5",
          "php5-cli",
          "php-apc",
          "php5-mysql",
          "php5-gd",
          "php5-mysqlnd",
          "php5-curl",
          "php5-xdebug"
          ]:
  ensure => installed,
  notify => Service["apache2"],
  require => [Exec["apt-get update ppa:ondrej/apache2"], Package["mysql-client"], Package["apache2"]],
}

exec { "/usr/sbin/a2enmod rewrite" :
  unless => "/bin/readlink -e /etc/apache2/mods-enabled/rewrite.load",
  notify => Service[apache2],
  require => Package["apache2"]
}

exec { "/usr/sbin/a2enmod dav" :
  unless => "/bin/readlink -e /etc/apache2/mods-enabled/rewrite.load",
  notify => Service[apache2],
  require => Package["apache2"]
}

exec { "/usr/sbin/a2enmod dav_svn" :
  unless => "/bin/readlink -e /etc/apache2/mods-enabled/rewrite.load",
  notify => Service[apache2],
  require => Package["apache2"]
}

exec { "/usr/sbin/a2enmod macro" :
  unless => "/bin/readlink -e /etc/apache2/mods-enabled/macro.load",
  notify => Service[apache2],
  require => Package["apache2"]
}

package { ["git"]:
  ensure => installed
}

package { ["mc"]:
  ensure => installed
}

package { ["rabbitmq-server"]:
  ensure => installed
}

exec { '/usr/lib/rabbitmq/bin/rabbitmq-plugins enable rabbitmq_management':
  path => "/usr/bin:/usr/sbin:/bin",
  environment => "HOME=/root",
  command => '/usr/lib/rabbitmq/bin/rabbitmq-plugins enable rabbitmq_management',
  require => Package['rabbitmq-server'],
}

package { ["supervisor"]:
  ensure => installed
}

exec { '/usr/bin/service supervisor restart':
  command => '/usr/bin/service supervisor restart',
  require => Package['supervisor'],
}

package { ["subversion"]:
  ensure => installed
}

# Set up a new VirtualHost

file { "/var/www/products":
  ensure  => "link",
  target  => "/vagrant",
  require => Package["apache2"],
  notify  => Service["apache2"],
  replace => yes,
  force   => true,
}

file { "/etc/apache2/sites-available/ubirimi":
  ensure => "link",
  target => "/vagrant/manifests/assets/ubirimi.conf",
  require => Package["apache2"],
  notify => Service["apache2"],
  replace => yes,
  force => true,
}

file { "/etc/apache2/sites-enabled/ubirimi.conf":
  ensure  => "link",
  target  => "/vagrant/manifests/assets/ubirimi.conf",
  require => Package["apache2"],
  notify  => Service["apache2"],
  replace => yes,
  force   => true,
}

file { "/etc/apache2/conf-enabled/svn.conf":
  ensure  => "link",
  target  => "/vagrant/manifests/assets/svn.conf",
  require => Package["apache2"],
  notify  => Service["apache2"],
  replace => yes,
  force   => true,
}

file { "/etc/apache2/conf-enabled/svn_repos.conf":
  ensure  => "link",
  target  => "/vagrant/manifests/assets/svn_repos.conf",
  require => Package["apache2"],
  notify  => Service["apache2"],
  replace => yes,
  force   => true,
}

exec { "Disable apache 000-default" :
  command => "/usr/sbin/a2dissite 000-default",
  require => Package["apache2"],
  notify  => Service["apache2"],
}

exec { "Reload apache" :
  command => "/usr/sbin/service apache2 reload",
  notify  => Service["apache2"],
  require => [Exec['Disable apache 000-default']],
  refreshonly => true,
}


# Setup xdebug
file { '/etc/php5/mods-available/xdebug.ini':
  ensure => file,
  source => '/vagrant/manifests/assets/xdebug.ini',
  require => Package["apache2"],
  notify  => Service["apache2"],
}

# Set Apache to run as the Vagrant user

exec { "ApacheUserChange" :
  command => "/bin/sed -i 's/APACHE_RUN_USER=www-data/APACHE_RUN_USER=vagrant/' /etc/apache2/envvars",
  onlyif  => "/bin/grep -c 'APACHE_RUN_USER=www-data' /etc/apache2/envvars",
  require => Package["apache2"],
  notify  => Service["apache2"],
}

exec { "ApacheGroupChange" :
  command => "/bin/sed -i 's/APACHE_RUN_GROUP=www-data/APACHE_RUN_GROUP=vagrant/' /etc/apache2/envvars",
  onlyif  => "/bin/grep -c 'APACHE_RUN_GROUP=www-data' /etc/apache2/envvars",
  require => Package["apache2"],
  notify  => Service["apache2"],
}

exec { "apache_lockfile_permissions" :
  command => "/bin/chown -R vagrant:www-data /var/lock/apache2",
  require => Package["apache2"],
  notify  => Service["apache2"],
}

# Setup the initial database

exec { "drop existing ubirimi database" :
  command => "/usr/bin/mysql -uroot -e \"drop database if exists ubirimi;\"",
  require => Service["mysql"],
}

exec { "create ubirimi database" :
  command => "/usr/bin/mysql -uroot -e \"create database if not exists ubirimi;\"",
  logoutput => on_failure,
  require => [Service["mysql"], Exec['drop existing ubirimi database']]
}

exec { "allow root to connect from anywhere" :
  command => "/usr/bin/mysql -uroot -e \"use mysql; update user set host='%' where user='root' and host='127.0.0.1'; flush privileges;\"",
  logoutput => on_failure,
  require => [Service["mysql"], Exec['create ubirimi database']]
}

exec { "import database structure" :
  command => "/usr/bin/mysql -uroot ubirimi < /vagrant/db/ubirimi.sql;",
  require => [Service["mysql"], Exec['create ubirimi database']]
}

exec { "allow external mysql connections":
  command => "/bin/sed -i \"s/bind-address.*/bind-address = 0.0.0.0/\" /etc/mysql/my.cnf",
  onlyif => '/bin/grep "bind-address.*\=.*127\.0\.0\.1" /etc/mysql/my.cnf',
  require => Package["mysql-server"],
  notify => Service["mysql"],
}

# composer install

exec { 'run install composer':
  path => "/usr/bin:/usr/sbin:/bin",
  environment => "HOME=/root",
  command => '/usr/local/bin/composer install --working-dir /var/www/products',
  timeout => 0,
  require => [Package["apache2"],Exec['install composer']]
}

# create assets folders

exec { 'create assets folder':
  command => '/bin/mkdir /var/www/assets; /bin/mkdir -p /var/www/assets/documentador/attachments; /bin/mkdir -p /var/www/assets/documentador/filelists; /bin/mkdir -p /var/www/assets/yongo/attachments; /bin/mkdir -p /var/www/assets/users;',
  require => [Exec['run install composer']]
}