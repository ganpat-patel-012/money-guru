# MoneyGuru 2.0

A personal money lending and borrowing tracker web application built with PHP, MySQL, and Docker. Track who owes you money and who you owe — with a clean dashboard, transaction history, and settle/unsettle functionality.

## Features

- User registration and login with session management
- Add transactions (lend / borrow) between registered users
- Dashboard showing total borrowed, total lent, and net balance
- Settle or unsettle transactions with one click
- Delete transactions (only by the user who created them)
- Edit user profile (name, email, phone, address)
- Fully containerized with Docker Compose — no manual setup required

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2 |
| Database | MySQL 8.1 |
| Frontend | HTML, CSS (vanilla) |
| Web Server | Apache (via php:8.2-apache image) |
| DB Admin UI | phpMyAdmin |
| Container | Docker + Docker Compose |

## Project Structure

```
moneyguru/
├── docker-compose.yml          # Spins up web + db + phpmyadmin
├── Database.sql                # Schema: Users, Transactions tables + triggers
├── Data-dump.sql               # Sample seed data
└── html/
    ├── index.php               # Landing page
    ├── login.php               # Login form
    ├── logout.php              # Session destroy
    ├── register.php            # New user registration
    ├── dashboard.php           # Main view: balance cards + transaction tables
    ├── add_transaction.php     # Add lend/borrow transaction
    ├── delete_transaction.php  # Delete own transaction
    ├── modify_register.php     # Edit profile
    └── fetch_user.php          # AJAX user lookup
```

## Database Schema

**Users** — stores user profile with email uniqueness constraint and password length check.

**Transactions** — stores lend/borrow records with foreign keys to both the user who added it and the user it is for. A trigger enforces that a user cannot create a transaction with themselves.

## Getting Started

### Prerequisites
- Docker Desktop installed and running

### Run the App

```bash
git clone https://github.com/ganpat-patel-012/moneyguru.git
cd moneyguru
docker-compose up --build
```

Then open:
- App: http://localhost:80
- phpMyAdmin: http://localhost:8080 (user: `root`, password: `root_password`)

### Import the Schema

Once the containers are running, import `Database.sql` via phpMyAdmin or:

```bash
docker exec -i <db-container-name> mysql -u root -proot_password moneyguru < Database.sql
```

## Team

Built by **Ganpat Patel**, Adnan Ali, Musa Ummar, Jatinkumar Keshabhai Parmar
EPITA MS — Semester 1 | Operating Systems Unix
