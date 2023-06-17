# NinjaBring

Source code for project NinjaBring

## Starting Docker containers

Docker configuration is stored on docker directory. On the first startup you need to copy env-example to .env file on docker directory:

    cp docker/env-example docker/.env

To start the containers run the following commands:

    cd docker
    docker-compose up -d


To run `artisan` commands first you need to log into workspace container. Make sure you are still in the docker directory and then run the following command:

    docker-compose exec -u laradock workspace bash

Then you can start using the `artisan` commands
 
## Running the project for the first time

After docker is up and running and you are logged into the workspace container you need to execute the following commands:

    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    chmod 600 storage/oauth*
    npm i
    npm run dev

## Seeding geo data

    php artisan geo:download
    php artisan migrate
    php artisan geo:seed --chunk=100000

## Generating API keys

Generate one using tinker.
    
    php artisan tinker
    User::first()->createToken("Testing")->plainTextToken;

## Using API

Use [postman](https://www.getpostman.com/) or similar tools to send API requests. Use the API tokens as Bearer authentication tokens.

## Testing APIs

The phpunit.xml file contains environment variables that will define how the application runs when testing. To change these settings, an .env.testing file can be created with a new database.

To execute the test cases run the following command:

    ./vendor/bin/phpunit 
 
## Demo data

You can seed the demo data with this command:

    php artisan db:seed --class DemoSeeder
