## About

**Da spaektionary** is a an open-source online dictionary that aims to capture and preserve the Shetland language/dialect. The project is a combination of collected works from [I Hear Dee](https://www.iheardee.com), a Shetland-based language advocacy group.

The project is written in Laravel 8+ and Vue 3, using Inertia for routing and SQL for the database. It is presently hosted on AWS at https://www.spaek.org

To get it up and running on your machine, clone the repo and follow the usual steps:

1. run `composer update` to get the PHP dependencies
2. run `npm i` to install the front end dependencies
3. create `.env`  and set up your database.
4. run `php artisan migrate` to run all migrations
5. run `php artisan serve` and `npm run hot` in separate terminals and you should be good to go.