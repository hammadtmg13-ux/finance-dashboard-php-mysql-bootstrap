<?php
require 'config.php';

$categories = ['Work', 'Housing', 'Food', 'Transport', 'Utilities', 'Entertainment', 'Education', 'Health', 'Other'];

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->execute([$id]);
$txn = $stmt->fetch();

if (!$txn) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = (float)$_POST['amount'];
    $date = $_POST['transaction_date'];
    $note = trim($_POST['note'] ?? '');

    $stmt = $pdo->prepare("UPDATE transactions SET title = ?, type = ?, category = ?, amount = ?, transaction_date = ?, note = ? WHERE id = ?");
    $stmt->execute([$title, $type, $category, $amount, $date, $note, $id]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction - WealthWise</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">&#128176; WealthWise</a>
        <div class="d-flex align-items-center gap-2">
            <span class="avatar-circle">HK</span>
            <span class="text-white fw-semibold">Hammad Khan</span>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card form-card p-4">
                <h3 class="fw-bold mb-3">Edit Transaction</h3>
                <form method="POST" action="edit_transaction.php?id=<?php echo $txn['id']; ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="255" value="<?php echo htmlspecialchars($txn['title']); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="Expense" <?php echo $txn['type'] === 'Expense' ? 'selected' : ''; ?>>Expense</option>
                                <option value="Income" <?php echo $txn['type'] === 'Income' ? 'selected' : ''; ?>>Income</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category; ?>" <?php echo $txn['category'] === $category ? 'selected' : ''; ?>><?php echo $category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">Amount ($)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required value="<?php echo htmlspecialchars($txn['amount']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="transaction_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" required value="<?php echo htmlspecialchars($txn['transaction_date']); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (optional)</label>
                        <input type="text" class="form-control" id="note" name="note" maxlength="255" value="<?php echo htmlspecialchars($txn['note']); ?>">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
