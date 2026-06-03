# Ledger — Double‑Entry Accounting System

Ledger is a double‑entry bookkeeping application built with **Laravel 13** and **MoonShine**.  
It provides full CRUD management for accounts and financial transactions, automatic debit/credit validation, trial balance reports, and a REST API for external integration.

![PHP](https://img.shields.io/badge/PHP-8.3+-blue)
![Laravel](https://img.shields.io/badge/Laravel-13.x-red)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-blue)
![MoonShine](https://img.shields.io/badge/MoonShine-4.x-purple)
![Tests](https://img.shields.io/badge/tests-PHPUnit-brightgreen)

---

## 📖 Table of Contents

- [Technology Stack](#technology-stack)
- [Core Entities](#core-entities)
- [Features](#features)
- [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Database Setup](#database-setup)
    - [Running the Application](#running-the-application)
- [REST API](#rest-api)

---

## Core Entities

- **Account** – financial account (asset, liability, equity, revenue, expense)
- **Transaction** – a financial transaction with a date and description
- **JournalEntry** – individual debit/credit line inside a transaction (minimum 2 entries; total debits must equal total credits)
---

## Features

- ✅ CRUD for **Accounts** via MoonShine
- ✅ CRUD for **Transactions** with inline management of journal entries
- ✅ Transaction list with filters by date and account
- ✅ View journal entries inside a transaction
- ✅ Dynamic form for adding/removing journal entry rows
- ✅ Export transactions to CSV/XLSX
- ✅ **Trial balance report** (opening/closing balances, turnovers) for a given date range
- ✅ Automatic calculation of account balances
- ✅ REST API with basic authentication for creating transactions and fetching account balances
- ✅ Unit tests (PHPUnit) covering transaction creation and debit/credit validation

---

## Getting Started

### Prerequisites

- PHP 8.3 or higher
- Composer 2.x
- PostgreSQL 15+

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/ledger.git
cd ledger

# Install PHP dependencies
composer install

# Copy environment file and edit database settings
cp .env.example .env
nano .env
```

Configure your database connection in .env (PostgreSQL):
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ledger
DB_USERNAME=postgres
DB_PASSWORD=secret
```
Database Setup
```bash

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed the database with sample accounts and transactions
php artisan db:seed

Running the Application
bash

# Start the development server
php artisan serve
```
Now visit http://localhost:8000/admin to access the MoonShine admin panel.
Using Laravel Sail (Docker)

If you prefer Docker, ensure Docker is installed and run:
```bash

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```
## REST API

Documentation is available here:
[Documentation](https://coolrunner1.github.io/ITPelag-Ledger/)
