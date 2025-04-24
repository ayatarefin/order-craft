# Production Order Management System (OrderCraft)

## 🚀 Objective
This web-based application facilitates the management of production orders with multiple image files. It allows employees to claim batches of files to edit and prevents duplication by locking claimed files. Admins can track progress in real time and manage users.

---

## ⚙️ Features
- Admin-only user creation (no public registration)
- Role-based access (Admin, Employee)
- Order and file upload system (supports nested folders)
- File claiming system (10-20 at a time)
- Real-time progress tracking with WebSockets (Laravel Echo + Reverb)
- Admin dashboard with live stats
- Logs for all actions: claimed, completed, etc.
- Basic integration-ready for Adobe Photoshop/Illustrator workflows

- Set your php.ini based on your needs

---

## 🛠️ Tech Stack
- Laravel 12
- Laravel Breeze (for auth scaffolding)
- Laravel Reverb (WebSockets)
- Pusher PHP Server
- Inertia + React (if applicable)
- MySQL

---

## 🧪 Local Setup Instructions

### 1. Clone the Repo
```bash
git clone https://github.com/ayatarefin/order-craft.git
cd order-craft

### 2. Install Dependencies
composer install
npm install && npm run dev

### 3. Set Up Environment File
cp .env.example .env
php artisan key:generate

- Update your .env with DB and Pusher credentials.

### 4. Run Migrations & Seeders
php artisan migrate
php artisan db:seed --class=AdminSeeder

### 5. Start the App
php artisan serve

### Default Admin Account (after seeder)
Email: ayatarefin@ordercraft.com
Password: password123456

- You can use this to create new employees or more admins from the Admin dashboard.

## Project Scripts
composer run dev
# Runs: server, queue listener, pail logs, and Vite