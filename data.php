<?php
include_once('db.php');
include_once('model.php');

if (isset($_GET['user'])) {
    $user_id = (int)$_GET['user'];
    $conn = get_connect();

    $transactions = get_user_transactions_balances($user_id, $conn);
    
    $monthNames = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];

    foreach ($transactions as &$transaction) {
        $month = substr($transaction['month'], 5, 2);
        $transaction['month'] = $monthNames[$month] ?? 'Unknown';
    }
    header('Content-Type: application/json');
    echo json_encode($transactions);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid request']);
