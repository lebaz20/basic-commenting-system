#!/bin/bash

# load client-side libraries
cd web/
bower install
cd ../

# load database -if not still loaded using DB credentials in config.php-
php Command/Database.php --action "create"