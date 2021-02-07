# upload-file-app-backend

## Setup

1. install [VirtualBox](https://www.virtualbox.org/wiki/Downloads), follow installation instructions according to your operating system (version 6.1.16)
2. install [Vagrant](https://www.vagrantup.com/downloads.html), follow installation instructions according to your operating system (version 2.2.6)
3. install [PHP8.0](https://www.php.net/downloads.php) follow installation instructions according to your operating system
4. enable extensions dom and mbstring
5. install [Composer](https://getcomposer.org/doc/00-intro.md), follow installation instructions according to your operating system
6. run `composer require laravel/homestead --dev`
7. add `192.168.10.10  upload-file-app-backend.local` to /etc/hosts file
8 position your self to project root
9. run `vagrant up`
10. run `vagrant ssh`
11. run `cd code`
12. create file .env and copy/paste all content from .env.example
13. run `composer install`
14. run `php artisan migrate`
15. run `php artisan key:generate`
16. run `phpunit` for generating tests
17. run `php artisan queue:work` command (Leave it running, don't interrupt the command!)
