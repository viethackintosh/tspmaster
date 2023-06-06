#!/bin/bash
while true; do
      inotifywait -e modify,create,delete -r ../tspmaster/ 
      php watch.php
done       
