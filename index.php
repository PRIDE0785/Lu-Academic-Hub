<?php
session_start();
require_once 'includes/config/database.php';
require_once 'includes/config/constants.php';
require_once 'includes/functions/security.php';
require_once 'includes/functions/auth.php';
require_once 'includes/functions/helpers.php';

$db = Database::getInstance();

// Get statistics
$stats = getStatistics();

// Get recent papers
$recentPapers = getRecentPapers(6);

// Get featured papers
$featuredPapers = getFeaturedPapers(4);

// Get faculties for search
$faculties = getFaculties();

// Get academic years for search
$academicYears = getAcademicYears();

// Include header
include 'includes/templates/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Lira University Academic Hub</title>
    <meta name="description" content="Access past papers, learning materials, and academic resources from Lira University">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dark-mode.css">
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content">
                    <div class="badge bg-light text-dark mb-3">
                        <i class="fas fa-graduation-cap me-2"></i> Lira University
                    </div>
                    <h1 class="display-3 fw-bold mb-4">
                        Your Academic Success <br>Starts Here
                    </h1>
                    <p class="lead mb-4">
                        Access thousands of past papers, lecture notes, and academic resources 
                        from Lira University's vast repository.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="/auth/register.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Get Started
                        </a>
                        <a href="#browse" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-search me-2"></i>Browse Papers
                        </a>
                    </div>
                    
                    <!-- Statistics -->
                    <div class="row g-3 mt-5">
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <span class="number"><?= number_format($stats['total_papers']) ?>+</span>
                                <span class="label">Past Papers</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <span class="number"><?= $stats['total_faculties'] ?></span>
                                <span class="label">Faculties</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <span class="number"><?= $stats['total_courses'] ?></span>
                                <span class="label">Courses</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <span class="number"><?= number_format($stats['total_users']) ?></span>
                                <span class="label">Students</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="hero-illustration">
                        <img src="/assets/images/undraw_education.svg" alt="Education Illustration" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Search Section -->
    <section class="py-5" id="browse">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="glass-card p-4 p-md-5">
                        <h3 class="text-center mb-4">
                            <i class="fas fa-search text-primary me-2"></i>
                            Quick Search
                        </h3>
                        <form action="/search.php" method="GET">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" name="q" class="form-control form-control-lg" 
                                           placeholder="Search by title, course code, or keywords...">
                                </div>
                                <div class="col-md-3">
                                    <select name="faculty" class="form-select form-select-lg">
                                        <option value="">All Faculties</option>
                                        <?php foreach ($faculties as $faculty): ?>
                                            <option value="<?= $faculty['id'] ?>"><?= htmlspecialchars($faculty['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="year" class="form-select form-select-lg">
                                        <option value="">All Years</option>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-search me-2"></i>Search Papers
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Papers Section -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Latest Uploaded Papers
                </h2>
                <a href="/papers.php" class="btn btn-outline-primary">View All</a>
            </div>
            
            <div class="row g-4">
                <?php foreach ($recentPapers as $paper): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="paper-card">
                            <div class="card-image">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($paper['title']) ?></h5>
                                <div class="card-meta">
                                    <span class="badge bg-primary"><?= htmlspecialchars($paper['course_code']) ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($paper['faculty_name']) ?></span>
                                </div>
                                <div class="card-meta mt-2">
                                    <i class="far fa-calendar-alt"></i> <?= htmlspecialchars($paper['academic_year']) ?>
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-download"></i> <?= number_format($paper['downloads']) ?>
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-eye"></i> <?= number_format($paper['views']) ?>
                                </div>
                                <div class="card-actions">
                                    <a href="/paper/view.php?id=<?= $paper['id'] ?>" class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="/api/download.php?id=<?= $paper['id'] ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="bookmark(<?= $paper['id'] ?>)">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-cloud-upload-alt fa-2x"></i>
                        </div>
                        <h5>Easy Upload</h5>
                        <p class="text-muted">Upload your past papers and learning materials with ease</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="icon-circle bg-success text-white mx-auto mb-3">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                        <h5>Smart Search</h5>
                        <p class="text-muted">Find exactly what you need with advanced search filters</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="icon-circle bg-info text-white mx-auto mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5>Community</h5>
                        <p class="text-muted">Connect with fellow students and share knowledge</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
    
    <script>
        function bookmark(paperId) {
            fetch('/api/bookmark.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ paper_id: paperId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Bookmark added successfully!', 'success');
                    const btn = document.querySelector(`button[onclick="bookmark(${paperId})"] i`);
                    if (btn) {
                        btn.className = 'fas fa-bookmark';
                        btn.closest('button').className = 'btn btn-primary btn-sm';
                    }
                } else {
                    showToast(data.message || 'Failed to bookmark', 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred', 'error');
            });
        }
    </script>
</body>
</html>
