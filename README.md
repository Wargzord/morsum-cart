## Morsum Cart Assessment

This is de Cart Assessment by Samuel Drilard, using Laravel 9, running on sail, with php 8 and mysql 8.

## Installation

Use the [docker](https://docs.docker.com/get-docker/) to run this project, so make sure you have the docker installed.

After cloning this repo, run the following commands, in this order:
```bash
cd morsum-cart 

mv .env.example .env

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

sail artisan config:cache

sail artisan migrate
```

## Access the documentation
Access http://localhost/api/documentation to get all the api documentation.
