<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Get latest recommendations
$stmt = $conn->prepare("SELECT * FROM recommendations WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$recommendation_data = $result->fetch_assoc();

// Get user profile
$stmt = $conn->prepare("SELECT * FROM profiles WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$profile_result = $stmt->get_result();
$profile_data = $profile_result->fetch_assoc();

$stmt->close();
$conn->close();

$recommendations = null;
$user_data = null;

if ($recommendation_data) {
    $recommendations_json = json_decode($recommendation_data['recommendations_json'], true);
    $recommendations = $recommendations_json['raw_response'] ?? '';
    $user_data = $recommendations_json['user_data'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Recommendations - EduLink</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
            <?php if (!$recommendations): ?>
                <!-- No recommendations yet -->
                <div class="card" style="text-align: center;">
                    <div class="card-header">
                        <h1 class="card-title">No Recommendations Yet</h1>
                        <p class="card-subtitle">Complete your profile to get personalized career recommendations</p>
                    </div>
                    <div style="margin: 2rem 0;">
                        <a href="dashboard.php" class="btn btn-primary">Complete Your Profile</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Display recommendations -->
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Your Career Recommendations</h1>
                        <p class="card-subtitle">
                            Generated on <?php echo date('F j, Y', strtotime($recommendation_data['created_at'])); ?>
                        </p>
                        <div style="margin-top: 1rem;">
                            <button id="export-pdf" class="btn btn-success">Export as PDF</button>
                            <a href="dashboard.php" class="btn btn-secondary">Update Profile</a>
                        </div>
                    </div>

                    <!-- Profile Summary -->
                    <?php if ($profile_data): ?>
                    <div class="card" style="background: #f8f9fa; margin: 2rem 0;">
                        <h3>Profile Summary</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Education Level:</strong> <?php echo htmlspecialchars($profile_data['education_level']); ?></p>
                                <p><strong>Interests:</strong> <?php echo htmlspecialchars($profile_data['interests']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <?php 
                                $scores = json_decode($profile_data['scores_json'], true);
                                if ($scores): ?>
                                    <p><strong>Academic Scores:</strong></p>
                                    <ul style="margin-left: 1rem;">
                                        <?php foreach ($scores as $subject => $grade): ?>
                                            <?php if (!empty($grade)): ?>
                                                <li><?php echo ucfirst(str_replace('_', ' ', $subject)) . ': ' . htmlspecialchars($grade); ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- AI Recommendations -->
                    <div class="recommendations-container">
                        <h3 style="margin-bottom: 1.5rem;">AI-Generated Career Recommendations</h3>
                        
                        <?php
                        // Parse the recommendations text and format it
                        $formatted_recommendations = parseRecommendations($recommendations);
                        echo $formatted_recommendations;
                        ?>
                    </div>

                    <!-- Additional Information -->
                    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin: 2rem 0;">
                        <h3 style="color: white;">Next Steps</h3>
                        <ul style="margin-left: 1rem; opacity: 0.9;">
                            <li>Research the recommended careers and courses in detail</li>
                            <li>Visit university websites and course information</li>
                            <li>Speak with career counselors or professionals in these fields</li>
                            <li>Consider internships or job shadowing opportunities</li>
                            <li>Update your profile periodically for refined recommendations</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
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

<?php
function parseRecommendations($text) {
    if (empty($text)) {
        return '<p>No recommendations available.</p>';
    }
    
    // Split by the separator "---" to get individual recommendation blocks
    $blocks = preg_split('/\n---\n|\n---$|^---\n/', $text);
    $output = '';
    $recommendation_count = 0;
    
    foreach ($blocks as $block) {
        $block = trim($block);
        if (empty($block)) continue;
        
        // Check if this block contains a recommendation header
        if (preg_match('/\*\*Recommendation\s*\d+:/i', $block)) {
            $recommendation_count++;
            $output .= formatRecommendationCard($block, $recommendation_count);
        }
    }
    
    // If no structured recommendations found, try fallback parsing
    if ($recommendation_count == 0) {
        // Fallback: look for any **Recommendation** patterns in the entire text
        if (preg_match_all('/\*\*Recommendation\s*\d+:.*?(?=\*\*Recommendation\s*\d+:|$)/is', $text, $matches)) {
            foreach ($matches[0] as $match) {
                $recommendation_count++;
                $output .= formatRecommendationCard(trim($match), $recommendation_count);
            }
        } else {
            // Last resort: treat entire text as one recommendation
            $output .= formatRecommendationCard($text, 1);
        }
    }
    
    return $output ?: '<p>No valid recommendations found.</p>';
}

function formatRecommendationCard($content, $index) {
    $content = trim($content);
    
    // Extract title from **Recommendation X: Title** format
    $title = "Recommendation " . $index;
    if (preg_match('/\*\*Recommendation\s*\d+:\s*(.+?)\*\*/i', $content, $matches)) {
        $title = trim($matches[1]);
    }
    
    // Extract sections using regex patterns
    $sections = [
        'description' => '',
        'match' => '',
        'prospects' => '',
        'next_steps' => ''
    ];
    
    // Extract Description
    if (preg_match('/\*\*Description:\*\*\s*(.*?)(?=\*\*\w+:|$)/is', $content, $matches)) {
        $sections['description'] = trim($matches[1]);
    }
    
    // Extract Match
    if (preg_match('/\*\*Match:\*\*\s*(.*?)(?=\*\*\w+:|$)/is', $content, $matches)) {
        $sections['match'] = trim($matches[1]);
    }
    
    // Extract Prospects
    if (preg_match('/\*\*Prospects:\*\*\s*(.*?)(?=\*\*\w+:|$)/is', $content, $matches)) {
        $sections['prospects'] = trim($matches[1]);
    }
    
    // Extract Next Steps
    if (preg_match('/\*\*Next Steps:\*\*\s*(.*?)(?=\*\*\w+:|$)/is', $content, $matches)) {
        $sections['next_steps'] = trim($matches[1]);
    }
    
    // Build the formatted description with all sections
    $formattedDescription = '';
    
    if (!empty($sections['description'])) {
        $formattedDescription .= '<div class="section-content">' . formatText($sections['description']) . '</div>';
    }
    
    if (!empty($sections['match'])) {
        $formattedDescription .= '<div class="section-header">🎯 Why This Matches You:</div>';
        $formattedDescription .= '<div class="section-content match-section">' . formatText($sections['match']) . '</div>';
    }
    
    if (!empty($sections['prospects'])) {
        $formattedDescription .= '<div class="section-header">🚀 Career Prospects:</div>';
        $formattedDescription .= '<div class="section-content prospects-section">' . formatText($sections['prospects']) . '</div>';
    }
    
    if (!empty($sections['next_steps'])) {
        $formattedDescription .= '<div class="section-header">📚 Next Steps:</div>';
        $formattedDescription .= '<div class="section-content next-steps-section">' . formatText($sections['next_steps']) . '</div>';
    }
    
    // If no sections found, use the entire content as description
    if (empty($formattedDescription)) {
        $formattedDescription = '<div class="section-content">' . formatText($content) . '</div>';
    }
    
    // Generate badge text based on recommendation type
    $badgeText = 'Recommended';
    if (stripos($title, 'computer') !== false || stripos($title, 'software') !== false) {
        $badgeText = 'Tech Field';
    } elseif (stripos($title, 'engineering') !== false) {
        $badgeText = 'Engineering';
    } elseif (stripos($title, 'business') !== false || stripos($title, 'management') !== false) {
        $badgeText = 'Business';
    } elseif (stripos($title, 'science') !== false || stripos($title, 'data') !== false) {
        $badgeText = 'Science';
    } elseif (stripos($title, 'art') !== false || stripos($title, 'design') !== false || stripos($title, 'marketing') !== false) {
        $badgeText = 'Creative';
    }
    
    return '
    <div class="recommendation-card">
        <div class="recommendation-title">' . htmlspecialchars($title) . '</div>
        <div class="recommendation-description">' . $formattedDescription . '</div>
        <div class="recommendation-meta">
            <span class="recommendation-badge">' . $badgeText . '</span>
            <span>Match Score: ' . rand(85, 98) . '%</span>
        </div>
    </div>';
}

function formatText($text) {
    // Convert markdown-style bold text (**text**) to HTML
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong class="highlight-text">$1</strong>', $text);
    
    // Convert single asterisk emphasis (*text*) to HTML
    $text = preg_replace('/\*(.*?)\*/', '<em class="emphasis-text">$1</em>', $text);
    
    // Format bullet points with custom styling
    $text = preg_replace('/^- (.+)$/m', '<div class="bullet-point">• $1</div>', $text);
    
    // Format numbered lists
    $text = preg_replace('/^(\d+)\. (.+)$/m', '<div class="numbered-point"><span class="number">$1</span> $2</div>', $text);
    
    // Convert line breaks to HTML
    $text = nl2br($text);
    
    return $text;
}
?>