<?php

/**
 * Return list of users.
 */
function get_users($conn)
{
    $query = "SELECT DISTINCT u.id, u.name
    FROM users u
    JOIN user_accounts ua ON u.id = ua.user_id
    JOIN transactions t ON t.account_from = ua.id OR t.account_to = ua.id
    ORDER BY u.name;";

    try {
        $stmt = $conn->query($query);
        $users = [];
        while ($row = $stmt->fetch()) {
            $users[$row['id']] = $row['name'];
        }

        return $users;
    } catch (PDOException $e) {
        return "Ошибка базы данных: " . $e->getMessage();
    }
}


/**
 * Return transactions balances of given user.
 */
function get_user_transactions_balances($user_id, $conn)
{
    $query = "SELECT 
      strftime('%Y-%m', t.trdate) AS month, 
      SUM(CASE WHEN ua_to.user_id = :user_id THEN t.amount ELSE 0 END) AS incoming,
      SUM(CASE WHEN ua_from.user_id = :user_id THEN t.amount ELSE 0 END) AS outgoing,
      SUM(CASE WHEN ua_to.user_id = :user_id THEN t.amount ELSE 0 END) -
      SUM(CASE WHEN ua_from.user_id = :user_id THEN t.amount ELSE 0 END) AS balance
        FROM transactions t
        LEFT JOIN user_accounts ua_from ON t.account_from = ua_from.id
        LEFT JOIN user_accounts ua_to ON t.account_to = ua_to.id
        WHERE ua_from.user_id = :user_id OR ua_to.user_id = :user_id
        GROUP BY strftime('%Y-%m', t.trdate)
        ORDER BY month;
    ";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {    
        return "Ошибка базы данных: " . $e->getMessage();
    }
}