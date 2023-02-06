## Morsum Cart Assessment

This is de Cart Assessment by Samuel Drilard, using Laravel 9, running on sail, with php 8 and mysql 8.

## Installation

Use the [docker](https://docs.docker.com/get-docker/) to run this project, so make sure you have the docker installed.

After cloning this repo, run the following commands, in this order:
```bash
cd morsum-cart
```
```bash
mv .env.example .env
```
```bash
docker run --rm --interactive --tty -v $(pwd):/app composer install
```
## Create the sail alias
It's not mandatory, but if you prefer you can create an alias so it´s become simpler to interface with sail :

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

You may add this to your shell configuration file in your home directory, such as ~/.zshrc or ~/.bashrc, restart your shell and then use.

If you choose not to use the alias, you can interact with sail using the path:

```bash
./vendor/bin/sail up -d
```

## Configure you local database
```bash
sail up -d
```
```bash
sail artisan config:cache
```
```bash
sail artisan migrate
```

Due to an issue with laravel sail configuration, sometimes the database do not initialize correctly the first time you build de app, if you experience any issue with database connection when running the migrations, do the following:

```bash
sail down -v
```
```bash
sail artisan config:cache
```
```bash
sail artisan optimize
```
```bash
sail artisan migrate
```
As far as I could understand, it's caused by some laravel caches not responding well with the sail container.

With all running, any command php artisan can be used as sail artisan.

## Access the documentation
Access http://localhost/api/documentation to get all the api documentation.
