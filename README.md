# mt940-to-csv
You have an MT940 file and want to analyze your spendings. This script helps you convert your MT940 file into a CSV file which you can then import e.g. to Firefly or any other application.
## Install the dependencies
```composer install```
## Run unit tests
```./vendor/bin/phpunit tests```
## Run the script
```php mt940-to-csv.php -i=<path to input> -o=<path to output>```