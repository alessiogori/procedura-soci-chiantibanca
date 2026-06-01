@TITLE SCRIPT crea_sds_soci.php
@ECHO OFF
COLOR 0a
VER > nul
WHERE php > nul 2>nul
IF %ERRORLEVEL% NEQ 0 (
COLOR 0c
ECHO ERRORE: Eseguibile php.exe non trovato!
ECHO E' necessario eseguire il seguente comando da prompt dei comandi:
ECHO  setx /M PATH ^"%%PATH%%;C:\wamp64\bin\php\php5.6.35^"
ECHO.
GOTO:EOF
)
REM --- AVVIA SCRIPT
@ECHO ON
php crea_sds_soci.php