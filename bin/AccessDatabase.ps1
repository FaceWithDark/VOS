#!/usr/bin/env pwsh

param (
    [String]$DatabaseName,
    [Int]$DatabasePort,
    [String]$DatabaseProtocol,
    [String]$DatabaseHost,
    [String]$DatabaseUsageConfirmation = 'N',
    [String]$DatabaseUsageName,
    [String]$DatabaseSqlConfirmation = 'N',
    [String]$DatabaseSqlFile
)

$DatabaseName               = Read-Host('Enter your database name here (e.g: root)')
$DatabasePort               = Read-Host('Enter your database port here (e.g: 3306)')
$DatabaseProtocol           = Read-Host('Enter your database connection type here (e.g: TCP/IP)')
$DatabaseHost               = Read-Host('Enter your database host here (e.g: localhost/[public-ip-address])')

# Double check with the user as they may have multiple databases
$DatabaseUsageConfirmation  = Read-Host('Do you want to use any databases by default (Y/N)')

switch -Regex ($DatabaseUsageConfirmation)
{
    ("^(?i)(y|yes)$")
    {
        $DatabaseUsageName = Read-Host('Enter your database for usage here')

        # Double check if user wants to run SQL files before accessing database
        $DatabaseSqlConfirmation = Read-Host('Do you want to run any SQL files before accessing the database (Y/N)')
        switch -Regex ($DatabaseSqlConfirmation)
        {
            ("^(?i)(y|yes)$")
            {
                $DatabaseSqlFile = Read-Host("Enter your database SQL file path here")
            }

            ("^(?i)(n|no)$")
            {
                $DatabaseSqlFile = '' # Empty string to the final command
            }

            Default
            {
                Write-Host('Invalid option provided, please try again!!')
                Exit 1
            }
        }
    }

    ("^(?i)(n|no)$")
    {
        $DatabaseUsageName = '' # Empty string to the final command

        # Double check if user wants to run SQL files before accessing database
        $DatabaseSqlConfirmation = Read-Host('Do you want to run any SQL files before accessing the database (Y/N)')
        switch -Regex ($DatabaseSqlConfirmation)
        {
            ("^(?i)(y|yes)$")
            {
                $DatabaseSqlFile = Read-Host("Enter your database SQL file path here")
            }

            ("^(?i)(n|no)$")
            {
                $DatabaseSqlFile = '' # Empty string to the final command
            }

            Default
            {
                Write-Host('Invalid option provided, please try again!!')
                Exit 1
            }
        }
    }

    Default
    {
        Write-Host('Invalid option provided, please try again!!')
        Exit 1
    }
}


[Boolean]$HasDatabaseName   = [Boolean]$DatabaseUsageName
[Boolean]$HasSqlFile        = [Boolean]$DatabaseSqlFile

switch ("$HasDatabaseName | $HasSqlFile")
{
    'False | False'
    {
        # No default database, no SQL file
        mariadb -u $DatabaseName -P $DatabasePort --protocol=$($DatabaseProtocol.ToUpper()) -h $DatabaseHost -p
    }

    'True | False'
    {
        # Use default database, no SQL file
        mariadb -u $DatabaseName -P $DatabasePort --protocol=$($DatabaseProtocol.ToUpper()) -h $DatabaseHost $DatabaseUsageName -p
    }

    'False | True'
    {
        # Cannot run SQL files without specifying a database
        Write-Host('How are you going to run the SQL files if no database is specified?? Please try again!!')
        Exit 1
    }

    'True | True'
    {
        # Both default database and SQL file specified (PowerShell is such a bitch
        # with Unix-like syntax for some reasons)
        Get-Content($DatabaseSqlFile) -Raw | mariadb -u $DatabaseName -P $DatabasePort --protocol=$($DatabaseProtocol.ToUpper()) -h $DatabaseHost $DatabaseUsageName -p
    }

    Default
    {
        Exit 1
    }
}
