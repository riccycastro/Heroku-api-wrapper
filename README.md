# Heroku API wrapper

The purpose is to deliver the best version of an API(Heroku-app), 
respecting the restful API standards and providing a way to filter the data that 
are returned

#### The problem
We want to serve our clients with an admirable service: fetch, filter and properly return a
bunch of KPIs from the ugly upstream service.
The upstream spills out something that could be interpreted as an application/json
response and (thank god!) they shared a status code! Just don't assume it will always
return the same data. And because the KPIs can be updated somewhere in the future
without notice don't bother caching it..
We've noticed it doesn't always return a 200 status. In that case we should not let our
clients down! We must keep on trying before (eventually) tell our clients the data is
unavailable.
____

### Docker setup

First, make sure you have Docker and Docker-compose installed in your machine. 
Clone this repo and enter the project folder.

Copy the content from webserver/hosts and paste it in you local hosts file. 
_Note: Ubuntu location: /etc/hosts_

Now you can start your server

``` bash
    docker-compose up --build
```

This should be enough to have the app server up and running

_Be aware that at the first time you start the server, it will install the project 
dependencies. It takes some seconds to complete_

After that, using your preferred browser or httpClient, go to 
http://api.internations.local/data to make sure everything is running well

### Tests
To run tests enter the application container
``` bash
    docker exec -it internations-app bash
```
Run the phpunit command
``` bash
    ./bin/phpunit
```

This command will execute unit and integrations tests

_Notice that at the first time you execute this command, it will install all 
phpunit dependencies and then start the tests_
