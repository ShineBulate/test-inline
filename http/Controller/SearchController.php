<?php
header('Content-Type: application/json');

$host = '127.0.0.1:3308';
$db   = 'anidubtrac_test'; 
$user = 'anidubtrac_test'; 
$pass = '12345678Aa';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения к базе данных: ' . $e->getMessage()]);
    exit;
}

if (isset($_GET['query']) && strlen($_GET['query']) >= 3) {
    $query = $_GET['query'];
    
    $sql = "SELECT comments.body AS comment_body, posts.title AS post_title 
            FROM comments 
            JOIN posts ON comments.postId = posts.id 
            WHERE comments.body LIKE :query";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['query' => "%$query%"]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} else {
    echo json_encode(['error' => 'Введите минимум 3 символа для поиска.']);
}
?>