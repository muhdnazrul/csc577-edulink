<?php
require_once 'config.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $conn = getDBConnection();
    
    // Collect and sanitize form data
    $education_level = sanitizeInput($_POST['education_level']);
    $interests = sanitizeInput($_POST['interests']);
    $career_goals = sanitizeInput($_POST['career_goals'] ?? '');
    
    // Collect academic scores
    $scores = [
        'mathematics' => $_POST['math_score'] ?? '',
        'science' => $_POST['science_score'] ?? '',
        'english' => $_POST['english_score'] ?? '',
        'bahasa_malaysia' => $_POST['malay_score'] ?? ''
    ];
    
    // Collect personality traits
    $personality = [
        'social_skills' => (int)($_POST['social_skills'] ?? 0),
        'independence' => (int)($_POST['independence'] ?? 0),
        'problem_solving' => (int)($_POST['problem_solving'] ?? 0),
        'risk_taking' => (int)($_POST['risk_taking'] ?? 0),
        'creativity' => (int)($_POST['creativity'] ?? 0),
        'organization' => (int)($_POST['organization'] ?? 0)
    ];
    
    // Validate required fields
    if (empty($education_level) || empty($interests)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit();
    }
    
    // Save or update profile
    $stmt = $conn->prepare("SELECT id FROM profiles WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $scores_json = json_encode($scores);
    $personality_json = json_encode($personality);
    
    if ($result->num_rows > 0) {
        // Update existing profile
        $stmt = $conn->prepare("UPDATE profiles SET education_level = ?, scores_json = ?, interests = ?, personality_json = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?");
        $stmt->bind_param("ssssi", $education_level, $scores_json, $interests, $personality_json, $_SESSION['user_id']);
    } else {
        // Insert new profile
        $stmt = $conn->prepare("INSERT INTO profiles (user_id, education_level, scores_json, interests, personality_json) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $_SESSION['user_id'], $education_level, $scores_json, $interests, $personality_json);
    }
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to save profile']);
        exit();
    }
    
    // Prepare data for OpenAI API
    $userData = [
        'education_level' => $education_level,
        'academic_scores' => $scores,
        'interests' => $interests,
        'personality_traits' => $personality,
        'career_goals' => $career_goals,
        'location' => 'Malaysia'
    ];
    
    // Create system prompt for Malaysian context
    $systemPrompt = "You are a professional Malaysian career advisor with extensive knowledge of the Malaysian job market, education system, and career opportunities. Analyze the user's profile and provide exactly 5 specific career or course recommendations that are:

1. Relevant to the Malaysian job market
2. Aligned with their academic performance and interests
3. Suitable for their personality traits
4. Include specific Malaysian universities, colleges, or institutions where applicable
5. Consider current industry trends in Malaysia

IMPORTANT: Format your response EXACTLY as shown below. Each recommendation must be a complete block with all information grouped together:

**Recommendation 1: [Career/Course Title]**

**Description:** [2-3 sentences describing the field/course]

**Match:** [Why it matches their profile - interests, personality, academic performance]

**Prospects:** [Career opportunities and job market outlook in Malaysia]

**Next Steps:** [Specific universities, courses, or actions to pursue this path]

---

**Recommendation 2: [Career/Course Title]**

**Description:** [2-3 sentences describing the field/course]

**Match:** [Why it matches their profile - interests, personality, academic performance]

**Prospects:** [Career opportunities and job market outlook in Malaysia]

**Next Steps:** [Specific universities, courses, or actions to pursue this path]

---

[Continue this exact format for all 5 recommendations]

Do NOT number each section separately. Keep all information for each recommendation together as one cohesive block.";
    
    $userPrompt = "Please analyze my profile and provide career recommendations:\n\n" . json_encode($userData, JSON_PRETTY_PRINT);
    
    // Prepare OpenAI API request
    $payload = [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt]
        ],
        'max_tokens' => 2000,
        'temperature' => 0.7
    ];
    
    // Make API call to OpenAI
    $ch = curl_init(OPENAI_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        echo json_encode(['success' => false, 'message' => 'Network error: ' . $curlError]);
        exit();
    }
    
    if ($httpCode !== 200) {
        $errorDetails = json_decode($response, true);
        $errorMessage = 'API request failed with code: ' . $httpCode;
        if ($errorDetails && isset($errorDetails['error']['message'])) {
            $errorMessage .= ' - ' . $errorDetails['error']['message'];
        }
        echo json_encode(['success' => false, 'message' => $errorMessage, 'response' => $response]);
        exit();
    }
    
    $result = json_decode($response, true);
    
    if (!$result || !isset($result['choices'][0]['message']['content'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid API response']);
        exit();
    }
    
    $recommendations = $result['choices'][0]['message']['content'];
    
    // Save recommendations to database
    $stmt = $conn->prepare("INSERT INTO recommendations (user_id, recommendations_json) VALUES (?, ?)");
    $recommendations_json = json_encode([
        'raw_response' => $recommendations,
        'generated_at' => date('Y-m-d H:i:s'),
        'user_data' => $userData
    ]);
    $stmt->bind_param("is", $_SESSION['user_id'], $recommendations_json);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Recommendations generated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save recommendations']);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Process.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred']);
}
?>