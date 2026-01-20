<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Get existing profile data
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$profile_result = $stmt->get_result();
$existing_profile = $profile_result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduLink</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">EduLink</a>
                <ul class="nav-links">
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="results.php">My Results</a></li>
                    <li><a href="logout.php">Logout</a></li>
                    <li><span style="color: #667eea;">Welcome, <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>!</span></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h1 class="card-title">Career Guidance Profile</h1>
                    <p class="card-subtitle">Complete your profile to receive personalized career recommendations</p>
                </div>

                <!-- Progress Bar (hidden by default) -->
                <div id="progress-container" style="display: none;">
                    <h3>Processing Your Profile...</h3>
                    <div class="progress">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <p style="text-align: center; color: #666;">Our AI is analyzing your profile and generating recommendations...</p>
                </div>

                <form id="profile-form" method="POST" action="process.php">
                    <!-- Education Level -->
                    <div class="form-group">
                        <label for="education_level" class="form-label">Current Education Level</label>
                        <select id="education_level" name="education_level" class="form-control" required>
                            <option value="">Select your education level</option>
                            <option value="SPM" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'SPM') ? 'selected' : ''; ?>>SPM (Form 5)</option>
                            <option value="STPM" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'STPM') ? 'selected' : ''; ?>>STPM (Form 6)</option>
                            <option value="Foundation" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'Foundation') ? 'selected' : ''; ?>>Foundation</option>
                            <option value="Diploma" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                            <option value="Degree" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'Degree') ? 'selected' : ''; ?>>Bachelor's Degree</option>
                            <option value="Masters" <?php echo (isset($existing_profile['education_level']) && $existing_profile['education_level'] == 'Masters') ? 'selected' : ''; ?>>Master's Degree</option>
                        </select>
                    </div>

                    <!-- Academic Scores -->
                    <div class="card" style="margin: 2rem 0; background: #f8f9fa;">
                        <h3>Academic Performance</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">Enter your grades/scores for relevant subjects</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="math_score" class="form-label">Mathematics</label>
                                    <select id="math_score" name="math_score" class="form-control">
                                        <option value="">Select grade</option>
                                        <option value="A+">A+</option>
                                        <option value="A">A</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B">B</option>
                                        <option value="C+">C+</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="science_score" class="form-label">Science</label>
                                    <select id="science_score" name="science_score" class="form-control">
                                        <option value="">Select grade</option>
                                        <option value="A+">A+</option>
                                        <option value="A">A</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B">B</option>
                                        <option value="C+">C+</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="english_score" class="form-label">English</label>
                                    <select id="english_score" name="english_score" class="form-control">
                                        <option value="">Select grade</option>
                                        <option value="A+">A+</option>
                                        <option value="A">A</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B">B</option>
                                        <option value="C+">C+</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="malay_score" class="form-label">Bahasa Malaysia</label>
                                    <select id="malay_score" name="malay_score" class="form-control">
                                        <option value="">Select grade</option>
                                        <option value="A+">A+</option>
                                        <option value="A">A</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B">B</option>
                                        <option value="C+">C+</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interests -->
                    <div class="form-group">
                        <label for="interests" class="form-label">Areas of Interest</label>
                        <textarea id="interests" name="interests" class="form-control" rows="4" 
                                  placeholder="Describe your interests, hobbies, and subjects you enjoy. For example: technology, healthcare, business, arts, sports, etc."
                                  required><?php echo isset($existing_profile['interests']) ? htmlspecialchars($existing_profile['interests']) : ''; ?></textarea>
                    </div>

                    <!-- Personality Assessment -->
                    <div class="card" style="margin: 2rem 0; background: #f8f9fa;">
                        <h3>Personality Assessment</h3>
                        <p style="color: #666; margin-bottom: 1.5rem;">Rate yourself on the following traits (1 = Strongly Disagree, 5 = Strongly Agree)</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I enjoy working with people</label>
                                    <select name="social_skills" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I prefer working independently</label>
                                    <select name="independence" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I enjoy solving complex problems</label>
                                    <select name="problem_solving" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I am comfortable with taking risks</label>
                                    <select name="risk_taking" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I enjoy creative and artistic activities</label>
                                    <select name="creativity" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">I prefer structured and organized work</label>
                                    <select name="organization" class="form-control" required>
                                        <option value="">Select rating</option>
                                        <option value="1">1 - Strongly Disagree</option>
                                        <option value="2">2 - Disagree</option>
                                        <option value="3">3 - Neutral</option>
                                        <option value="4">4 - Agree</option>
                                        <option value="5">5 - Strongly Agree</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Career Preferences -->
                    <div class="form-group">
                        <label for="career_goals" class="form-label">Career Goals & Preferences</label>
                        <textarea id="career_goals" name="career_goals" class="form-control" rows="3" 
                                  placeholder="Describe your career aspirations, preferred work environment, salary expectations, etc."><?php echo isset($existing_profile['career_goals']) ? htmlspecialchars($existing_profile['career_goals']) : ''; ?></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" id="submit-btn" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                            <span id="btn-text">Get AI Career Recommendations</span>
                            <span id="btn-loading" style="display: none;">
                                <span style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ffffff; border-radius: 50%; border-top-color: transparent; animation: spin 1s linear infinite; margin-right: 8px;"></span>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 EduLink. AI-Powered Career Guidance Platform.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>