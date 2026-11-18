<?php
require 'config.php';

$month = '2026-11';

// Totals for the month
$totals = $pdo->query("SELECT type, SUM(amount) AS total FROM transactions WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$month' GROUP BY type")->fetchAll(PDO::FETCH_KEY_PAIR);
$totalIncome = $totals['Income'] ?? 0;
$totalExpense = $totals['Expense'] ?? 0;
$balance = $totalIncome - $totalExpense;
$savingsRate = $totalIncome > 0 ? round(($balance / $totalIncome) * 100) : 0;

// Spending by category for the donut chart
$catStmt = $pdo->query("SELECT category, SUM(amount) AS total FROM transactions WHERE type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = '$month' GROUP BY category ORDER BY total DESC");
$categories = $catStmt->fetchAll();

// Budgets with spent amounts
$budgetStmt = $pdo->query("SELECT b.category, b.monthly_limit, COALESCE(SUM(t.amount), 0) AS spent FROM budgets b LEFT JOIN transactions t ON t.category = b.category AND t.type = 'Expense' AND DATE_FORMAT(t.transaction_date, '%Y-%m') = b.month WHERE b.month = '$month' GROUP BY b.category, b.monthly_limit");
$budgets = $budgetStmt->fetchAll();

// Recent transactions
$txnStmt = $pdo->query("SELECT * FROM transactions ORDER BY transaction_date DESC, id DESC LIMIT 10");
$transactions = $txnStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WealthWise - Personal Finance Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">&#128176; WealthWise</a>
        <div class="d-flex align-items-center gap-2">
            <a href="monthly_report.php" class="btn btn-outline-light btn-sm">Monthly Report</a>
            <span class="avatar-circle">HK</span>
            <span class="text-white fw-semibold">Hammad Khan</span>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Finance Dashboard</h2>
            <p class="text-muted mb-0">November 2026 &middot; Know where every rupee goes.</p>
        </div>
        <a href="add_transaction.php" class="btn btn-success mt-2 mt-md-0">+ Add Transaction</a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-success">$<?php echo number_format($balance, 2); ?></div>
                <div class="stat-label">Total Balance</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-primary">$<?php echo number_format($totalIncome, 2); ?></div>
                <div class="stat-label">Income This Month</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-danger">$<?php echo number_format($totalExpense, 2); ?></div>
                <div class="stat-label">Expenses This Month</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-warning"><?php echo $savingsRate; ?>%</div>
                <div class="stat-label">Savings Rate</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card chart-card p-3">
                <h5 class="fw-bold mb-3">Income vs Expenses &middot; Last 6 Months</h5>
                <canvas id="trendChart" height="220"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card chart-card p-3">
                <h5 class="fw-bold mb-3">Spending by Category</h5>
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>
    </div>

    <!-- Budgets -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card budget-card p-3">
                <h5 class="fw-bold mb-3">Monthly Budgets</h5>
                <?php foreach ($budgets as $budget):
                    $percent = $budget['monthly_limit'] > 0 ? min(100, round(($budget['spent'] / $budget['monthly_limit']) * 100)) : 0;
                    $barClass = $percent >= 90 ? 'bg-danger' : ($percent >= 70 ? 'bg-warning' : 'bg-success');
                ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold"><?php echo htmlspecialchars($budget['category']); ?></span>
                            <span class="text-muted">$<?php echo number_format($budget['spent'], 2); ?> of $<?php echo number_format($budget['monthly_limit'], 2); ?> &middot; <?php echo $percent; ?>%</span>
                        </div>
                        <div class="progress budget-progress mt-1">
                            <div class="progress-bar <?php echo $barClass; ?>" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="card table-card p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Transactions</h5>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search transactions..." style="width: 200px;">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-success active" data-type-filter="All">All</button>
                    <button type="button" class="btn btn-outline-success" data-type-filter="Income">Income</button>
                    <button type="button" class="btn btn-outline-success" data-type-filter="Expense">Expense</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $txn): ?>
                        <tr class="txn-row" data-type="<?php echo $txn['type']; ?>">
                            <td>
                                <span class="fw-semibold"><?php echo htmlspecialchars($txn['title']); ?></span>
                                <?php if ($txn['note']): ?><br><small class="text-muted"><?php echo htmlspecialchars($txn['note']); ?></small><?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($txn['category']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($txn['transaction_date'])); ?></td>
                            <td><span class="badge <?php echo $txn['type'] === 'Income' ? 'bg-success' : 'bg-danger'; ?>"><?php echo $txn['type']; ?></span></td>
                            <td class="text-end <?php echo $txn['type'] === 'Income' ? 'amount-income' : 'amount-expense'; ?>"><?php echo $txn['type'] === 'Income' ? '+' : '-'; ?>$<?php echo number_format($txn['amount'], 2); ?></td>
                            <td class="text-end">
                                <a href="edit_transaction.php?id=<?php echo $txn['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="delete_transaction.php?id=<?php echo $txn['id']; ?>" class="btn btn-sm btn-outline-danger delete-txn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
