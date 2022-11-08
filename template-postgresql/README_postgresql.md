# PostgreSQL with Docker

## Introduction

This README describes how to initiate the setup the development environment for LBAW. These instructions address the development with a local environment, i.e. on the machine (that can be a VM) without using a Docker container for PHP.
Containers are used for PostgreSQL and pgAdmin, though.

The template was prepared to run on Ubuntu 21.10, but it should be fairly easy to follow and adapt for other operating systems.

The following links provide instructions for installing [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/)

## Working with PostgreSQL

We have created a _docker-compose_ file that sets up __PostgreSQL__ and __pgAdmin4__ to run as local Docker containers.

From the project root issue the following command:

    docker-compose up

This will start the database and the pgAdmin4 tool images as two independent docker containers.

Navigate on your browser to http://localhost:4321 to access __pgAdmin4__ and manage your database. Depending on your installation setup, you might need to use the IP address from the virtual machine providing docker instead of `localhost`. Please refer to your installation documentation.
Use the following credentials to login:

    Email: postgres@lbaw.com
    Password: pg!password

In the first usage of the development database, you will need to add a new Server using the following attributes<sup>1</sup>:

    hostname: postgres
    username: postgres
    password: pg!password

<sup>1</sup>Hostname is _postgres_ instead of _localhost_ since _Docker composer_ creates an internal DNS entry to facilitate the connection between linked containers.
