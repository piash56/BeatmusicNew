@echo off
REM Run Laravel with increased upload limits so release uploads (cover + track) work.
REM If you get 413 Content Too Large, use this script instead of "php artisan serve".
php -d post_max_size=200M -d upload_max_filesize=200M artisan serve
