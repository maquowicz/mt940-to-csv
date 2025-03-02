> Thanks terencejackson8000 for the fork.

Added:
  
- Adjustments for PKO Bank Polski MT940 reports
- ISO-8559-2 mt940 file encoding
- Works with Odoo OCA/bank-statement-import CSV's

Enjoy!

## mt940-to-csv
You have an MT940 file and want to analyze your spendings. This script helps you convert your MT940 file into a CSV file which you can then import e.g. to Firefly or any other application.
## Install the dependencies
```composer install```
## Run xdebug session
```export XDEBUG_MODE=debug XDEBUG_SESSION=1```
## Run unit tests
```XDEBUG_MODE=coverage ./vendor/bin/phpunit --log-junit reports/junit.xml --coverage-clover reports/clover.xml tests```
## Run the script
```php mt940-to-csv.php -i=<path to input> -o=<path to output>```
