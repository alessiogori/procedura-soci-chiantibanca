@TITLE SCRIPT Soci - mutua_crea_elencosoci.php
@ECHO OFF
COLOR 0a
REM --- CONTROLLA PRESENZA DEL FILE "php.exe"
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
cd "E:\www\soci\"
@ECHO ON
php mutua_crea_elencosoci.php