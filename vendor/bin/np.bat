@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../turbo124/notification-pusher/np
php "%BIN_TARGET%" %*
