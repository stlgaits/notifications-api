# Basic (Email) Notifications API 

![API Swagger Homepage](docs/swagger.png)

# Code quality tools

This project uses the following code quality tools:
- [PHPStan](https://phpstan.org/) for static analysis
- [ECS Fixer](https://cs.symfony.com/) for coding standards
- [Rector]

# Architecture

This project leverages the following libraries and architecture:
- [Symfony 7.3](https://symfony.com/) 
- [API Platform 3](https://api-platform.com/) to build the API
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html) for database
- [Docker](https://www.docker.com/) to run a database in a container
- [PHPUnit](https://phpunit.de/) for testing
- [Makefile](https://www.gnu.org/software/make/) to automate common tasks


## Project Structure

As this is a minimal test project, I've decided to follow the default Symfony standard project structure. In a larger project, I would consider
resorting to a Domain-Driven Design (DDD) approach, organizing the code into distinct domains or bounded contexts coupled with CQRS pattern.

## API Platform

I've decided to resort to API Platform as I believe API Platform provides a robust set of tools and features that simplify the process 
of building and maintaining APIs, allowing developers to focus on business logic rather than boilerplate code. Most importantly,
its solid set of standards and best practices help ensure that the API is well-designed, secure, and scalable, particularly when 
it comes to handling serialization, validation, pagination. Its built-in Swagger & Open API documentation also makes the API super easy
to test out.

# Using and testing out the API

## Running the server

If you've got the Symfony CLI & PHP on your local machine can setup the database and run the project with:

```
make basic-install
```

Once you've got the server running, you can check out the API documentation at:

```
https://127.0.0.1:8000/api
```

You can also access the OpenAPI documentation directly at:

```
https://127.0.0.1:8000/api/docs.jsonopenapi
```

## Fake notifications system

For the sake of simplicity, this project does not include a real notification system. Instead, it simulates sending notifications by logging messages to a file.
You can find the log file at `var/log/dev_notifications.log` or `var/log/test_notifications.log`. Whenever a notification is "sent", a message will be appended to this file.
It uses a dedicated monolog channel called `notifications` to separate these logs from other application logs.

![Notifications log](docs/logs.png)

# Test suite

You can run the test suite with:

```
make test
```

Tests rely on data fixtures, stories and factories thanks to Foundry, as well as justinrainbow/json-schema and DAMADoctrineTestBundle to reset the database.

