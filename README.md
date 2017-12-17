# Api

Game api with calculation ELO raiting.

## Requirements

* PHP 5.5
* Vagrant VM [use this repository for get VM to this project](https://github.com/DimonchikSP/vagrant_vm)

## Install project

1. Clone repo to your PROJECT_ROTT_DIR

2. Get composer:

    ```
    curl -sS https://getcomposer.org/installer | php
    ```
3. Install composer:

    ```
    php composer.phar install
    ```
4. Install the dependencies:
    ```
    composer install
    ```
5. Create and setup DB connection configs:
    - setup config/local.php

    ```
    <?php
    return [
        'db' => [
            'dsn'            => 'mysql:dbname=DBNAME;host=127.0.0.1',
            'driver_options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
            ],
            'user' => 'DBUSER',
            'password' => 'PASSWORD',
        ]
    ];
    ```
6. Create DB from MySql cli:

    ```
    CREATE DATABASE dbname CHARACTER SET utf8 COLLATE utf8_general_ci;
    ```

7. Install dump to DB:

    ```
    mysql -u DBUSER -p DBNAME < PROJECT_ROOT/murka.sql
    ```
    Dump with data from table "players"
    
##  Use API

1. API support next HTTP methods:
    ```
    GET - for get data
    ```
    ```
    POST - for set data
    ```
    ```
    PUT - for update data
    ```
    ```
    DELETE - for delete data;
    ```
2. You can use api with 2 data format, such as XML and JSON.

    - for use JSON data format you can send request to:
    ```
    http://YOURDOMAIN/api.json
    ```
    - for use XML data format you can send request:
    ```
    http://YOURDOMAIN/api.xml
    ```

3. API params:
    #### Method GET
    - get list of matches:
    ```
    http://YOURDOMAIN/api.json
   
    ```
    - get list of matches played by a given player
    - use param "player_games" with player id
    ```
    http://YOURDOMAIN/api.json?player_games=1

    ```
    - get a list of matches that started in a certain time interval
    - use param "game_between_time" date range
    ```
    http://YOURDOMAIN/api.json?game_between_time[from]=2017-12-06 00:00:00&game_between_time[to]=2017-12-09 00:00:00
    ```
    - get the player's rating
    - use param "player_elo" with player
    ```
    http://YOURDOMAIN/api.json?player_elo=1
    
    ```
    - get information about one match
    - use param "id" with match id
   
    ```
    http://YOURDOMAIN/api.json?id=1
    
    ```
    #### Method POST
    - save game
    - use params:
        - "start_time" with date and time '2017-12-24 00:00:00'
        - "end_time" with date and time '2017-12-24 00:00:00'
        - "winner_id" with player id
        - "participants" with player id, api supports two or more match participants.
        - "log" with free text format
    ```
    http://YOURDOMAIN/api.json?start_time=2017-12-24 00:00:00&end_time=2017-12-25 00:00:00&winner_id=8&log=test_LOG&participants[]=7&participants[]=3
    
    ```
    #### Method PUT
    - update match data:
    - use params:
      - "start_time" with date and time '2017-12-24 00:00:00'
      - "end_time" with date and time '2017-12-24 00:00:00'
      - "winner_id" with player id
      - "log" with free text format
    ```
    http://YOURDOMAIN/api.json?id=16&start_time=2017-12-24 00:00:00&end_time=2017-12-25 00:00:00&winner_id=99
    ```
    #### Method DELETE
    - delete information about one match
    - use param "id" with match id   
    ```
    http://YOURDOMAIN/api.json?id=1
        
    ```
    
