[![Open Source Love](https://badges.frapsoft.com/os/v1/open-source.svg?v=103)](https://github.com/ellerbrock/open-source-badges/)


# Build a RESTful API in Slim 3
A RESTful API based in PHPSlim 3. To follow along, this application has been documented as a two-part article on Pusher blog. You can read about it part 1 [here](https://pusher.com/tutorials/rest-api-slim-part-1) and part two [here](https://pusher.com/tutorials/rest-api-slim-part-2)

## Note

If you are following along using the tutioral, please checkout to the `master` branch. The default branch `refined` was created as an update to the current codebase. It factors in a lot of code refinement/improvements

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

## Prerequisites
What things you need to install the software.

- Git.
- PHP.
- Composer.
- Laravel CLI.


## Install
Clone the git repository on your computer
```
$ git clone https://github.com/fisayoafolayan/build-a-restful-api-in-slim3.git
```
You can also download the entire repository as a zip file and unpack in on your computer if you do not have git

After cloning the application, you need to install it's dependencies.
```
$ cd build-a-voucher-pool-api-in-slim3
$ composer install
```
## Setup
When you are done with installation, copy the .env.example file to .env
```
$ cp .env.example .env
```
Migrate the application
```
$ vendor/bin/phinx migrate
``` 
Run the application
```
$ php -S localhost:9000 -t public
```

Click [here](https://www.figma.com/file/MRNeeuJIH6Gsgkw8mGp4YVtY/Database-Schema?node-id=0%3A1) to view the Database Schema
