<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/inti/gearUp/db_connection.php';
$username = $_SESSION['username'] ?? ''; // Make sure $username is available
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Summary</title>
  <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fff;
        margin: 0;
        padding: 0;
        color: #333;
    }

    #container {
        margin-left:200px;
        max-width: 900px;
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(70, 90, 120, 0.1);
    }

    h1, h2 {
        color: #3a3a7e;
        margin-bottom: 15px;
        font-weight: 700;
    }

    ul {
        list-style: none;
        padding-left: 0;
        margin-bottom: 40px;
    }

    ul li {
        margin-bottom: 10px;
        font-size: 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
    }

    th, td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #e6e6fa;
        color: #333;
        font-weight: 600;
    }

    tr:nth-child(even) {
        background-color: #f6f6ff;
    }

    tr:hover {
        background-color: #dcdcff;
    }

</style>

</head>
<body>
<div id="container">
    <h1>Sales Summary (Seller)</h1>

    <?php
    // Get seller_id
    $sql = "
        SELECT s.seller_id 
        FROM users u
        JOIN seller s ON u.user_id = s.user_id
        WHERE u.usernames = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $seller_id = null;
    if ($row = $result->fetch_assoc()) {
        $seller_id = $row['seller_id'];
    }

    if (!$seller_id) {
        echo "<p>Error: Seller not found.</p>";
        exit;
    }

    // Total Sales
    $salesQuery = "
        SELECT 
            o.total_price,
            COUNT(DISTINCT o.order_id) AS completed_orders,
            SUM(oi.quantity) AS total_items
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN products p ON oi.product_id = p.product_id 
        WHERE o.order_status = 'purchased' AND p.seller_id = ? AND o.wallet_status = 'paid'
    ";
    $stmt = $conn->prepare($salesQuery);
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $salesResult = $stmt->get_result();
    $salesData = $salesResult->fetch_assoc();

    // Calculate commission
    $commissionRate = 0.05; // 5%
    $grossSales = $salesData['total_price'] ?? 0;
    $netSales = $grossSales * (1 - $commissionRate);

    // Top 5 Products
    $topProductsQuery = "
        SELECT 
            p.product_name,
            SUM(oi.quantity) AS total_sold,
            SUM(oi.quantity * oi.price) AS total_revenue
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.order_status = 'purchased' AND p.seller_id = ? AND o.wallet_status = 'paid'
        GROUP BY oi.product_id
        ORDER BY total_sold DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($topProductsQuery);
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $topProductsResult = $stmt->get_result();

    // Sales by Store
    $storeSalesQuery = "
        SELECT 
            s.storeName, o.total_price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        JOIN seller s ON p.seller_id = s.seller_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.order_status = 'purchased' AND p.seller_id = ? AND o.wallet_status = 'paid' 
        GROUP BY s.storeName
    ";
    $stmt = $conn->prepare($storeSalesQuery);
    $stmt->bind_param("i", $seller_id);
    $stmt->execute();
    $storeSalesResult = $stmt->get_result();
    ?>

    <h2>Overall Metrics</h2>
    <ul>
        <li><strong>Total Sales (Before Commission):</strong> RM <?= number_format($grossSales, 2) ?></li>
        <li><strong>Shipping Price: </strong> RM 9.00 </li>
        <li><strong>Net Earnings (After 5% Commission):</strong> RM <?= number_format($netSales, 2) ?></li>
        <li><strong>Total Completed Orders:</strong> <?= $salesData['completed_orders'] ?></li>
        <li><strong>Total Products Sold:</strong> <?= $salesData['total_items'] ?></li>
    </ul>

    <h2>Top 5 Best-Selling Products</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity Sold</th>
            <th>Total Revenue (RM)</th>
        </tr>
        <?php while ($row = $topProductsResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['total_sold'] ?></td>
                <td><?= number_format($row['total_revenue'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Sales by Store</h2>
    <table>
        <tr>
            <th>Store Name</th>
            <th>Total Sales (RM)</th>
        </tr>
        <?php while ($row = $storeSalesResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['storeName']) ?></td>
                <td><?= number_format($row['total_price'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>