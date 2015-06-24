#vettu-hva backend developer documentation

##Install guide

###Setting up a persistent Docker environment: 

See [Docker docs] (http://docs.docker.com/installation/) for installing Docker on various platforms. Download the Dockerfile from this repository. The Dockerfile needs to be in the same folder as the config file (see sample config.php and description below) to build the image. Run this command to build the image: 

```docker build -t imageName folder_with_config_and_dockerfile```

Create a volume for Docker to store databases in (only needs to be done the first time): 

```docker create -v /dbdata --name vettuhva-DBdata imageName```

Start the Docker image with importing from the database storage:

```docker run -d --volumes-from vettuhva-DBdata --name vettuhva-backend -p external_port:80 -p external_port:3306 -e MYSQL_PASS="chosen password" imageName```

Read as: docker run -demonize --import from database volume --name of container -port exposed extrernal:internal -port exposed external:internal -environment variable variable="" imageName

To stop the running container:

```docker stop containerName```

To remove old containers:

```docker rm containerName```


###Config file parameters
DB_USERNAME - Can be left as root since this is only used internally in the image

DB_PASSWORD - Can be left blank since this is only used internally in the image

DB_HOST -  Can be left blank which is the same as typing localhost

DB_NAME - storytelling is the name of the database

API_URL - This is the URL to Digitalt museums API

API_KEY - The API key needed to use Digitalt museums API

APP_MAIL - The email used for sending users emails upon creation of user (uses a gmail address)

APP_MAIL_PASS - The password for the email given

TOKEN - Token to ensure that only post request that comes from the application are treated by controller.php

## Developer guide

### Dependencies

The most critical dependencies for the Docker image is the Linux, Apache, MySQL and PHP (LAMP) stack and Java.

Choose between installing and configuring PHP or downloading a development environment with PHP and MySQL.

Install and configure PHP: http://php.net/manual/en/install.php

Download MySQL server: http://www.mysql.com/downloads/

Development environment alternatives that include MySQL and PHP:
* XAMPP. See https://www.apachefriends.org/index.html
* WAMP. See http://www.wampserver.com/en/

Java download: http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html
Note: the Dockerfile uses Java 7 as the recommender.jar files is compiled with Java 7. This might be changed.





