# Emergya technical test.

This is the project for the techinical test.

It uses [Ddev](https://ddev.readthedocs.io/) with PHP 8.1, MariaDB 10.6, Drupal 9.4.X (minimum Drupal version supported 9.3.0)

There is one snapshot of the database with the Drupal installation with some products and the images zipped in the file
media.zip in the root of the project.

## Installation

Use [Ddev](https://ddev.readthedocs.io/) to install the project.

```bash
ddev start
ddev composer install
ddev snapshot restore
ddev drush uli
```

You can restore the images of the media.zip file in the root of the project in web/sites/default/files

## Execute the test.

The project has one test clase with 30 assertions and using SQLite like database.

To execute the test you have 2 options:

### Use the ddev command

```bash
ddev emergya-test
```

### In the container

```bash
ddev ssh
/var/www/html/vendor/bin/phpunit -c /var/www/html/phpunit.local.xml /var/www/html/web/modules/custom/emergya_test/test/src/Kernel/AccessCheckerTest.php
```
