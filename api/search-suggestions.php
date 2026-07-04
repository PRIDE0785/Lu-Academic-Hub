<?php
/**
 * Search Suggestions API
 * Returns autocomplete suggestions for search
 */

session_start();
require_once '../includes/config/database.php';
require_once '../includes/config/constants.php';
require_once '../includes/functions/security.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = Database::getInstance();
    
    $sql = "SELECT p.id, p.title, c.course_code, p.downloads 
            FROM past_papers p
            JOIN courses c ON p.course_id = c.id
            WHERE p.status = 'approved' 
            AND (p.title LIKE ? OR p.keywords LIKE ? OR p.tags LIKE ? OR c.course_code LIKE ?)
            LIMIT 10";
    
    $searchTerm = '%' . $query . '%';
    $stmt = $db->query($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
    
} catch (Exception $e) {
    error_log("Search API error: " . $e->getMessage());
    echo json_encode([]);
}
