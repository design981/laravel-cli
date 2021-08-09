# Laravel server for cli

## Why use here
Traditional php-fpm require vender file for every time, This not only consumes CPU and disk I/O,
Even if opcache is used, it loads a large number of files. It's also a waste of time and resources.
So write kernel to memory, Reduce file access, Only keep build requests. This can save a lot of resources


1. Reduce access to cpu
2. Reduce access to disk I/o
3. Reduce db socket build

### install
> composer require design/laravel-cli

## use workerman

### start
> php artisan cli:workerman start

### status
> php artisan cli:workerman stop


