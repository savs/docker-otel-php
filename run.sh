#!/bin/bash
clear
docker compose down --remove-orphans; clear; docker compose up -d --build
