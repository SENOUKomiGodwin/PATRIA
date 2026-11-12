#!/bin/bash

# Run migrations and seeds
php artisan migrate --force --seed

# Start the scheduler in the background
php artisan schedule:work &

# Start the Laravel server
php artisan serve --host=0.0.0.0 --port=8000
