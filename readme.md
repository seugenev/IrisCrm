[SENIOR PHP DEVELOPER TASK DESCRIPTION](./task.md)
# SOLUTION

## Installation
1. Run `./docker-compose up`
2. Run `./composer install`
3. Execute SQL script `schema.sql` to setup database structure 
4. Execute script `./process` to run task

## Description
To speed up processing and save resources were used:
1. generators
2. cashing - cache size could be changed in models
3. bulk query inserts 

