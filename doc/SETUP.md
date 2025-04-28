# Development setup from source

> [!NOTE]
> This is the build setup for Window-only. For Linux, I will update this document later on for it

## 1. Windows

### Install prerequisites:
+ [Docker & Docker Desktop](https://docs.docker.com/desktop/setup/install/windows-install/)
+ [Git](https://git-scm.com/downloads) (or [GitHub CLI](https://github.com/cli/cli#installation))
+ [PHP 8.0+](https://www.php.net/downloads.php)
+ [NGINX](https://nginx.org/en/docs/windows.html)
+ [MariaDB](https://mariadb.org/download/)

### Clone this project's repository
```bash
git clone https://github.com/FaceWithDark/VOS.git # Normal way
git clone git@github.com:FaceWithDark/VOS.git     # SSH way
```

### Copy and move all configure files in correct place
```powershell
# Please execute these scripts from the project's root directory.
# For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'

Copy-Item -Path ".\doc\example.env" -Destination ".\docker-compose.<build-stage>.env" -Confirm -Force         # Environment file.
Copy-Item -Path ".\doc\example.yaml" -Destination ".\docker-compose.<build-stage>.yaml" -Confirm -Force       # Docker-compose configuration file.
Copy-Item -Path ".\doc\example.conf" -Destination ".\docker\nginx\default.<build-stage>.conf" -Confirm -Force # NGINX configuration file.
```

> [!NOTE]
> For **Dockerfile configuration file**, I have created 3 different Dockerfile profiles for each container, which help debugging process much more easier by letting me fixing the issues within each containers sepearately without having interfere without others. Therefore, you will have to copy the example file 3 times in for 3 different build file and delete one (or two) blocks of code over another (I have explain clearly within the code file).

```powershell
# Dockerfile configuration file.
Copy-Item -Path ".\doc\example.Dockerfile" -Destination ".\docker\php\php.<build-stage>.Dockerfile" -Confirm -Force
Copy-Item -Path ".\doc\example.Dockerfile" -Destination ".\docker\php\nginx.<build-stage>.Dockerfile" -Confirm -Force
Copy-Item -Path ".\doc\example.Dockerfile" -Destination ".\docker\php\mariadb.<build-stage>.Dockerfile" -Confirm -Force
```

> [!TIP]
> Remove _**-Confirm**_ parameter if you just want it to run instantly without additional prompting.

### Run automate build script
```powershell
# Ensure that you have Git installed and added to PATH (there will be a checkbox for you to click on when installing Git for the 1st time). Then, please execute these scripts from the project's root directory.
# For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'

sh .\bin\deploy.sh <build-stage>   # Set the website's docker configuration settings for the first time (or fully re-build).
sh .\bin\rebuild.sh <build-stage>  # Reset the website's docker configuration settings without fully killing it and run again.
sh .\bin\shutdown.sh <build-stage> # Unset the website's docker configuration settings if not intended to work on it anymore.
```

> [!CAUTION]
> By executing the last shell scripts, all backed-up as well as up-to-date database data will be fully wiped out as well. Therefore, be sure to back it up somewhere else on your computer (or USB) before performing the action.

> [!NOTE]
> If you want a more GUI-based approaches, please open up Docker Desktop and manually stop/kill the service and related configuration settings **(in this case, the service name will be 'vos')**.

At this point, you should be able to access the website via _**`http://localhost:<port-number>/`**_. Again, we have 3 options coressponding to 3 development stages (`dev`, `stage`, and `prod`) for the _`<>`_ placeholder. However, you will notice that as soon as we get to the next page (`Home` page), we received a **SQL-related error**. Please follow along the next step to be able to resolve it smoothly.

### Access the database with root user
> [!NOTE]
> This step is needed for the next step. Please don't skip this part.

```bash
# Hit <Enter> again when asked for password
mariadb -u root -P <port-number> -h localhost -p
```

### Example of succeed connection (root user)
```sql
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is <number>.
Server version: 11.6.2-MariaDB mariadb.org binary distribution.
Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
MariaDB [(none)]>
```

### Create new non-root user
> [!WARNING]
> It is not recommeded to use **root user account** as the primary database login access _(somehow most large-scale companies still doing so)_.Therfore, we will have to create a new user account that share almost the same privileges level as **root user account**.

```sql
MariaDB [(none)]># can be either 'localhost' (local access level), or '%' (global access level);
Query OK, 0 rows affected (0.005 sec)

MariaDB [(none)]>CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
Query OK, 0 rows affected (0.005 sec)

MariaDB [(none)]>SELECT User, Host FROM mysql.user;
+------+-----------+
| User |    Host   |
+------+-----------+
| Name | %         |
| Name | localhost |
+------+-----------+
2 rows in set (0.003 sec)

MariaDB [(none)]>GRANT ALL PRIVILEGES ON *.* TO 'username'@'localhost' IDENTIFIED BY 'password';
Query OK, 0 rows affected (0.005 sec)

MariaDB [(none)]>FLUSH PRIVILEGES;
Query OK, 0 rows affected (0.003 sec)

MariaDB [(none)]>SHOW GRANTS FOR 'username'@localhost;
+----------------------------------------------------------------------------------------+
| Grant for username@localhost                                                           |
+----------------------------------------------------------------------------------------+
| GRANT USAGE ON *.* TO `username`@`localhost` IDENTIFIED BY PASSWORD '<hased-password>' |
+----------------------------------------------------------------------------------------+
1 row in set (0.003 sec)
```

###
### Access the database again with new user
```bash
# For all bash variables, these can be found under the copied environment file (a.k.a 'docker-compose.${ENV}.env')
# For all '<>' placeholder, it can be found under the copied docker-compose configuration file (a.k.a 'docker-compose.${ENV}.yaml')

mariadb -u $DATABASE_USER -P <port-number> -h localhost $DATABASE_NAME -p
```

> [!TIP]
> It is generally recommended to leave the _**-p (look closely, small 'p' letter)**_ parameter empty so that you will not potentially expose the website's database password for someone else to see it.

### Example of succeed connection (new user)
```sql
Welcome to the MariaDB monitor.  Commands end with ; or \g.
Your MariaDB connection id is <number>.
Server version: 11.6.2-MariaDB mariadb.org binary distribution.
Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
MariaDB [(${DATABASE_NAME})]>
```

Reference to [MariaDB documentation](https://mariadb.com/kb/en/sql-statements/) for further interaction with the database.
