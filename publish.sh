#!/bin/bash

function publish()
{
    HOST=$1
    DIR=$2
    
    echo "Publishing on host $HOST into directory $DIR"
    
    ncftpput -R -v -u $USERNAME -p $PASSWORD $HOST $DIR export/*
}

read -p "Enter username: " USERNAME
read -p "Enter FTP password: " -s PASSWORD
echo

echo *** PUBLISHING TO NET1 ***
publish ftp.calmarius.net /public_html/stratadi/net1

echo *** PUBLISHING TO XHU1 ***
publish ftp.calmarius.net /public_html/stratadi/net1

