# Test exercise
Please do not store this test task into a public repository.
Feel free email us if you have any questions regarding this test task.

## Installation
Run `composer install` to install phpunit and generate `vendor/autoload.php`.

## Schema
Modify `schema.sql` and write SQL queries to create a db for storing a result.

## Importer
Implement `Importer`. Feel free to make any changes you need.

Your implementation need to use `$mapping` in order to find a proper position of a value.
It means you cannot hard code names/positions of the report columns.

Please note, that you should be able to import a huge report that won't fit into the memory completely.

It would be good to have it implemented in pure PHP (+PDO) and "green" tests (optional).

Things to note:
 - validation is not required, you can trust the provided report
 - Merchant has many Batches, Batch has many Transactions
 - Merchant unique key is `Report::MERCHANT_ID`
 - Batch unique key is a combination of `Report::MERCHANT_ID` and `Report::BATCH_DATE` and `Report::BATCH_REF_NUM`
 - Transaction does not have any unique key and having duplicates after several imports is OK
 - significant optimizations are welcome (consume less memory, make fewer db queries, less execution time)
 - do NOT implement missing framework features from scratch, focus on solving this test task
 
## Queries
Modify `queries.sql` and write SQL queries (how to get a proper result from your DB)
