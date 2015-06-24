#vettu-hva backend developer documentation

This repository contains the code for the backend part of a student project that developed the application "Vettu hva?" during the spring of 2015. "Vettu hva?" is a prototype of a mobile application that gives users recommendations on cultural stories based on their interest. The application was developed in context of the EU/FP7 IST research project [TAG CLOUD] (http://www.tagcloudproject.eu/). The goal of this project is the increase the interest in cultural heritage through a personalized cultural experience.

The frontend part for "Vettu hva?" can be found at: https://github.com/RoarG/VettuHva-FrontEnd

##Install guide

The backend uses Docker to ease the deployment of the application. This is done by providing a virtual operating-system-level abstraction. See more on https://www.docker.com/

###Setting up a persistent Docker environment: 

See [Docker docs] (http://docs.docker.com/installation/) for installing Docker on various platforms. Download the Dockerfile from this repository. The Dockerfile needs to be in the same folder as the config file (see sample config.php and description below) to build the Docker image. Run this command to build the image: 

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

Development environment alternatives that include MySQL and PHP, for Linux and other platforms:
* XAMPP. See https://www.apachefriends.org/index.html
* WAMP. See http://www.wampserver.com/en/

Java download: http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html
Note: the Dockerfile uses Java 7 as the recommender.jar files is compiled with Java 7. This might be changed.



##Repository description 

###A overview of each of the folders:

####PHPExcel_1.8.0_doc
Library for creating Excel-files in PHP. See: https://github.com/PHPOffice/PHPExcel
####admin
Consists of files for creating a web interface for admin users. The admin tasks is to get research data from the application and to set which areas to harvest stories from Digitalt fortalt.
####database
This section contains the classes dbStory, dbUser, dbHelper and harvesting. The db classes are responsible for accessing the database. dbStory contains methods for adding or retrieving story related information from the database and dbUser is responsible for user related information. dbHelper consists of more general methods and is the class which establishes a connection with the database. The harvesting script is responsible for collecting all database stories from Digitalt museums API and adding stories to or updating stories in the database. In addition, there is a SQL script for creating all the necessary tables in the database.
####docker
Consists of files used by the Dockerfile to configure the image.
####java
This folder consists of the Java code and recommender.jar file which make up the Mahout recommender, plus some tests.
####models
Consisting of the classes storyModel, userModel and preferenceValue. The models are used to temporarily hold information about a story, a user and a user's preference value for a story to be utilized by other files. Information is either retrieved from the database, sent from front-end, harvested from Digitalt museum's API or a combination of these. These models also contain formatting methods, which makes it possible to return story or user information to front-end.
####personalization
Consisting of the classes computePreferences and runRecommender. This section computes preference values for each Digitalt fortalt story in the database for each user. runRecommender is also responsible for initializing the Mahout recommendation engine when a user's preference values have been calculated. The similarities.csv file in this folder is produced by the harvesting.php file and used by the recommender.jar file when computing content-based recommendations.
####requests
Contains the controller script, which receives and handles front-end HTTP requests and returns JSON responses.
####test
Contains test-classes for most of the code.

###An overview of the class structure:

![](/overall_backend.png)





