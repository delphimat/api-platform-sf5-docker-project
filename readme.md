# Levuro : Coding project  

## Installation

Please setup a Docker running env on your desktop / laptop

```
docker-compose build
docker-compose up -d
```

Next we will install the dependency

```
docker exec -it www_docker_symfony composer install
```

## JWT

Setup the keys for the JWT token

```
docker exec -it www_docker_symfony  bin/console  lexik:jwt:generate-keypair
```


## Database

```
docker exec -it www_docker_symfony  bin/console  doctrine:database:create
docker exec -it www_docker_symfony  bin/console  make:migration
docker exec -it www_docker_symfony  bin/console doctrine:migrations:migrate
```

## Clear cache

```
docker exec -it www_docker_symfony  bin/console  cache:clear
```

```
docker exec -it www_docker_symfony symfony 
```

## Run the test

Please run the test to check if everything is working normally

```
docker exec -it www_docker_symfony php bin/phpunit tests
```

Everything should be running alright then you can reach the documentation
```
http://localhost:8741/api
```

there is a phpAdmin setup no password, just login:root
```
http://localhost:8080/
```

--------

Create a new user

POST : http://localhost:8741/api/login
JSON :

```
{
"username" : "username_test-633f3345a479e",
"password" : "password"
}
```

Login

POST: http://localhost:8741/api/login

```
{
	"username" : "username_test-633f3345a479e",
	"password" : "password"
}
```

Add new Task for the authenticated user

POST: http://localhost:8741/api/tasks

```
{
  "name": "toto"
}
```

Updating an existing task

PATCH: http://localhost:8741/api/tasks/{id}

```
{
"name": "toto",
"status": "progress"
}
```

Get all tasks for authenticated user

GET: http://localhost:8741/api/tasks