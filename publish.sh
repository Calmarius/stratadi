#!/bin/bash

function publish()
{
    HOST=$1
    DIR=$2

    echo "Publishing on host $HOST into directory $DIR"

    shopt -s dotglob
    ncftpput -R -v -u $USERNAME -p $PASSWORD $HOST $DIR export/*
    shopt -u dotglob
}

echo "*** CREATING EXPORT ***"
./createexport.sh
echo "DONE."

read -p "Enter username: " USERNAME
read -p "Enter FTP password: " -s PASSWORD
echo

echo "*** PUBLISHING TO XHU1 ***"
publish ftp.calmarius.net /public_html/stratadi/xhu1

