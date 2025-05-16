#!/bin/bash

# URL of the PHP app (adjust if needed)
BASE_URL="http://localhost:8080"

# Number of user IDs to simulate (adjust if you know your DB contents)
MAX_USER_ID=4

while true; do
    # Hit the main page
    echo "Requesting $BASE_URL/ ..."
    curl -s -o /dev/null "$BASE_URL/"

    # Pick a random user ID
    USER_ID=$(( ( RANDOM % MAX_USER_ID )  + 1 ))
    USER_URL="$BASE_URL/person/$USER_ID"
    echo "Requesting $USER_URL ..."
    curl -s -o /dev/null "$USER_URL"

    # Wait a random decimal between 0 and 3 seconds before next iteration
    SLEEP_TIME=$(awk -v min=0 -v max=3 'BEGIN{srand(); print min+rand()*(max-min)}')
    sleep $SLEEP_TIME
done
