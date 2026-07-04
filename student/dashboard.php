<?php
session_start();
require_once '../includes/config/database.php';
require_once '../includes/config/constants.php';
require_once '../includes/functions/security.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/helpers.php';

// Require authentication
requireAuth();

// Get current user
$user = getCurrentUser();
if (!$user) {
    header('Location: /auth/login.php');
    exit;
}

// Check if user is student (or admin)
if (!hasAnyRole(['student', 'admin', 'super_admin'])) {
    header('Location: /unauthorized.php');
    exit;
}

$db = Database::getInstance();

// Get student statistics
$stats = getStudentStats($user['id']);

// Get recent activity
$recentActivity = getRecentActivity($user['id']);

// Get notifications
$notifications = getNotifications($user['id'], 5);
$unreadCount = getUnreadNotificationCount($user['id']);

// Get bookmarks
$bookmarks = getBookmarks($user['id'], 4);

// Include header
include '../includes/templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar p-0">
                <div class="sidebar-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="<?= $user['profile_picture'] ?: '/assets/images/default-avatar.png' ?>" 
                             alt="Profile" class="rounded-circle img-fluid" style="width: 80px; height: 80px; object-fit: cover;">
                        <h6 class="mt-2 mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                        <small class="text-muted"><?= ucfirst($user['role_name'] ?? 'Student') ?></small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="/student/dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="/student/upload.php">
                            <i class="fas fa-upload me-2"></i>Upload Paper
                        </a>
                        <a class="nav-link" href="/student/uploads.php">
                            <i class="fas fa-file-alt me-2"></i>My Uploads
                        </a>
                        <a class="nav-link" href="/student/downloads.php">
                            <i class="fas fa-download me-2"></i>Downloads
                        </a>
                        <a class="nav-link" href="/student/bookmarks.php">
                            <i class="fas fa-bookmark me-2"></i>Bookmarks
                        </a>
                        <a class="nav-link" href="/student/favorites.php">
                            <i class="fas fa-star me-2"></i>Favorites
                        </a>
                        <a class="nav-link" href="/student/notifications.php">
                            <i class="fas fa-bell me-2"></i>Notifications
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger ms-2"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="/student/achievements.php">
                            <i class="fas fa-trophy me-2"></i>Achievements
                        </a>
                        <a class="nav-link" href="/student/profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="nav-link" href="/student/settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <hr>
                        <a class="nav-link text-danger" href="/auth/logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Welcome Banner -->
                <div class="glass-card p-4 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4>Welcome back, <?= htmlspecialchars($user['first_name']) ?>! 👋</h4>
                            <p class="text-muted">Here's what's happening with your academic journey.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="/student/upload.php" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Paper
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3 text-center">
                            <h3 class="text-primary"><?= $stats['total_uploads'] ?></h3>
                            <p class="text-muted mb-0">Papers Uploaded</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3 text-center">
                            <h3 class="text-success"><?= $stats['total_downloads'] ?></h3>
                            <p class="text-muted mb-0">Papers Downloaded</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3 text-center">
                            <h3 class="text-info"><?= $stats['total_bookmarks'] ?></h3>
                            <p class="text-muted mb-0">Bookmarks</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3 text-center">
                            <h3 class="text-warning"><?= $stats['total_comments'] ?></h3>
                            <p class="text-muted mb-0">Comments</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    <!-- Recent Activity -->
                    <div class="col-md-8">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">
                                <i class="fas fa-clock me-2"></i>Recent Activity
                            </h5>
                            <?php if (empty($recentActivity)): ?>
                                <p class="text-muted">No recent activity</p>
                            <?php else: ?>
                                <div class="activity-timeline">
                                    <?php foreach ($recentActivity as $activity): ?>
                                        <div class="activity-item d-flex mb-3">
                                            <div class="activity-icon me-3">
                                                <i class="fas fa-<?= $activity['icon'] ?? 'circle' ?> text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0"><?= htmlspecialchars($activity['description']) ?></p>
                                                <small class="text-muted"><?= timeAgo($activity['created_at']) ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Notifications -->
                    <div class="col-md-4">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">
                                <i class="fas fa-bell me-2"></i>Notifications
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge bg-danger ms-2"><?= $unreadCount ?> new</span>
                                <?php endif; ?>
                            </h5>
                            <?php if (empty($notifications)): ?>
                                <p class="text-muted">No notifications</p>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="notification-item mb-3 <?= $notification['is_read'] ? '' : 'unread' ?>">
                                        <div class="d-flex justify-content-between">
                                            <strong><?= htmlspecialchars($notification['title']) ?></strong>
                                            <small class="text-muted"><?= timeAgo($notification['created_at']) ?></small>
                                        </div>
                                        <p class="mb-0 small text-muted"><?= htmlspecialchars($notification['message']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                                <a href="/student/notifications.php" class="btn btn-outline-primary btn-sm w-100">View All</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Bookmarks -->
                <?php if (!empty($bookmarks)): ?>
                    <div class="glass-card p-4 mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-bookmark me-2"></i>Recent Bookmarks
                        </h5>
                        <div class="row g-3">
                            <?php foreach ($bookmarks as $bookmark): ?>
                                <div class="col-md-3 col-sm-6">
                                    <div class="paper-card-mini p-3">
                                        <h6><?= htmlspecialchars($bookmark['title']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($bookmark['course_code']) ?></small>
                                        <div class="mt-2">
                                            <a href="/paper/view.php?id=<?= $bookmark['id'] ?>" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Initialize charts if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh notifications
            setInterval(function() {
                fetch('/api/notifications.php?unread=true')
                    .then(response => response.json())
                    .then(data => {
                        if (data.unread > 0) {
                            const badge = document.querySelector('.badge.bg-danger');
                            if (badge) {
                                badge.textContent = data.unread;
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }, 30000); // Every 30 seconds
        });
    </script>

    <?php include '../includes/templates/footer.php'; ?>
</body>
</html>
