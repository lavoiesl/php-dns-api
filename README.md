# PHP DNS API

Basic API using PHP with React DNS

## Usage

Prerequisite: PHP and Composer: https://getcomposer.org/

```sh
# Install dependencies
composer install

# Start a server
php -S localhost:9999

# Query the API
curl http://localhost:9999/dns.php?nameserver=8.8.8.8&hostname=google.com
```

## Author

Sébastien Lavoie <github@lavoie.sl>
