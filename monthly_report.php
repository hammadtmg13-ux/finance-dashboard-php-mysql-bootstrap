<?php
require 'config.php';

$month = $_GET['month'] ?? '2026-11';

// Totals for the selected month
$totals = $pdo->query("SELECT type, SUM(amount) AS total, COUNT(*) AS count FROM transactions WHERE DATE_FORMAT(transaction_date, '%Y-%m') = '$month' GROUP BY type")->fetchAll(PDO::FETCH_ASSOC);
$summary = ['Income' => ['total' => 0, 'count' => 0], 'Expense' => ['total' => 0, 'count' => 0]];
foreach ($totals as $row) {
    $summary[$row['type']] = ['total' => $row['total'], 'count' => $row['count']];
}
$balance = $summary['Income']['total'] - $summary['Expense']['total'];

// Top spending categories
$topStmt = $pdo->query("SELECT category, SUM(amount) AS total FROM transactions WHERE type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = '$month' GROUP BY category ORDER BY total DESC LIMIT 5");
$topCategories = $topStmt->fetchAll();

// Biggest single expense
$biggest = $pdo->query("SELECT * FROM transactions WHERE type = 'Expense' AND DATE_FORMAT(transaction_date, '%Y-%m') = '$month' ORDER BY amount DESC LIMIT 1")->fetch();

// Months available
$months = $pdo->query("SELECT DISTINCT DATE_FORMAT(transaction_date, '%Y-%m') AS month FROM transactions ORDER BY month DESC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - WealthWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">&#128176; WealthWise</a>
        <div class="d-flex align-items-center gap-2">
            <a href="index.php" class="btn btn-outline-light btn-sm">Dashboard</a>
            <span class="avatar-circle">HK</span>
            <span class="text-white fw-semibold">Hammad Khan</span>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Monthly Report</h2>
            <p class="text-muted mb-0">A clear summary of your month.</p>
        </div>
        <form method="GET" action="monthly_report.php" class="mt-2 mt-md-0">
            <select name="month" class="form-select" onchange="this.form.submit()">
                <?php foreach ($months as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo $m === $month ? 'selected' : ''; ?>><?php echo date('F Y', strtotime($m . '-01')); ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-success">$<?php echo number_format($balance, 2); ?></div>
                <div class="stat-label">Balance</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-primary">$<?php echo number_format($summary['Income']['total'], 2); ?></div>
                <div class="stat-label">Income (<?php echo $summary['Income']['count']; ?> entries)</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-danger">$<?php echo number_format($summary['Expense']['total'], 2); ?></div>
                <div class="stat-label">Expenses (<?php echo $summary['Expense']['count']; ?> entries)</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-value text-warning"><?php echo $biggest ? htmlspecialchars($biggest['title']) : '-'; ?></div>
                <div class="stat-label">Biggest Expense<?php echo $biggest ? ' ($' . number_format($biggest['amount'], 2) . ')' : ''; ?></div>
            </div>
        </div>
    </div>

    <div class="card table-card p-3">
        <h5 class="fw-bold mb-3">Top Spending Categories</h5>
        <?php if (empty($topCategories)): ?>
            <p class="text-muted mb-0">No expenses this month.</p>
        <?php endif; ?>
        <?php foreach ($topCategories as $cat): ?>
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="fw-semibold"><?php echo htmlspecialchars($cat['category']); ?></span>
                <span class="amount-expense">$<?php echo number_format($cat['total'], 2); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
