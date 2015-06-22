# storytelling

#Setting up a persistent docker environment: 

Download the dockerfile, create a image from the docker file, this needs to be with the config file in the same folder as the docker file: 

```docker build -t imageName folder_with_config_and_dockerfile```

Create a volume for docker to store databases in (only needs to be done the first time): 

```docker create -v /dbdata --name Storytelling-DBdata imageName```

Start the docker image with importing from the database storage:

```docker run -d --volumes-from vettuhva-DBdata --name vettuhva-backend -p external_port:80 -p external_port:3306 -e MYSQL_PASS="chosen password" imageName```

Read as: docker run -demonize --import from database volume --name of container -port exposed extrernal:internal -port exposed external:internal -environment variable variable="" imageName

To stop the running container:

```docker stop containerName```

To remove old containers:

```docker rm containerName```


# Config file parameters
DB_USERNAME - Can be left as root since this is only used internally in the image

DB_PASSWORD - Can be left blank since this is only used internally in the image

DB_HOST -  Can be left blank which is the same as typing localhost

DB_NAME - storytelling is the name of the database

API_URL - This is the URL to Digitalt museums API

API_KEY - The API key needed to use Digitalt museums API

APP_MAIL - The email used for sending users emails upon creation of user (uses a gmail address)

APP_MAIL_PASS - The password for the email given

TOKEN - Token to ensure that post request to controller.php comes from the application
