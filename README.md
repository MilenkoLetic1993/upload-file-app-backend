# upload-file-app-backend

## Setup

1. install [VirtualBox](https://www.virtualbox.org/wiki/Downloads), follow installation instructions according to your operating system (version 6.1.16)
2. install [Vagrant](https://www.vagrantup.com/downloads.html), follow installation instructions according to your operating system (version 2.2.6)
3. install php8.0 (ubuntu - `sudo apt-get install php8.0`)
4. enable extensions dom and mbstring `sudo apt-get install php8.0-dom` and `sudo apt-get install php8.0-mbstring`
5. install [Composer](https://getcomposer.org/doc/00-intro.md), follow installation instructions according to your operating system
5. run `composer require laravel/homestead --dev`
6. add `192.168.10.10  upload-file-app-backend.local` to /etc/hosts file
7 position your self to project root
8. run `vagrant up`
9. run `vagrant ssh`
10. run `cd code`
11. create file .env and copy/paste all content from .env.example
12. run `composer install`
13. run `php artisan migrate`
14. run `php artisan key:generate`
15. run `phpunit` for generating tests
16. run `php artisan queue:work` command (Leave it running, don't interrupt the command!)
