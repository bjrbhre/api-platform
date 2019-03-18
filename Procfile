release: ./api/bin/console doctrine:migrations:migrate --no-interaction
web: pushd api && $(composer config bin-dir)/heroku-php-apache2 public/
