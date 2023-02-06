## Morsum Cart Assessment

This is de Cart Assessment by Samuel Drilard, using Laravel 9, running on sail, with php 8 and mysql 8.

## Learning Laravel

To run this project is simple:

1 - Make sure you have the docker installed, if not, install here: https://docs.docker.com/get-docker/

2 - clone this repo

3 - run mv .env.example .env

4 - run docker run --rm --interactive --tty -v $(pwd):/app composer install

5 - run ./vendor/bin/sail up to initialize the local server

5.5 - It's not mandatory, but if you prefer you can create an alias so it´s become simpler to interface with sail using this:

alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

You may add this to your shell configuration file in your home directory, such as ~/.zshrc or ~/.bashrc, and then restart your shell.

6 - Access http://localhost/api/documentation to get all the api documentation.