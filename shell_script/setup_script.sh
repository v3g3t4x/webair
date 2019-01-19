#!/bin/bash
exec 3>&1 4>&2
trap 'exec 2>&4 1>&3' 0 1 2 3
exec 1>./logs/setup_script.log 2>&1
echo "****** $(date) ******"
echo "Setup script"
#clear all crontab rows
#create crontab item for read sensor
#create crontab item for take_photo


echo "****** $(date) ******"