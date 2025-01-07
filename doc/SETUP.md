# Development setup from source

> [!NOTE]
> This is the build setup for Window-only. For Linux, I will update this document later on for it

## 1. Windows

### Install prerequisites:
+ [Docker](https://docs.docker.com/get-started/get-docker/)
+ [Git](https://git-scm.com/downloads) (or [GitHub CLI](https://github.com/cli/cli#installation))
+ [PHP 8.0+](https://www.php.net/downloads.php)
+ [NGINX](https://nginx.org/en/docs/windows.html)
+ [MariaDB 11.6.2](https://mariadb.org/download/)

### Clone this project's repository
```
git clone https://github.com/FaceWithDark/VOS.git (Normal way)
git clone git@github.com:FaceWithDark/VOS.git (SSH way)
```

### Copy and move all configure files in correct place
```powershell
# Assuming you're at the project's root directory when executing these command lines.
# Remove '-Confirm' parameter if you just want it to run instantly without additional prompting.

# Environment file. '<>' placeholder received from any of the bash scripts in 'bin/' directory
Copy-Item -Path ".\doc\example.env" -Destination ".\docker-compose.<build-stage>.env" -Confirm -Force

# Docker-compose configuration file. '<>' placeholder received from any of the bash scripts in 'bin/' directory
Copy-Item -Path ".\doc\example.yaml" -Destination ".\docker-compose.<build-stage>.yaml" -Confirm -Force

# NGINX configuration file. '<>' placeholder received from any of the bash scripts in 'bin/' directory
Copy-Item -Path ".\doc\example.conf" -Destination ".\docker\nginx\default.<build-stage>.conf" -Confirm -Force

# Dockerfile configuration file. '<>' placeholder received from any of the bash scripts in 'bin/' directory
Copy-Item -Path ".\doc\example.Dockerfile" -Destination ".\docker\php\<build-stage>.Dockerfile" -Confirm -Force
```

### Run automate build script
```powershell
# You've to be within 'bin/' directory in order to let these command lines work. 

# Set the website up if not done yet. '<>' placeholder received from any of the bash scripts in 'bin/' directory.
sh .\deploy.sh <build-stage>

# Making changes not related to .php files. '<>' placeholder received from any of the bash scripts in 'bin/' directory.
sh .\rebuild.sh <build-stage>


# Fully shutdown the website if not intended to work on it anymore. '<>' placeholder received from any of the bash scripts in 'bin/' directory.
sh .\shutdown.sh <build-stage>

# Otherwise, go to the Docker Desktop and manually stop the service (in this case, the service name will be 'vos').
```

**At this point, you should be able to access the website via __```http://localhost:<port-number>/```__ if followed the step correctly.**

### Access the database
```powershell
# Replace all '$<name>' placeholder what the name defined with those variables within .env file.
mariadb -u $DATABASE_USER -P <port-number> -h localhost $DATABASE_NAME -p$DATABASE_PASSWORD
```

You can leave the '-p' parameter empty. In that case, you'll be prompted to enter the password for the corresponding database connection after execute the above command line.

### Example of succeed connection
```
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is <number>.
Server version: 11.6.2-MariaDB mariadb.org binary distribution.
Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
MariaDB [(none)]>  
```

Reference to [MariaDB documentation](https://mariadb.com/kb/en/sql-statements/) for further interaction with the database.
