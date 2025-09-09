#!/bin/bash

read -p "Enter your database name here (e.g: root): " DATABASE_NAME
read -p "Enter your database port here (e.g: 3306): " DATABASE_PORT
read -p "Enter your database connection type here (e.g: TCP/IP): " DATABASE_PROTOCOL
read -p "Enter your database host here (e.g: localhost/[public-ip-address]): " DATABASE_HOST

# Double check with the user as they may have multiple databases
read -p "Do you want to use any databases by default (Y/N): " DATABASE_USAGE_CONFIRMATION
case $DATABASE_USAGE_CONFIRMATION in
    y|Y|yes|Yes|YES)
        read -p "Enter your database for usage here: " DATABASE_USAGE_NAME

        # Double check if user wants to run SQL files before accessing database
        read -p "Do you want to run any SQL files before accessing the database (Y/N): " DATABASE_SQL_CONFIRMATION
        case $DATABASE_SQL_CONFIRMATION in
            y|Y|yes|Yes|YES)
                read -p "Enter your database SQL file path here (Windows: '\\' | Linux:'/'): " DATABASE_SQL_FILE
                ;;
            n|N|no|No|NO)
                DATABASE_SQL_FILE=""  # empty string to the final command
                ;;
            *)
                echo 'Invalid option provided, please try again!!'
                return 1
                ;;
        esac
        ;;
    n|N|no|No|NO)
        DATABASE_USAGE_NAME=""  # empty string to the final command
        ;;
    *)
        echo 'Invalid option provided, please try again!!'
        return 1
        ;;
esac

if [[ -z "$DATABASE_USAGE_NAME" && -z "$DATABASE_SQL_FILE" ]]; then
    # No default database, no SQL file
    mariadb -u "$DATABASE_NAME" -P "$DATABASE_PORT" --protocol="${DATABASE_PROTOCOL^^}" -h "$DATABASE_HOST" -p
elif [[ -n "$DATABASE_USAGE_NAME" && -z "$DATABASE_SQL_FILE" ]]; then
    # Use default database, no SQL file
    mariadb -u "$DATABASE_NAME" -P "$DATABASE_PORT" --protocol="${DATABASE_PROTOCOL^^}" -h "$DATABASE_HOST" "$DATABASE_USAGE_NAME" -p
elif [[ -z "$DATABASE_USAGE_NAME" && -n "$DATABASE_SQL_FILE" ]]; then
    # Cannot run SQL files without specifying a database
    echo 'How are you going to run the SQL files if no database is specified?? Please try again!!'
    exit 1
else
    # Both default database and SQL file specified
    mariadb -u "$DATABASE_NAME" -P "$DATABASE_PORT" --protocol="${DATABASE_PROTOCOL^^}" -h "$DATABASE_HOST" "$DATABASE_USAGE_NAME" -p < "$DATABASE_SQL_FILE"
fi
