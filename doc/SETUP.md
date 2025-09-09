# A. Instructions on how to build the website from source code

## 1. Windows
> [!NOTE]
> I really do not like working on **Windows** at all, let aside writing documentation for it. However, it is as it is no matter how I detest it. Oh well.

<details hidden>
<summary><strong>1.1. Windows 10 & 11</strong></summary>
  
Do not ask me why I have to specifically mention that this is for **Windows 10 & 11** only.

  <details hidden>
  <summary><strong>1.1.1. Install prerequisites</strong></summary>

  + [Docker & Docker Desktop](https://docs.docker.com/desktop/setup/install/windows-install/)
  + [Git](https://git-scm.com/downloads)
  + [GitHub Desktop](https://github.com/apps/desktop) (Optional)
  + [MariaDB](https://mariadb.org/download/)

  </details>
  
  <details hidden>
  <summary><strong>1.1.2. Clone this project's repository</strong></summary>
  
  ```bash
  git clone https://github.com/FaceWithDark/VOS.git # Normal way
  git clone git@github.com:FaceWithDark/VOS.git     # SSH way
  ```

  </details>

  <details hidden>
  <summary><strong>1.1.3. Copy and move all configure files in correct place</strong></summary>

  ```powershell
  # Please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'

  # Please take a read at the comments within the example environment file to understand why you have to copy them twice.  
  Copy-Item -Path ".\doc\example.env" -Destination ".\docker.<build-stage>.env" -Confirm -Force
  Copy-Item -Path ".\doc\example.env" -Destination ".\src\private\Configurations\vot.<build-stage>.env" -Confirm -Force

  Copy-Item -Path ".\doc\example.yaml" -Destination ".\docker-compose.<build-stage>.yaml" -Confirm -Force       # Docker-compose configuration file.
  Copy-Item -Path ".\doc\example.conf" -Destination ".\docker\nginx\default.<build-stage>.conf" -Confirm -Force # NGINX configuration file.
  ```
  
  For **Dockerfile configuration file**, I have created 3 different Dockerfile profiles for each container, which help debugging process much more easier by letting me fixing the issues within each containers sepearately without having interfere without others. Therefore, you will have to copy the example file 3 times in for 3 different build file and delete one (or two) blocks of code over another (I have explain clearly within the code file).
  
  ```powershell
  # Dockerfile configuration file.
  Copy-Item -Path '.\doc\example.Dockerfile' -Destination '.\docker\php\php.<build-stage>.Dockerfile' -Confirm -Force
  Copy-Item -Path '.\doc\example.Dockerfile' -Destination '.\docker\nginx\nginx.<build-stage>.Dockerfile' -Confirm -Force
  Copy-Item -Path '.\doc\example.Dockerfile' -Destination '.\docker\mariadb\mariadb.<build-stage>.Dockerfile' -Confirm -Force
  ```
  
  _Remove **-Confirm** parameter if you just want to run it without additional manual typing/checking process._
  
  </details>

  <details hidden>
  <summary><strong>1.1.4. Run automate build script</strong></summary>

  **By executing the last shell script, all backed-up as well as up-to-date database data will be fully wiped out. Therefore, be sure to back it up somewhere else on your computer (or USB) before action is done.**

  ```powershell
  # Ensure that you have Git installed and added to PATH (there will be a checkbox for you to click on when installing Git for the 1st time). Then, please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'
  
  sh .\bin\deploy.sh <build-stage>   # Set the website's docker configuration settings for the first time (or fully re-build).
  sh .\bin\rebuild.sh <build-stage>  # Reset the website's docker configuration settings without fully killing it and run again.
  sh .\bin\shutdown.sh <build-stage> # Unset the website's docker configuration settings if not intended to work on it anymore.
  ```
  
  _If you want a more GUI-based approaches, please open up **Docker Desktop** and manually stop/kill the service and related configuration settings **(in this case, the service name will be 'vos')**._
  
  </details>

  <details hidden>
  <summary><strong>1.1.5. Test website URL accessible link</strong></summary>
  
  At this point, you should be able to access the website via _**`http://localhost:<port-number>/`**_. You can find the port number by typing `docker ps` to your command line. However, you will notice that as soon as we open up the website, we received a **SQL-related error**. Please follow along the next step to be able to resolve it smoothly.
  
  </details>

  <details hidden>
  <summary><strong>1.1.6. Access the database with root user</strong></summary>
  
  **Please do not skip this step as it is crucially needed for any further steps.**
  
  ```powershell
  # If you set the root password to something else than default (which is no password) then use that when getting asked upon executing the command
  sh .\bin\access-database.sh
  ```
  
  An example of a succeeded MariaDB connection as a root user would look like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(none)]>
  ```

  </details>

  <details hidden>
  <summary><strong>1.1.7. Create new non-root user</strong></summary>
  
  **It is not recommeded to use** `root user account` **as the primary database login access** _(somehow most large-scale companies still doing so)_. **Therfore, we will have to create a new user account that share almost the same privileges level as** `root user account` **.**
  
  ```sql
  MariaDB [(none)]>CREATE DATABASE 'database-name';
  Query OK, 0 rows affected (0.005 sec)

  MariaDB [(none)]># Can be either 'localhost' (local access level), or '%' (global access level);
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

  </details>

  <details hidden>
  <summary><strong>1.1.8. Access the database again with new user</strong></summary>
  
  ```powershell
  sh ./bin/access-database.sh
  ```

  If you choose to not to directly use the database that you just created, then a succeeded MariaDB connection as a non-root user would look exactly like 2.1.6. Otherwise, it would be like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(${DATABASE_NAME})]>
  ```
  
  Refers to [MariaDB documentation](https://mariadb.com/kb/en/sql-statements/) for further interaction with the database.
  
  </details>
  
</details>

## 2. Linux
> [!NOTE]
> I do not have that much leisure time to test on other distros. Therefore, I will be assuming that you are using Arch Linux when building this website, which I am highly recommeded for any other distros to hop over and try as well because _**`I use Arch btw!`**_

<details hidden>
<summary><strong>2.1. Arch Linux</strong></summary>

**[Arch Wiki](https://wiki.archlinux.org/title/Main_page)** is one of the best learning resources out there for not only **Arch Linux users** but for other distros as well. Therefore, I highly suggested you to visit the page for any inquiries about configuration issues, packages issues, etc. All of prerequisites below are links that direct to this **gigachad wiki**. 
  
  <details hidden>
  <summary><strong>2.1.1. Install prerequisites</strong></summary>
    
  + [Docker](https://wiki.archlinux.org/title/Docker)
  + [Git](https://wiki.archlinux.org/title/Git)
  + [MariaDB](https://wiki.archlinux.org/title/MariaDB)
    
  </details>

  <details hidden>
  <summary><strong>2.1.2. Clone this project's repository</strong></summary>
  
  ```bash
  git clone https://github.com/FaceWithDark/VOS.git # Normal way
  git clone git@github.com:FaceWithDark/VOS.git     # SSH way
  ```
  </details>

  <details hidden>
  <summary><strong>2.1.3. Copy and move all configure files in correct place</strong></summary>
  
  ```bash
  # Please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'

  # Please take a read at the comments within the example environment file to understand why you have to copy them twice.
  cp -v './doc/example.env' './docker.<build-stage>.env'
  cp -v './doc/example.env' './src/private/Configurations/vot.<build-stage>.env'

  cp -v './doc/example.yaml' './docker-compose.<build-stage>.yaml'                # Docker-compose configuration file.
  cp -v './doc/example.conf' './docker/nginx/default.<build-stage>.conf'          # NGINX configuration file.
  ```
  
  For **Dockerfile configuration file**, I have created 3 different Dockerfile profiles for each container, which help debugging process much more easier by letting me fixing the issues within each containers sepearately without having interfere without others. Therefore, you will have to copy the example file 3 times in for 3 different build file and delete one (or two) blocks of code over another (I have explain clearly within the code file).
  
  ```bash
  # Dockerfile configuration file.
  cp -v './doc/example.Dockerfile' './docker/php/php.<build-stage>.Dockerfile'
  cp -v './doc/example.Dockerfile' './docker/php/nginx.<build-stage>.Dockerfile'
  cp -v './doc/example.Dockerfile' './docker/php/mariadb.<build-stage>.Dockerfile'
  ```
  
  _Remove **-v** parameter if you just want to run it without additional information._
  
  </details>

  <details hidden>
  <summary><strong>2.1.4. Run automate build script</strong></summary>

  **By executing the last shell script, all backed-up as well as up-to-date database data will be fully wiped out. Therefore, be sure to back it up somewhere else on your computer (or USB) before action is done.**

  ```bash
  # Ensure that you have Git installed (no need for adding to PATH like Windows build). Then, please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'
  
  ./bin/deploy.sh <build-stage>   # Set the website's docker configuration settings for the first time (or fully re-build).
  ./bin/rebuild.sh <build-stage>  # Reset the website's docker configuration settings without fully killing it and run again.
  ./bin/shutdown.sh <build-stage> # Unset the website's docker configuration settings if not intended to work on it anymore.
  ```
  
  </details>

  <details hidden>
  <summary><strong>2.1.5. Test website URL accessible link</strong></summary>
  
  At this point, you should be able to access the website via _**`http://localhost:<port-number>/`**_. You can find the port number by typing `sudo docker ps` to your command line. However, you will notice that as soon as we open up the website, we received a **SQL-related error**. Please follow along the next step to be able to resolve it smoothly.
  
  </details>

  <details hidden>
  <summary><strong>2.1.6. Access the database with root user</strong></summary>
  
  **Please do not skip this step as it is crucially needed for any further steps.**
  
  ```bash
  # If you set the root password to something else than default (which is no password) then use that when getting asked upon executing the command
  ./bin/access-database.sh
  ```
  
  An example of a succeeded MariaDB connection as a root user would look like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(none)]>
  ```

  </details>

  <details hidden>
  <summary><strong>2.1.7. Create new non-root user</strong></summary>
  
  **It is not recommeded to use** `root user account` **as the primary database login access** _(somehow most large-scale companies still doing so)_. **Therfore, we will have to create a new user account that share almost the same privileges level as** `root user account` **.**
  
  ```sql
  MariaDB [(none)]>CREATE DATABASE 'database-name';
  Query OK, 0 rows affected (0.005 sec)

  MariaDB [(none)]># Can be either 'localhost' (local access level), or '%' (global access level);
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

  </details>

  <details hidden>
  <summary><strong>2.1.8. Access the database again with new user</strong></summary>
  
  ```bash
  ./bin/access-database.sh
  ```

  If you choose to not to directly use the database that you just created, then a succeeded MariaDB connection as a non-root user would look exactly like 2.1.6. Otherwise, it would be like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(${DATABASE_NAME})]>
  ```
  
  Refers to [MariaDB documentation](https://mariadb.com/kb/en/sql-statements/) for further interaction with the database.
  
  </details>
  
</details>

<details hidden>
<summary><strong>2.2. Fedora</strong></summary>

**[Fedora Wiki](https://fedoraproject.org/wiki/Fedora_Project_Wiki)**(TODO: write something for it). 
  
  <details hidden>
  <summary><strong>2.2.1. Install prerequisites</strong></summary>
    
  + [Docker](https://wiki.archlinux.org/title/Docker)
  + [Git](https://wiki.archlinux.org/title/Git)
  + [MariaDB](https://wiki.archlinux.org/title/MariaDB)
    
  </details>

  <details hidden>
  <summary><strong>2.2.2. Clone this project's repository</strong></summary>
  
  ```bash
  git clone https://github.com/FaceWithDark/VOS.git # Normal way
  git clone git@github.com:FaceWithDark/VOS.git     # SSH way
  ```
  </details>

  <details hidden>
  <summary><strong>2.2.3. Copy and move all configure files in correct place</strong></summary>
  
  ```bash
  # Please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'

  # Please take a read at the comments within the example environment file to understand why you have to copy them twice.
  cp -v './doc/example.env' './docker.<build-stage>.env'
  cp -v './doc/example.env' './src/private/Configurations/vot.<build-stage>.env'

  cp -v './doc/example.yaml' './docker-compose.<build-stage>.yaml'                # Docker-compose configuration file.
  cp -v './doc/example.conf' './docker/nginx/default.<build-stage>.conf'          # NGINX configuration file.
  ```
  
  For **Dockerfile configuration file**, I have created 3 different Dockerfile profiles for each container, which help debugging process much more easier by letting me fixing the issues within each containers sepearately without having interfere without others. Therefore, you will have to copy the example file 3 times in for 3 different build file and delete one (or two) blocks of code over another (I have explain clearly within the code file).
  
  ```bash
  # Dockerfile configuration file.
  cp -v './doc/example.Dockerfile' './docker/php/php.<build-stage>.Dockerfile'
  cp -v './doc/example.Dockerfile' './docker/php/nginx.<build-stage>.Dockerfile'
  cp -v './doc/example.Dockerfile' './docker/php/mariadb.<build-stage>.Dockerfile'
  ```
  
  _Remove **-v** parameter if you just want to run it without additional information._
  
  </details>

  <details hidden>
  <summary><strong>2.2.4. Run automate build script</strong></summary>

  **By executing the last shell script, all backed-up as well as up-to-date database data will be fully wiped out. Therefore, be sure to back it up somewhere else on your computer (or USB) before action is done.**

  ```bash
  # Ensure that you have Git installed (no need for adding to PATH like Windows build). Then, please execute these scripts from the project's root directory.
  # For all '<>' placeholder, you will have 3 options corresponding to 3 development stages: 'dev', 'stage', and 'prod'
  
  ./bin/deploy.sh <build-stage>   # Set the website's docker configuration settings for the first time (or fully re-build).
  ./bin/rebuild.sh <build-stage>  # Reset the website's docker configuration settings without fully killing it and run again.
  ./bin/shutdown.sh <build-stage> # Unset the website's docker configuration settings if not intended to work on it anymore.
  ```
  
  </details>

  <details hidden>
  <summary><strong>2.2.5. Test website URL accessible link</strong></summary>
  
  At this point, you should be able to access the website via _**`http://localhost:<port-number>/`**_. You can find the port number by typing `sudo docker ps` to your command line. However, you will notice that as soon as we open up the website, we received a **SQL-related error**. Please follow along the next step to be able to resolve it smoothly.
  
  </details>

  <details hidden>
  <summary><strong>2.2.6. Access the database with root user</strong></summary>
  
  **Please do not skip this step as it is crucially needed for any further steps.**
  
  ```bash
  # If you set the root password to something else than default (which is no password) then use that when getting asked upon executing the command
  ./bin/access-database.sh
  ```
  
  An example of a succeeded MariaDB connection as a root user would look like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(none)]>
  ```

  </details>

  <details hidden>
  <summary><strong>2.2.7. Create new non-root user</strong></summary>
  
  **It is not recommeded to use** `root user account` **as the primary database login access** _(somehow most large-scale companies still doing so)_. **Therfore, we will have to create a new user account that share almost the same privileges level as** `root user account` **.**
  
  ```sql
  MariaDB [(none)]>CREATE DATABASE 'database-name';
  Query OK, 0 rows affected (0.005 sec)

  MariaDB [(none)]># Can be either 'localhost' (local access level), or '%' (global access level);
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

  </details>

  <details hidden>
  <summary><strong>2.2.8. Access the database again with new user</strong></summary>
  
  ```bash
  ./bin/access-database.sh
  ```

  If you choose to not to directly use the database that you just created, then a succeeded MariaDB connection as a non-root user would look exactly like 2.2.6. Otherwise, it would be like below:
  
  ```sql
  Welcome to the MariaDB monitor.  Commands end with ; or \g.
  Your MariaDB connection id is <number>.
  Server version: 11.6.2-MariaDB mariadb.org binary distribution.
  Copyright (c) 2000, 2018, Oracle, MariaDB Corporation Ab and others.
  Type 'help;' or '\h' for help. Type '\c' to clear the current input statement.
  MariaDB [(${DATABASE_NAME})]>
  ```
  
  Refers to [MariaDB documentation](https://mariadb.com/kb/en/sql-statements/) for further interaction with the database.
  
  </details>
  
</details>
