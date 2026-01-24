# AIU Maintenance Management System (MMS)

A comprehensive Laravel 12 + Blade-based maintenance management system designed for efficient equipment maintenance tracking, task assignment, and inventory management.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Demo Credentials](#demo-credentials)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Tech Stack](#tech-stack)
- [Development](#development)
- [Testing](#testing)
- [License](#license)

## Features

### Admin Features
- **Equipment Management**: Track and manage lifts, chillers, and other maintenance equipment
- **Task Assignment & Tracking**: Create, assign, and monitor maintenance tasks
- **Worker Management**: Add, edit, and manage worker profiles and trades
- **Inventory Management**: Track parts and supplies with low-stock alerts
- **Task Templates**: Create reusable task templates for recurring maintenance
- **Reporting**: Generate and view maintenance reports
- **Dashboard**: Comprehensive admin dashboard with system overview

### Worker Features
- **Task View**: View assigned maintenance tasks with priorities
- **Task Completion**: Mark tasks as complete and add notes
- **Profile Management**: Update personal profile information
- **Dashboard**: Worker-specific dashboard showing assigned tasks and statistics

### System Features
- Role-based access control (Admin/Worker)
- Real-time notifications
- Responsive design
- Database queue support
- Session-based authentication

## Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL 5.7+ / PostgreSQL / SQLite
- Web server (Apache/Nginx) or use Laravel's built-in server

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/reem01/AIU-MMS
cd AIU-MMS
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install
```

### 3. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

Configure your database in the `.env` file:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aiu_mms
DB_USERNAME=root
DB_PASSWORD=your_password
```



### 5. Run Migrations and Seeders

```bash
php artisan migrate:fresh --seed
```

This will create all necessary tables and seed demo data including admin and worker accounts.

### 6. Build Assets

```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

## Quick Start

### Using Composer Scripts

The easiest way to get started:

```bash
# Complete setup (install, migrate, build)
composer setup

# Start development environment (server + queue + logs + vite)
composer dev
```

### Manual Start

```bash
# Start the development server
php artisan serve

# In another terminal, start the queue worker (optional)
php artisan queue:work

# In another terminal, start Vite (for asset compilation)
npm run dev
```

Visit http://localhost:8000 in your browser.


### Session Configuration

Sessions are stored in the database by default. The migration for sessions is included.

### Cache Configuration

The system uses database cache. Ensure the cache table is migrated.

## Demo Credentials

**Admin Account:**
- Username: `admin`
- Password: `adminpass`

**Worker Accounts:**
- Username: `worker1` | Password: `workerpass`
- Username: `worker2` | Password: `workerpass2`

> **Note**: Change these credentials in production by running migrations without seeds and creating your own admin account.

## Usage

### Admin Workflow

1. **Login** as admin using the credentials above
2. **Manage Workers**: Navigate to Workers section to add/edit worker profiles
3. **Manage Equipment**: Add lifts, chillers, and other equipment to track
4. **Create Task Templates**: Set up recurring maintenance task templates
5. **Assign Tasks**: Create and assign tasks to workers with priorities
6. **Monitor Inventory**: Track parts and receive low-stock alerts
7. **View Reports**: Generate maintenance reports and analytics

### Worker Workflow

1. **Login** as a worker
2. **View Dashboard**: See assigned tasks and statistics
3. **Complete Tasks**: Mark tasks as done and add completion notes
4. **Update Profile**: Manage personal information

## Project Structure

```
AIU-MMS/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin-specific controllers
│   │   │   ├── Auth/           # Authentication controllers
│   │   │   └── Worker/         # Worker-specific controllers
│   │   └── Middleware/         # Custom middleware (RoleMiddleware)
│   └── Models/                 # Eloquent models
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── public/                     # Public assets
├── resources/
│   ├── views/                  # Blade templates
│   └── css/                    # Stylesheets
├── routes/
│   └── web.php                 # Web routes
└── storage/                    # Application storage
```

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates, TailwindCSS 4.0
- **Database**: MySQL/PostgreSQL/SQLite
- **Build Tools**: Vite 7.0
- **Queue**: Database-backed queues
- **Session**: Database-backed sessions
- **Cache**: Database-backed cache






This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
