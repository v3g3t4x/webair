#!/bin/bash
exec 3>&1 4>&2
trap 'exec 2>&4 1>&3' 0 1 2 3
exec 1>./logs/skeleton_empty.log 2>&1
echo "****** $(date) ******"


echo "****** $(date) ******"
