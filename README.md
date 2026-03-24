# Laravel CSV Transaction Manager

A robust, responsive web application built with Laravel and Tailwind CSS for uploading, parsing, and managing financial transaction records from CSV files.

## 🚀 Features

- **CSV Import:** Seamlessly upload and parse large CSV files.
- **Duplicate Prevention:** Intelligent logic to skip redundant records based on date, description, and amount.
- **Dynamic Filtering:** Filter transactions by Business or Status with real-time query updates.
- **Responsive Design:** A mobile-first UI built with Tailwind CSS, featuring a scrollable data table.
- **Error Handling:** Validates file types (CSV/TXT), file size, and handles empty rows gracefully.

## 🛠️ Tech Stack

- **Framework:** [Laravel 12.x](https://laravel.com)
- **Styling:** [Tailwind CSS](https://tailwindcss.com)
- **Database:** MySQL
- **Icons/UI:** Heroicons (optional)

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/yourusername/transaction-manager.git](https://github.com/yourusername/transaction-manager.git)
   cd transaction-manager
2.  **Cmd / Bash**
  ```bash
  composer install
  npm install && npm run dev
3.  **Setup Env using bash :**
  ```bash
    cp .env.example .env
    php artisan key:generate

    open new bash and run
  ```bash
    php artisan serve
4.  **Update database name on env:**
  if no database create run Xampp and add new database name test_system
  env.
  DB_DATABASE=test_system
  DB_USERNAME=root
  DB_PASSWORD=
