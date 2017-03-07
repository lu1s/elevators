

## Elevator

Elevator is an application that models an Elevator set and its requests from different floors. Things that this application can do.

- Display all elevators and their floor, direction and queue status.
- Show how an elevator request is created and added to the queue to an available elevator.

## Installing

### Requirements

- Php >= 5.6
- MySQL

### Setting up database

- Configure database in .env file to point to an empty MySQL database
- `php artisan migrate`
- `php artisan db:seed`

### Running

- `php artisan serve`
- Go to http://localhost:8000

