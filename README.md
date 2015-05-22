[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4e3d9b1a-d023-4894-a7bf-48cde810b132/mini.png)](https://insight.sensiolabs.com/projects/4e3d9b1a-d023-4894-a7bf-48cde810b132)
[![Build Status](https://travis-ci.org/ubirimi/ubirimi.png)](https://travis-ci.org/ubirimi/ubirimi) [![Code Climate](https://codeclimate.com/github/ubirimi/ubirimi/badges/gpa.svg)](https://codeclimate.com/github/ubirimi/ubirimi)[![Gittip](http://img.shields.io/gratipay/w2pc.svg)](https://www.gittip.com/w2pc/)

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/ubirimi/ubirimi?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=body_badge)

What is Ubirimi?
-----------------

Ubirimi is a productivity platform that offers open source tools. It is designed to suite a multitude of scenarios: from personal use to large enterprise deployments. It is written with speed and flexibility in mind. A fork can be found at https://www.ubirimi.com.

Requirements
------------
- Apache or nginx
- Ubirimi is only supported on PHP 5.5.0 and up.
- Be warned that PHP versions before 5.3.8 are known to be buggy and might not work for you
- MySQL 5.0 or above
- if you go with Apache you must install mod_rewrite module
- needed PHP extensions: mysqli, mysqlnd, gd, mbstring

Products available
------------
1. Yongo - Track and manage the issues, bugs, tasks, deadlines, code, hours.
2. Agile - The power of Agile: planning, estimating and visualizing team activity.
3. Helpdesk - Powerful solution for any organization. Keep in touch with your customers.
4. Documentador - Content Creation, Collaboration & Knowledge Sharing software for teams.
5. SVN Hosting - Reliable, private hosting for your projects with unlimited users.
6. Events - Plan and keep track of people, projects and events. A complete calendar application.
7. QuickNotes - A note application

Installation on Apache Server
-----------------------------

- download the source code
- `composer install`
- `composer setup`
- import an empty database structure
- set your Apache virtual host configuration. An example can be found below:

```
<VirtualHost *:80>
  ServerName ubirimi_net.lan
  DocumentRoot "c:/www/ubirimi-web/web"
  ServerAlias demo.ubirimi_net.lan
  DirectoryIndex index.php

  <Directory "c:/www/ubirimi-web/web">
      AllowOverride All
      Allow from ubirimi_net.lan
  </Directory>

  Alias /assets c:/www/ubirimi-web/assets
  <Directory "c:/www/ubirimi-web/assets">
	AllowOverride All
	Allow from All
  </Directory>
</VirtualHost>
```
- in the config.properties file set: database credentials, cache and assets folders
- restart web server

Installation on nginx
-----------------------------
Coming Soon!  

Upgrading
------------
- clear the cache
- `composer update`
- `composer dump-autoload`

Documentation
-------------

Common Errors:  
Error: PHP Fatal error:  Call to undefined method mysqli_stmt::get_result()  
Cause: mysqlnd not installed  

Contribution
------------

If you have a patch, or stumbled upon an issue with Ubirimi core, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/ubirimi/ubirimi/blob/master/CONTRIBUTING.md) for more information how you can do this.  

Get started using our pre-made development server
-------------------------------------------------

Ubirimi is an open source, community-driven project. If you would like to contribute just send pull requests. To get started clone the repository. 

Inside then newly created project, from the terminal run the following commands:

`vagrant plugin install vagrant-hostmanager`

`vagrant up`

`vagrant ssh`

You can log in into the dev vm by executing "vagrant ssh ubirimi". Once inside do: 

`cd /var/www`

`composer setup`

In the setup procedure when asked for the URL enter http://ubirimi.dev
Sometimes it is needed to restart the apache web server. Do this by executing: 
`/etc/init.d/apache2 restart`

You will be all set with a working project.

To test the working ubirimi environment enter in a browser the url: http://ubirimi.dev
You should get a login screen into the platform. You can use the credentials provided during the setup procedure.

Requirements on Host Machine:
- Virtual Box - https://www.virtualbox.org/
- Vagrant - https://www.vagrantup.com/
- Vagrant hostmanager plugin - https://github.com/mitchellh/vagrant/wiki/Available-Vagrant-Plugins

Connecting to the Ubirimi Database on Virtualbox.  
SSH Host: 127.0.0.1  
SSH Port: 2222  
SSH User: vagrant  
SSH Password: vagrant  

MySQL Host: 127.0.0.1  
DB User: root  
DB Password: (leave blank)  
Port: 3306  


Copyright and License
---------------------

Ubirimi is distributed under the terms of the GNU General Public License version 2.0
