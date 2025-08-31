# Basic (Email) Notifications API 

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
[//]: # (- [Symfony Messenger]&#40;https://symfony.com/doc/current/messenger.html&#41; for async processing)
[//]: # (- [Symfony Notifier]&#40;https://symfony.com/doc/current/notifier.html&#41; for sending)
- [Docker](https://www.docker.com/) to run a database in a container
- [PHPUnit](https://phpunit.de/) for testing
- [Makefile](https://www.gnu.org/software/make/) to automate common tasks


# Architectural Decision Records


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

Once you've got the server running, you can check out the API documentation at:

```
https://127.0.0.1:8000/api
```

You can also access the OpenAPI documentation directly at:

```
//https://127.0.0.1:8000/api/docs.jsonopenapi
```
