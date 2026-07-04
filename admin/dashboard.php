<?php
session_start();
require_once '../includes/config/database.php';
require_once '../includes/config/constants.php';
require_once '../includes/functions/security.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/helpers.php';

// Require admin access
requireAdmin();

$db = Database::getInstance();

// Get admin statistics
$stats = getAdminStats();

// Get recent users
$recentUsers = getRecentUsers(5);

// Get pending approvals
$pendingPapers = getPendingPapers(5);

// Get system logs
$recentLogs = getRecentLogs(5);

// Get chart data
$chartData = getChartData();

// Include header
include '../includes/templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Admin Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar p-0">
                <div class="sidebar-sticky pt-3">
                    <div class="text-center mb-4 text-white">
                        <i class="fas fa-crown fa-3x"></i>
                        <h6 class="mt-2"><?= APP_NAME ?></h6>
                        <small>Admin Panel</small>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="/admin/dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="/admin/users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link" href="/admin/papers.php">
                            <i class="fas fa-file-alt me-2"></i>Papers
                        </a>
                        <a class="nav-link" href="/admin/courses.php">
                            <i class="fas fa-book me-2"></i>Courses
                        </a>
                        <a class="nav-link" href="/admin/faculties.php">
                            <i class="fas fa-university me-2"></i>Faculties
                        </a>
                        <a class="nav-link" href="/admin/departments.php">
                            <i class="fas fa-building me-2"></i>Departments
                        </a>
                        <a class="nav-link" href="/admin/reports.php">
                            <i class="fas fa-flag me-2"></i>Reports
                        </a>
                        <a class="nav-link" href="/admin/announcements.php">
                            <i class="fas fa-bullhorn me-2"></i>Announcements
                        </a>
                        <a class="nav-link" href="/admin/forum.php">
                            <i class="fas fa-comments me-2"></i>Forum
                        </a>
                        <a class="nav-link" href="/admin/settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                        <a class="nav-link" href="/admin/backups.php">
                            <i class="fas fa-database me-2"></i>Backups
                        </a>
                        <a class="nav-link" href="/admin/logs.php">
                            <i class="fas fa-clipboard-list me-2"></i>Logs
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
                <!-- Stats Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">Total Users</p>
                                    <h3><?= number_format($stats['total_users']) ?></h3>
                                </div>
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-users fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">Total Papers</p>
                                    <h3><?= number_format($stats['total_papers']) ?></h3>
                                </div>
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-file-alt fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">Pending Approvals</p>
                                    <h3><?= number_format($stats['pending_papers']) ?></h3>
                                </div>
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-clock fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="glass-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">Total Downloads</p>
                                    <h3><?= number_format($stats['total_downloads']) ?></h3>
                                </div>
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-download fa-2x text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row g-4 mb-4">
                    <div class="col-md-8">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">Paper Uploads & Downloads (Last 7 Days)</h5>
                            <canvas id="activityChart" height="200"></canvas>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">Top Courses</h5>
                            <canvas id="courseChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">
                                <i class="fas fa-clock me-2"></i>Recent Users
                            </h5>
                            <?php if (empty($recentUsers)): ?>
                                <p class="text-muted">No recent users</p>
                            <?php else: ?>
                                <?php foreach ($recentUsers as $user): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $user['profile_picture'] ?: '/assets/images/default-avatar.png' ?>" 
                                             alt="User" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                        <div>
                                            <p class="mb-0"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                            <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                        </div>
                                        <span class="ms-auto badge bg-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-3">
                                <i class="fas fa-hourglass-half me-2"></i>Pending Approvals
                            </h5>
                            <?php if (empty($pendingPapers)): ?>
                                <p class="text-muted">No pending approvals</p>
                            <?php else: ?>
                                <?php foreach ($pendingPapers as $paper): ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="me-2">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0"><?= htmlspecialchars($paper['title']) ?></p>
                                            <small class="text-muted"><?= htmlspecialchars($paper['course_code']) ?></small>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="/admin/approve.php?id=<?= $paper['id'] ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="/admin/reject.php?id=<?= $paper['id'] ?>" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- System Logs -->
                <?php if (!empty($recentLogs)): ?>
                    <div class="glass-card p-4 mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-clipboard-list me-2"></i>Recent System Logs
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentLogs as $log): ?>
                                        <tr>
                                            <td><?= date('H:i:s', strtotime($log['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                                            <td><span class="badge bg-info"><?= htmlspecialchars($log['action']) ?></span></td>
                                            <td><?= htmlspecialchars($log['description']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Activity Chart
            const ctx1 = document.getElementById('activityChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartData['labels']) ?>,
                    datasets: [
                        {
                            label: 'Uploads',
                            data: <?= json_encode($chartData['uploads']) ?>,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Downloads',
                            data: <?= json_encode($chartData['downloads']) ?>,
                            borderColor: '#48bb78',
                            backgroundColor: 'rgba(72, 187, 120, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Course Chart
            const ctx2 = document.getElementById('courseChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($chartData['course_labels']) ?>,
                    datasets: [{
                        data: <?= json_encode($chartData['course_data']) ?>,
                        backgroundColor: [
                            '#667eea', '#764ba2', '#48bb78', '#ed8936', '#fc8181'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    
    <?php include '../includes/templates/footer.php'; ?>
</body>
</html>
