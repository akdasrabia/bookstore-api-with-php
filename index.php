<?php
require 'config/database.php';


function validateApiKey($apiKey)
{
    $validApiKey = 'bookstore12*'; 
    return $apiKey === $validApiKey;
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

$headers = getallheaders();
if (!isset($headers['X-API-Key']) || !validateApiKey($headers['X-API-Key'])) {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['error' => 'Invalid API key']);
    exit();
}


$pdo = getDbConnection();
switch ($method) {
    case 'GET':
        if (isset($request[1]) && $request[1] === 'books') {
            header('Content-Type: application/json');

            if (isset($request[2])) {
                $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
                $stmt->execute([$request[2]]);
                $book = $stmt->fetch();
                if ($book) {
                    echo json_encode($book);
                } else {
                    header('HTTP/1.0 404 Not Found');
                    echo json_encode(['error' => 'Book not found']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM books");
                $books = $stmt->fetchAll();
                echo json_encode($books);
            }
        }
        break;

    case 'POST':
        if ($request[1] === 'books') {
            header('Content-Type: application/json');

            try {
                $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, price) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$input['title'], $input['author'], $input['isbn'], $input['price']])) {
                    echo json_encode(['success' => 'Book added successfully']);
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo json_encode(['error' => 'Failed to add book']);
                }
            } catch (PDOException $e) {
                header('HTTP/1.0 400 Bad Request');
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        }
        break;

        case 'PUT':
            if ($request[1] === 'books' && isset($request[2])) {
                header('Content-Type: application/json');
                $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
                $stmt->execute([$request[2]]);
                $book = $stmt->fetch();
        
                if ($book) {
                    try {
                        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, price = ? WHERE id = ?");
                        if ($stmt->execute([$input['title'], $input['author'], $input['isbn'], $input['price'], $request[2]])) {
                            echo json_encode(['success' => 'Book updated successfully']);
                        } else {
                            header('HTTP/1.0 400 Bad Request');
                            echo json_encode(['error' => 'Failed to update book']);
                        }
                    } catch (PDOException $e) {
                        header('HTTP/1.0 400 Bad Request');
                        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    }
                } else {
                    header('HTTP/1.0 404 Not Found');
                    echo json_encode(['error' => 'Book not found']);
                }
            }
            break;
            case 'DELETE':
                if ($request[1] === 'books' && isset($request[2])) {
                    header('Content-Type: application/json');
                    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
                    $stmt->execute([$request[2]]);
                    $book = $stmt->fetch();
            
                    if ($book) {
                        try {
                            $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
                            if ($stmt->execute([$request[2]])) {
                                echo json_encode(['success' => 'Book deleted successfully']);
                            } else {
                                header('HTTP/1.0 400 Bad Request');
                                echo json_encode(['error' => 'Failed to delete book']);
                            }
                        } catch (PDOException $e) {
                            header('HTTP/1.0 400 Bad Request');
                            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                        }
                    } else {
                        header('HTTP/1.0 404 Not Found');
                        echo json_encode(['error' => 'Book not found']);
                    }
                }
                break;
    default:
        header('HTTP/1.0 405 Method Not Allowed');
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
