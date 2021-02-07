# upload-file-app-backend

## Setup

1. install [VirtualBox](https://www.virtualbox.org/wiki/Downloads), follow installation instructions according to your operating system (version 6.1.16)
2. install [Vagrant](https://www.vagrantup.com/downloads.html), follow installation instructions according to your operating system (version 2.2.6)
3. add `192.168.10.10  upload-file-app-backend.local` to /etc/hosts file
4. position your self to project root
5. run `vagrant up`
6. run `vagrant ssh`
7. run `cd code`
8. create file .env and copy/paste all content from .env.example
9. run `composer install`
10. run `php artisan migrate`
11. run `php artisan key:generate`
12. run `vendor/bin/phpunit` for generating tests
13. run `php artisan queue:work` command (Leave it running, don't interrupt the command!)
