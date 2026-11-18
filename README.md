# WealthWise - Personal Finance Dashboard

A personal finance dashboard built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Track income and expenses, see where your money goes with charts, and stay inside your monthly budgets.

## Features

- Add income and expense transactions with category and date
- Live balance, income, expenses and savings rate
- Income vs expenses trend chart for the last 6 months
- Spending breakdown by category with a donut chart
- Monthly budgets with progress bars
- Search transactions and filter by type and category
- Edit and delete transactions
- Monthly report page with totals and top categories
- Clean responsive layout with Bootstrap 5 and Chart.js

## Tech Stack

- HTML, CSS, JavaScript
- Bootstrap 5
- Chart.js for charts
- PHP with PDO for safe database queries
- MySQL

## Setup

1. Import `database.sql` into MySQL to create the `wealthwise_db` database, the `transactions` and `budgets` tables, and sample data.
2. Update the database credentials in `config.php` if needed.
3. Place the project folder in your web server directory (for example `htdocs` in XAMPP).
4. Open `index.php` in your browser.

## Files

- `index.php` - Dashboard with balance, charts, budgets and transactions
- `add_transaction.php` - Add a new transaction
- `edit_transaction.php` - Edit an existing transaction
- `delete_transaction.php` - Delete a transaction
- `monthly_report.php` - Monthly summary with totals and top categories
- `config.php` - Database connection
- `database.sql` - Database and table setup with sample data
- `assets/css/style.css` - Custom styles
- `assets/js/app.js` - Charts, search, filter and form helpers
