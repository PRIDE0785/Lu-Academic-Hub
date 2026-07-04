<?php
/**
 * Header Template
 * Navigation bar with dark mode toggle and user menu
 */
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCSRFToken() ?>">
    <title><?= APP_NAME ?></title>
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-graduation-cap me-2"></i>
                <span class="fw-bold">LU Academic Hub</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Navigation -->
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="/">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="papersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-alt me-1"></i>Papers
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="papersDropdown">
                            <li><a class="dropdown-item" href="/papers.php">All Papers</a></li>
                            <li><a class="dropdown-item" href="/papers.php?newest=1">Latest Uploads</a></li>
                            <li><a class="dropdown-item" href="/papers.php?popular=1">Popular Downloads</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/papers/upload.php"><i class="fas fa-upload text-success me-2"></i>Upload Paper</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/courses/">
                            <i class="fas fa-book me-1"></i>Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/forum/">
                            <i class="fas fa-comments me-1"></i>Forum
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/research/">
                            <i class="fas fa-flask me-1"></i>Research
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/announcements/">
                            <i class="fas fa-bullhorn me-1"></i>Announcements
                        </a>
                    </li>
                </ul>
                
                <!-- Right side - Search, Theme Toggle, User Menu -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Search -->
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-input" placeholder="Search papers..." aria-label="Search">
                        <div class="search-results dropdown-menu w-100 d-none"></div>
                    </div>
                    
                    <!-- Dark Mode Toggle -->
                    <button class="btn btn-ghost rounded-circle" id="darkModeToggle" aria-label="Toggle dark mode">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- User Menu -->
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary rounded-circle p-2" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="/profile.php">
                                        <i class="fas fa-user me-2"></i>Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/dashboard.php">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/bookmarks.php">
                                        <i class="fas fa-bookmark me-2"></i>Bookmarks
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="/auth/logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="d-flex gap-2">
                            <a href="/auth/login.php" class="btn btn-outline-primary">Login</a>
                            <a href="/auth/register.php" class="btn btn-primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Toast Container -->
    <div class="toast-container"></div>
