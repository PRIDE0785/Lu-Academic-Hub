<?php
/**
 * Helper Functions
 * Utility functions for the application
 */

/**
 * Get statistics for homepage
 */
function getStatistics() {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM past_papers WHERE status = 'approved') as total_papers,
                    (SELECT COUNT(*) FROM faculties WHERE is_active = 1) as total_faculties,
                    (SELECT COUNT(*) FROM courses WHERE is_active = 1) as total_courses,
                    (SELECT COUNT(*) FROM users WHERE is_active = 1 AND role_id = 4) as total_users,
                    (SELECT COUNT(*) FROM learning_materials) as total_materials";
        
        $stmt = $db->query($sql);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting statistics: " . $e->getMessage());
        return [
            'total_papers' => 0,
            'total_faculties' => 0,
            'total_courses' => 0,
            'total_users' => 0,
            'total_materials' => 0
        ];
    }
}

/**
 * Get recent papers
 */
function getRecentPapers($limit = 6) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT p.*, c.course_code, f.name as faculty_name 
                FROM past_papers p
                JOIN courses c ON p.course_id = c.id
                JOIN faculties f ON p.faculty_id = f.id
                WHERE p.status = 'approved'
                ORDER BY p.created_at DESC
                LIMIT ?";
        $stmt = $db->query($sql, [$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting recent papers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get featured papers
 */
function getFeaturedPapers($limit = 4) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT p.*, c.course_code, f.name as faculty_name 
                FROM past_papers p
                JOIN courses c ON p.course_id = c.id
                JOIN faculties f ON p.faculty_id = f.id
                WHERE p.status = 'approved' AND p.is_featured = 1
                ORDER BY p.created_at DESC
                LIMIT ?";
        $stmt = $db->query($sql, [$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting featured papers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get faculties list
 */
function getFaculties() {
    try {
        $db = Database::getInstance();
        $sql = "SELECT id, name FROM faculties WHERE is_active = 1 ORDER BY name";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting faculties: " . $e->getMessage());
        return [];
    }
}

/**
 * Get academic years
 */
function getAcademicYears() {
    $currentYear = date('Y');
    $years = [];
    for ($i = 0; $i < 5; $i++) {
        $year = $currentYear - $i;
        $years[] = $year . '/' . ($year + 1);
    }
    return $years;
}

/**
 * Get student statistics
 */
function getStudentStats($userId) {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM past_papers WHERE uploader_id = ?) as total_uploads,
                    (SELECT COUNT(*) FROM downloads WHERE user_id = ?) as total_downloads,
                    (SELECT COUNT(*) FROM bookmarks WHERE user_id = ?) as total_bookmarks,
                    (SELECT COUNT(*) FROM comments WHERE user_id = ?) as total_comments";
        
        $stmt = $db->query($sql, [$userId, $userId, $userId, $userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting student stats: " . $e->getMessage());
        return [
            'total_uploads' => 0,
            'total_downloads' => 0,
            'total_bookmarks' => 0,
            'total_comments' => 0
        ];
    }
}

/**
 * Get recent activity
 */
function getRecentActivity($userId, $limit = 10) {
    try {
        $db = Database::getInstance();
        
        // Combine activities from multiple tables
        $sql = "SELECT 'upload' as type, 'uploaded a paper' as description, created_at 
                FROM past_papers WHERE uploader_id = ?
                UNION
                SELECT 'download' as type, 'downloaded a paper' as description, created_at 
                FROM downloads WHERE user_id = ?
                UNION
                SELECT 'bookmark' as type, 'bookmarked a paper' as description, created_at 
                FROM bookmarks WHERE user_id = ?
                UNION
                SELECT 'comment' as type, 'commented on a paper' as description, created_at 
                FROM comments WHERE user_id = ?
                ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $db->query($sql, [$userId, $userId, $userId, $userId, $limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting recent activity: " . $e->getMessage());
        return [];
    }
}

/**
 * Get notifications
 */
function getNotifications($userId, $limit = 5) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT * FROM notifications WHERE user_id = ? 
                ORDER BY created_at DESC LIMIT ?";
        $stmt = $db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting notifications: " . $e->getMessage());
        return [];
    }
}

/**
 * Get unread notification count
 */
function getUnreadNotificationCount($userId) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM notifications 
                WHERE user_id = ? AND is_read = 0";
        $stmt = $db->query($sql, [$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Error getting unread count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get bookmarks
 */
function getBookmarks($userId, $limit = 4) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT p.*, c.course_code 
                FROM bookmarks b
                JOIN past_papers p ON b.paper_id = p.id
                JOIN courses c ON p.course_id = c.id
                WHERE b.user_id = ? AND p.status = 'approved'
                ORDER BY b.created_at DESC LIMIT ?";
        $stmt = $db->query($sql, [$userId, $limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting bookmarks: " . $e->getMessage());
        return [];
    }
}

/**
 * Get admin statistics
 */
function getAdminStats() {
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
                    (SELECT COUNT(*) FROM past_papers WHERE status = 'approved') as total_papers,
                    (SELECT COUNT(*) FROM past_papers WHERE status = 'pending') as pending_papers,
                    (SELECT COUNT(*) FROM downloads) as total_downloads,
                    (SELECT COUNT(*) FROM forum_discussions) as total_discussions,
                    (SELECT COUNT(*) FROM reports WHERE status = 'pending') as pending_reports";
        
        $stmt = $db->query($sql);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error getting admin stats: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_papers' => 0,
            'pending_papers' => 0,
            'total_downloads' => 0,
            'total_discussions' => 0,
            'pending_reports' => 0
        ];
    }
}

/**
 * Get recent users
 */
function getRecentUsers($limit = 5) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT ?";
        $stmt = $db->query($sql, [$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting recent users: " . $e->getMessage());
        return [];
    }
}

/**
 * Get pending papers
 */
function getPendingPapers($limit = 5) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT p.*, c.course_code 
                FROM past_papers p
                JOIN courses c ON p.course_id = c.id
                WHERE p.status = 'pending'
                ORDER BY p.created_at ASC LIMIT ?";
        $stmt = $db->query($sql, [$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting pending papers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent logs
 */
function getRecentLogs($limit = 5) {
    try {
        $db = Database::getInstance();
        $sql = "SELECT l.*, u.username 
                FROM audit_logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC LIMIT ?";
        $stmt = $db->query($sql, [$limit]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Error getting recent logs: " . $e->getMessage());
        return [];
    }
}

/**
 * Get chart data
 */
function getChartData() {
    try {
        $db = Database::getInstance();
        
        // Get last 7 days
        $labels = [];
        $uploads = [];
        $downloads = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('M d', strtotime($date));
            
            // Uploads
            $sql = "SELECT COUNT(*) as count FROM past_papers 
                    WHERE DATE(created_at) = ? AND status = 'approved'";
            $stmt = $db->query($sql, [$date]);
            $result = $stmt->fetch();
            $uploads[] = $result['count'] ?? 0;
            
            // Downloads
            $sql = "SELECT COUNT(*) as count FROM downloads WHERE DATE(created_at) = ?";
            $stmt = $db->query($sql, [$date]);
            $result = $stmt->fetch();
            $downloads[] = $result['count'] ?? 0;
        }
        
        // Get top courses
        $courseLabels = [];
        $courseData = [];
        $sql = "SELECT c.course_code, COUNT(p.id) as count 
                FROM past_papers p
                JOIN courses c ON p.course_id = c.id
                WHERE p.status = 'approved'
                GROUP BY p.course_id
                ORDER BY count DESC LIMIT 5";
        $stmt = $db->query($sql);
        $courses = $stmt->fetchAll();
        
        foreach ($courses as $course) {
            $courseLabels[] = $course['course_code'];
            $courseData[] = $course['count'];
        }
        
        return [
            'labels' => $labels,
            'uploads' => $uploads,
            'downloads' => $downloads,
            'course_labels' => $courseLabels,
            'course_data' => $courseData
        ];
    } catch (Exception $e) {
        error_log("Error getting chart data: " . $e->getMessage());
        return [
            'labels' => [],
            'uploads' => [],
            'downloads' => [],
            'course_labels' => [],
            'course_data' => []
        ];
    }
}

/**
 * Time ago function
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $period => $seconds) {
        if ($difference >= $seconds) {
            $count = floor($difference / $seconds);
            return $count . ' ' . $period . ($count > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'Just now';
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Generate slug
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Get user role badge
 */
function getRoleBadge($role) {
    $badges = [
        'super_admin' => '<span class="badge bg-danger">Super Admin</span>',
        'admin' => '<span class="badge bg-primary">Admin</span>',
        'lecturer' => '<span class="badge bg-success">Lecturer</span>',
        'student' => '<span class="badge bg-info">Student</span>'
    ];
    return $badges[$role] ?? '<span class="badge bg-secondary">User</span>';
}

/**
 * Send email using PHP mail
 */
function sendEmail($to, $subject, $message, $from = null) {
    $from = $from ?? APP_EMAIL;
    $headers = "From: " . APP_NAME . " <" . $from . ">\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}
