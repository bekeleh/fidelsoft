@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../cerdic/css-tidy/bin/pcsstidy
php "%BIN_TARGET%" %*
