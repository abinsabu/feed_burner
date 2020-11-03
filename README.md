Feed Burner Application
========================

The "Feed Burner" is a Symfony based application to read any kind of ATOM or RSS feed and import the data to database. User can add feed links and import the data individually.

Requirements
------------

  * PHP 7.1.3 or higher;
  * and the [usual Symfony application requirements][1].

Setting up an Existing Symfony Project
------------

In addition to creating new Symfony project, you will also work on projects already created by other developers. In that case, you only need to get the project code and install the dependencies with Composer. Setup your project with the following commands

```bash
$ cd /feed_burner/
$ composer update
```


Usage
-----


You’ll probably also need to customize your .env file and do a few other project-specific tasks (e.g. creating a database). When working on a existing Symfony application for the first time, it may be useful to run this command which displays information about the project:


```bash
$ php bin/console doctrine:database:create
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
$ symfony server:start
```
Tests
-----

Execute this command to run tests:

```bash
$ cd feed_burner/
$ bin/phpunit
```

[1]: https://symfony.com/doc/current/reference/requirements.html
