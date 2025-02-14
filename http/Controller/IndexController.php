<?php
class APIDataLoader {
    private $connection;

    public function __construct($servername, $username, $password, $dbname) {
        $this->connection = new mysqli($servername, $username, $password, $dbname);

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
        $this->executeSchema('../../base/schema.sql');

    }
    private function executeSchema($schemaFile) {
        if (!file_exists($schemaFile)) {
            echo ("Файл схемы не найден: " . $schemaFile)."<br>";

            echo "Текущий путь: " . realpath(dirname(__FILE__)) . "\n";
            return;

        }

        $schema = file_get_contents($schemaFile);

    
        if (mysqli_multi_query($this->connection, $schema)) {
            while (mysqli_next_result($this->connection));
        } else {
            echo "Ошибка при выполнении схемы: " . $this->connection->error . "\n";
            return false;
        }
    }
    public function loadPosts($posts_api_url) {
        $posts_response = file_get_contents($posts_api_url);
        $posts_data = json_decode($posts_response, true);

        $loaded_posts = 0;

        if (is_array($posts_data)) {
            foreach ($posts_data as $post) {
                $stmt = $this->connection->prepare("INSERT INTO posts (userID, id, title, body) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $post['userId'], $post['id'], $post['title'], $post['body']);
                
                if ($stmt->execute()) {
                    $loaded_posts++;
                }
            }
        } else {
            echo "Ошибка: данные не получены из API для постов.\n";
        }

        return $loaded_posts;
    }

    public function loadComments($comments_api_url) {
        $comments_response = file_get_contents($comments_api_url);
        $comments_data = json_decode($comments_response, true);

        $loaded_comments = 0;

        if (is_array($comments_data)) {
            foreach ($comments_data as $comment) {
                $stmt = $this->connection->prepare("INSERT INTO comments (postId, id, `name`, email, body) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $comment['postId'], $comment['id'], $comment['name'], $comment['email'], $comment['body']);
                
                if ($stmt->execute()) {
                    $loaded_comments++;
                }
            }
        } else {
            echo "Ошибка: данные не получены из API для комментариев.\n";
        }

        return $loaded_comments;
    }

    public function __destruct() {
        $this->connection->close();
    }
}

$apiLoader = new APIDataLoader("127.0.0.1:3308", "anidubtrac_test", "12345678Aa", "anidubtrac_test");

$posts_api_url = "https://jsonplaceholder.typicode.com/posts";
$comments_api_url = "https://jsonplaceholder.typicode.com/comments";

$loadedPosts = $apiLoader->loadPosts($posts_api_url);
$loadedComments = $apiLoader->loadComments($comments_api_url);

echo "Загружено $loadedPosts записей и $loadedComments комментариев\n";
?>