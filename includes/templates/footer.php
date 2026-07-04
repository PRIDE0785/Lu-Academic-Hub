<?php
/**
 * Footer Template
 * Contains footer content, social links, and copyright
 */
?>
<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-primary">
                    <i class="fas fa-graduation-cap me-2"></i><?= APP_NAME ?>
                </h5>
                <p class="text-muted">
                    Lira University's premier academic resource hub. 
                    Access past papers, lecture notes, and learning materials 
                    to excel in your studies.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="me-2" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-2" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-2" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-2" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/papers.php">Past Papers</a></li>
                    <li><a href="/courses/">Courses</a></li>
                    <li><a href="/forum/">Forum</a></li>
                    <li><a href="/research/">Research</a></li>
                </ul>
            </div>
            
            <!-- Resources -->
            <div class="col-lg-2 col-md-6">
                <h5>Resources</h5>
                <ul>
                    <li><a href="/about.php">About Us</a></li>
                    <li><a href="/contact.php">Contact</a></li>
                    <li><a href="/faq.php">FAQ</a></li>
                    <li><a href="/privacy.php">Privacy Policy</a></li>
                    <li><a href="/terms.php">Terms of Use</a></li>
                </ul>
            </div>
            
            <!-- Newsletter -->
            <div class="col-lg-4 col-md-6">
                <h5>Stay Updated</h5>
                <p class="text-muted">Subscribe to our newsletter for updates on new materials</p>
                <form action="/subscribe.php" method="POST" class="mt-2">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Your email" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Copyright -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="text-muted mb-0">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="text-muted mb-0">
                    <i class="fas fa-code me-1"></i> Built with <i class="fas fa-heart text-danger mx-1"></i> for Lira University
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js for analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- QRCode.js for QR code generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Custom JavaScript -->
<script src="/assets/js/main.js"></script>
<script src="/assets/js/auth.js"></script>

<?php if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) == 'dashboard.php'): ?>
    <script src="/assets/js/dashboard.js"></script>
<?php endif; ?>

<!-- Initialize CSRF token -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        if (csrfToken) {
            window.csrfToken = csrfToken;
        }
    });
</script>
</body>
</html>
