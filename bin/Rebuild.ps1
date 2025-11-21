#!/usr/bin/env pwsh

param (
    [String]$BuildStage,
    [String]$YamlFile   = "docker-compose.$BuildStage.yaml",
    [String]$EnvFile    = "docker.$BuildStage.env"
)

[String]$NewBuildStage = $BuildStage.ToLower()
[String]$BuildShutdown =
@"

+==================================================================+
|                                                                  |
|   SHUTDOWN RUNNING APPLICATION FOR [$NewBuildStage] BUILD STAGE  |
|                                                                  |
+==================================================================+

"@
[String]$BuildRebuild =
@"

+==================================================================+
|                                                                  |
|       REBUILD APPLICATION FOR [$NewBuildStage] BUILD STAGE       |
|                                                                  |
+==================================================================+

"@


function Stage-Rebuild
{
    param (
        [String]$BuildPath,
        [String]$YamlPath,
        [String]$EnvPath
    )

    Write-Host("Found valid [$BuildPath] build stage. Move on to the next step...")
    (Start-Sleep -Milliseconds 2000)

    if (!(Test-Path($YamlPath)))
    {
        Write-Host("[$YamlPath] is invalid YAML file format. Double check with the setup guide and try again!!")
        Exit 1
    } else
    {
        Write-Host("Found valid [$YamlPath] file. Move on to the next step...")
        (Start-Sleep -Milliseconds 2000)

        if (!(Test-Path($EnvPath)))
        {
            Write-Host("[$EnvPath] is invalid ENV file format. Double check with the setup guide and try again!!")
            Exit 1
        } else
        {
            Write-Host("Found valid [$EnvPath] file. Move on to the final step...")
            (Start-Sleep -Milliseconds 2000)

            Write-Host($BuildShutdown)
            docker compose --file "$YamlPath" --env-file "$EnvPath" down

            Write-Host($BuildRebuild)
            docker compose --file "$YamlPath" --env-file "$EnvPath" up --build -d

            if ($LASTEXITCODE -eq 1)
            {
                Write-Host("`n`rFAILED TO REBUILD APPLICATION FOR [$BuildPath] BUILD STAGE!!")
                Exit 1
            } else
            {
                Write-Host("`n`rSUCCEED TO REBUILD APPLICATION FOR [$BuildPath] BUILD STAGE!!")
            }
        }
    }
}


if ([String]::IsNullOrEmpty($BuildStage) || [String]::IsNullOrWhiteSpace($BuildStage))
{
    Write-Host('Invalid build stage provided, please use one of the following option: [dev], [stage], [prod]!!')
    Exit 1
} else
{
    switch ($NewBuildStage)
    {
        'dev'
        {
            Stage-Rebuild -BuildPath $NewBuildStage -YamlPath $YamlFile -EnvPath $EnvFile
        }

        'stage'
        { 
            Stage-Rebuild -BuildPath $NewBuildStage -YamlPath $YamlFile -EnvPath $EnvFile
        }

        'prod'
        { 
            Stage-Rebuild -BuildPath $NewBuildStage -YamlPath $YamlFile -EnvPath $EnvFile
        }

        Default
        {
            Write-Host('Invalid build stage provided, please use one of the following option: [dev], [stage], [prod]!!')
            Exit 1
        } 
    }
}
