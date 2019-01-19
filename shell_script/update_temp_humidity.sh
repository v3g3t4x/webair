#!/bin/bash
exec 3>&1 4>&2
trap 'exec 2>&4 1>&3' 0 1 2 3
exec 1>./logs/update_temp_humidity.log 2>&1
echo "****** $(date) ******"
echo "SENSOR READ | START"
echo "Run command"
echo "cut output"
echo "execute insert on mysql"
echo "SENSOR READ | END"


echo "****** $(date) ******"