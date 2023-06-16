#!/bin/bash

while true; do
  # Run the command in the background
  now=$(date +"%d/%m/%Y %H:%M:%S")
  echo "chmod start"
  sudo chmod -R 777 manatime_4_6 &
  echo "The current time is $now"

  # Sleep for 10 seconds
  wait
  sleep 30
done
#ps axo pid=,stat= | awk '$2~/^Z/ { print $1 }'