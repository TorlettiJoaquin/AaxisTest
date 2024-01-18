# Product Management System

This project is a simple Product Management System built using Symfony 7 and PostgreSQL. It includes a REST API for product operations such as adding, updating, listing, and deleting products, along with token-based authentication. Additionally, it features a basic frontend interface for interacting with the API.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

What things you need to install the software and how to install them:

- PHP (version 8.0 or higher)
- Symfony (version 7)
- Composer
- PostgreSQL (version 16)

### Installing

A step-by-step series of examples that tell you how to get a development environment running.

Clone the repository:

```bash
git clone https://github.com/TorlettiJoaquin/AaxisTest.git
cd AaxisTest
```

Install dependencies using Composer:

```bash
composer install
```

After installing dependencies, you need to configure the database connection. The project is configured to use PostgreSQL. Update the `DATABASE_URL` in the `.env` file with your PostgreSQL credentials. For example:

```env
# .env
# ...
# Configure your db connection here
DATABASE_URL="postgresql://db_user:db_password@localhost:5432/aaxistest?serverVersion=16&charset=utf8"
```

Make sure that PostgreSQL is running and create the database:
```bash
php bin/console doctrine:database:create
```


Run the database migrations to set up the required tables:

```bash
php bin/console doctrine:migrations:migrate
```

### Running the Application

Start the Symfony server:

```bash
symfony server:start
```

Open http://localhost:8000 in your browser to view the application.

### Using the API

You can interact with the API using tools like cURL or Postman. Here are the available endpoints:

- GET /api/products - List all products.
- POST /api/products - Add a new product.
- PUT /api/products - Update an existing product.
- DELETE /api/products - Delete a product.

### Built with

- Symfony - The web framework used
- PostgreSQL - Database Management System
- PHP - Programming Language

### Author

- Axel Joaquin Torletti

### License

This project is licensed under the MIT License.

### Acknowledgments

Thanks to AAXIS for considering me for this position.