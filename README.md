# basic-commenting-system
A system where users can post and comment on posts

## Description

Application load all stored posts and comments in database

Through Ajax, posts and comments can be added and displayed once saved without reload

Using linkify library, links in posts and/or comments are turned into links

Using a custom solution for spam protection which relies on honeypot concept,

For example simple addition operation and a checkbox that is not supposed to be checked would do the trick

## Installation

#### Create a virtual host
For example a host for Apache2, would be as follows :

````
<VirtualHost *:80>

        ServerName local-commenting-system.com

        DocumentRoot /var/www/github/basic-commenting-system
        <Directory "/var/www/github/basic-commenting-system">
                AllowOverride All
                Order Allow,Deny
                Allow from All
        </Directory>
</VirtualHost>
````
##### Create a database for the application, and update `config.php` with database credentials

##### Run deploy script `deploy.sh` to download all needed files and populate database

And voila!, Application should now be accessible via index.php available inside web directory


