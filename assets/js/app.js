// WealthWise - Frontend helpers and charts

document.addEventListener('DOMContentLoaded', function () {

    // Income vs expenses trend chart (last 6 months)
    var trendCanvas = document.getElementById('trendChart');
    if (trendCanvas && typeof Chart !== 'undefined') {
        new Chart(trendCanvas, {
            type: 'bar',
            data: {
                labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [
                    {
                        label: 'Income',
                        data: [3200, 3200, 3450, 3450, 3450, 3450],
                        backgroundColor: '#198754'
                    },
                    {
                        label: 'Expenses',
                        data: [1250, 1400, 1320, 1180, 1450, 1339],
                        backgroundColor: '#dc3545'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Spending by category donut chart
    var donutCanvas = document.getElementById('categoryChart');
    if (donutCanvas && typeof Chart !== 'undefined') {
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Housing', 'Food', 'Transport', 'Utilities', 'Education', 'Health', 'Entertainment'],
                datasets: [{
                    data: [800, 220, 120, 85, 49, 49.99, 15.49],
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#6f42c1', '#fd7e14', '#20c997', '#d63384']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Live search: filter transaction rows by title, category or note
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var query = this.value.toLowerCase().trim();
            document.querySelectorAll('.txn-row').forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    }

    // Type filter buttons (All, Income, Expense)
    document.querySelectorAll('[data-type-filter]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = this.getAttribute('data-type-filter');
            document.querySelectorAll('[data-type-filter]').forEach(function (b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
            document.querySelectorAll('.txn-row').forEach(function (row) {
                var rowType = row.getAttribute('data-type');
                row.style.display = (type === 'All' || rowType === type) ? '' : 'none';
            });
        });
    });

    // Confirm before deleting a transaction
    document.querySelectorAll('.delete-txn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Delete this transaction? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
