<?php
require_once 'config.php';

$message = '';
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'logged_out':
            $message = '<div class="alert alert-info">You have been successfully logged out.</div>';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduLink - AI-Powered Career Guidance Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">EduLink</a>
                <ul class="nav-links">
                    <?php if (isLoggedIn()): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="results.php">My Results</a></li>
                        <li><a href="logout.php">Logout</a></li>
                        <li><span style="color: #667eea;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <?php echo $message; ?>
            
            <!-- Hero Section -->
            <div class="card" style="text-align: center; margin: 3rem 0;">
                <div class="card-header">
                    <h1 class="card-title" style="font-size: 3rem; margin-bottom: 1rem;">
                        Discover Your Perfect Career Path
                    </h1>
                    <p class="card-subtitle" style="font-size: 1.3rem; margin-bottom: 2rem;">
                        AI-powered career guidance tailored for Malaysian students and professionals
                    </p>
                </div>
                
                <?php if (!isLoggedIn()): ?>
                    <div style="margin: 2rem 0;">
                        <a href="register.php" class="btn btn-primary" style="margin: 0 1rem;">Get Started</a>
                        <a href="login.php" class="btn btn-secondary" style="margin: 0 1rem;">Login</a>
                    </div>
                <?php else: ?>
                    <div style="margin: 2rem 0;">
                        <a href="dashboard.php" class="btn btn-primary" style="margin: 0 1rem;">Go to Dashboard</a>
                        <a href="results.php" class="btn btn-secondary" style="margin: 0 1rem;">View My Results</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Features Section -->
            <div class="row" style="margin: 3rem 0;">
                <div class="col-md-4">
                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 1.5rem;">🎯</span>
                            </div>
                            <h3>Personalized Recommendations</h3>
                        </div>
                        <p style="text-align: center; color: #666;">
                            Get tailored career and course recommendations based on your academic performance, interests, and personality traits.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 1.5rem;">🤖</span>
                            </div>
                            <h3>AI-Powered Analysis</h3>
                        </div>
                        <p style="text-align: center; color: #666;">
                            Advanced AI technology analyzes your profile and matches you with the most suitable career paths in the Malaysian job market.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 1.5rem;">📊</span>
                            </div>
                            <h3>Comprehensive Reports</h3>
                        </div>
                        <p style="text-align: center; color: #666;">
                            Receive detailed reports with career insights, required skills, and educational pathways. Export as PDF for future reference.
                        </p>
                    </div>
                </div>
            </div>

            <!-- How It Works Section -->
            <div class="card" style="margin: 3rem 0;">
                <div class="card-header">
                    <h2 class="card-title">How EduLink Works</h2>
                    <p class="card-subtitle">Simple steps to discover your ideal career path</p>
                </div>
                
                <div class="row">
                    <div class="col-md-4" style="text-align: center;">
                        <div style="font-size: 2rem; color: #667eea; margin-bottom: 1rem;">1</div>
                        <h4>Create Your Profile</h4>
                        <p style="color: #666;">Register and input your academic results, interests, and complete a personality assessment.</p>
                    </div>
                    
                    <div class="col-md-4" style="text-align: center;">
                        <div style="font-size: 2rem; color: #667eea; margin-bottom: 1rem;">2</div>
                        <h4>AI Analysis</h4>
                        <p style="color: #666;">Our AI analyzes your profile against Malaysian job market trends and career requirements.</p>
                    </div>
                    
                    <div class="col-md-4" style="text-align: center;">
                        <div style="font-size: 2rem; color: #667eea; margin-bottom: 1rem;">3</div>
                        <h4>Get Recommendations</h4>
                        <p style="color: #666;">Receive personalized career and course recommendations with detailed insights and next steps.</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <?php if (!isLoggedIn()): ?>
            <div class="card" style="text-align: center; margin: 3rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h2 style="color: white; margin-bottom: 1rem;">Ready to Discover Your Future?</h2>
                <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">
                    Join thousands of students who have found their perfect career path with EduLink.
                </p>
                <a href="register.php" class="btn" style="background: white; color: #667eea; margin: 0 1rem;">Start Your Journey</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 EduLink. AI-Powered Career Guidance Platform for Malaysian Students.</p>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>