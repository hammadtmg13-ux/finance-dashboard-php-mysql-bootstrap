-- WealthWise - Personal Finance Dashboard
-- Database: wealthwise_db
-- Run this file in MySQL to create the database and tables

CREATE DATABASE IF NOT EXISTS wealthwise_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wealthwise_db;

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'Expense',
    category VARCHAR(50) NOT NULL DEFAULT 'Other',
    amount DECIMAL(10,2) NOT NULL,
    transaction_date DATE NOT NULL,
    note VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    monthly_limit DECIMAL(10,2) NOT NULL,
    month VARCHAR(7) NOT NULL DEFAULT '2026-11'
);

-- Sample transactions for November 2026
INSERT INTO transactions (title, type, category, amount, transaction_date, note) VALUES
('Monthly Salary', 'Income', 'Work', 3000.00, '2026-11-01', 'Salary from part time work'),
('Freelance Project', 'Income', 'Work', 450.00, '2026-11-10', 'Website for a local client'),
('House Rent', 'Expense', 'Housing', 800.00, '2026-11-02', 'Monthly house rent'),
('Groceries', 'Expense', 'Food', 220.00, '2026-11-08', 'Weekly grocery shopping'),
('Electricity Bill', 'Expense', 'Utilities', 85.00, '2026-11-05', 'Monthly electricity bill'),
('Transport', 'Expense', 'Transport', 120.00, '2026-11-12', 'Bus and ride fares'),
('Netflix', 'Expense', 'Entertainment', 15.49, '2026-11-01', 'Monthly subscription'),
('Online Course', 'Expense', 'Education', 49.00, '2026-11-15', 'PHP advanced course'),
('Gym Membership', 'Expense', 'Health', 49.99, '2026-11-03', 'Monthly gym fee');

-- Sample monthly budgets for November 2026
INSERT INTO budgets (category, monthly_limit, month) VALUES
('Food', 300.00, '2026-11'),
('Transport', 150.00, '2026-11'),
('Entertainment', 100.00, '2026-11');
