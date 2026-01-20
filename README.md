# EduLink - AI-Powered Career Guidance Platform

EduLink is a comprehensive web-based system that helps Malaysian students and professionals discover their ideal career paths through AI-powered analysis of their academic performance, interests, and personality traits.

## 🚀 Features

- **User Authentication**: Secure registration and login system with password hashing
- **Comprehensive Profile Assessment**: Academic scores, interests, and personality evaluation
- **AI-Powered Recommendations**: Integration with OpenAI GPT-4o-mini for personalized career guidance
- **Malaysian Context**: Tailored recommendations for the Malaysian job market and education system
- **Responsive Design**: Modern, mobile-friendly interface
- **PDF Export**: Export recommendations as PDF for future reference
- **Secure Data Storage**: All user data securely stored in MySQL database

## 📋 Requirements

- **Web Server**: Apache/Nginx with PHP support
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **OpenAI API Key**: Required for AI recommendations

## 🛠️ Installation & Setup

### 1. Database Setup

1. Open phpMyAdmin or your MySQL client
2. Import the database schema:
   ```sql
   -- Run the contents of setup_database.sql
   ```
   Or manually execute:
   ```sql
   CREATE DATABASE edulink_db;
   USE edulink_db;
   -- Then run all the CREATE TABLE statements from setup_database.sql
   ```

### 2. Configuration

1. Open `config.php`
2. Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'edulink_db');
   ```

3. Add your OpenAI API key:
   ```php
   define('OPENAI_API_KEY', 'your_openai_api_key_here');
   ```

### 3. File Permissions

Ensure the web server has read/write permissions to all PHP files and directories.

### 4. Web Server Setup

- Place all files in your web server's document root (e.g., `htdocs`, `www`, `public_html`)
- Ensure PHP is enabled and configured
- Test by accessing `index.php` in your browser

## 📁 File Structure

```
EduLink/
├── config.php              # Database and API configuration
├── index.php               # Landing page
├── register.php             # User registration
├── login.php                # User login
├── logout.php               # Session termination
├── dashboard.php            # Profile input form
├── process.php              # AI API integration
├── results.php              # Display recommendations
├── setup_database.sql       # Database schema
├── css/
│   └── style.css           # Main stylesheet
├── js/
│   └── main.js             # JavaScript functionality
└── README.md               # This file
```

## 🎯 Usage Guide

### For Users

1. **Registration**: Create an account with your email and password
2. **Profile Setup**: Complete your academic and personal profile
3. **Get Recommendations**: Submit your profile for AI analysis
4. **View Results**: Review personalized career recommendations
5. **Export**: Download your recommendations as PDF

### For Administrators

- Monitor user registrations in the `users` table
- Review user profiles in the `profiles` table
- Track AI recommendations in the `recommendations` table

## 🔒 Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()`
- **Input Sanitization**: All user inputs are sanitized to prevent XSS
- **SQL Injection Prevention**: Prepared statements used for all database queries
- **Session Management**: Secure session handling for user authentication
- **API Key Protection**: OpenAI API key stored in configuration file

## 🧪 Testing Checklist

- [ ] User registration works without errors
- [ ] User login authenticates correctly
- [ ] Profile form submission processes successfully
- [ ] OpenAI API integration returns recommendations
- [ ] Recommendations display properly on results page
- [ ] PDF export functionality works
- [ ] All data is saved correctly in database
- [ ] Session management works (login/logout)
- [ ] Responsive design works on mobile devices

## 🔧 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config.php`
   - Ensure MySQL service is running
   - Verify database exists and tables are created

2. **OpenAI API Error**
   - Verify API key is correct and active
   - Check internet connectivity
   - Ensure API key has sufficient credits

3. **PHP Errors**
   - Enable PHP error reporting for debugging
   - Check PHP version compatibility
   - Verify all required PHP extensions are installed

4. **CSS/JS Not Loading**
   - Check file paths are correct
   - Ensure web server can serve static files
   - Clear browser cache

## 📊 Database Schema

### Users Table
- `id`: Primary key
- `name`: User's full name
- `email`: Unique email address
- `password`: Hashed password
- `created_at`: Registration timestamp

### Profiles Table
- `id`: Primary key
- `user_id`: Foreign key to users table
- `education_level`: Current education level
- `scores_json`: Academic scores in JSON format
- `interests`: User interests and hobbies
- `personality_json`: Personality assessment results

### Recommendations Table
- `id`: Primary key
- `user_id`: Foreign key to users table
- `recommendations_json`: AI recommendations in JSON format
- `created_at`: Generation timestamp

## 🌟 Future Enhancements

- Admin dashboard for user management
- Email notifications for new recommendations
- Career path tracking and progress monitoring
- Integration with Malaysian university databases
- Mobile app development
- Advanced analytics and reporting

## 📞 Support

For technical support or questions about the EduLink platform, please refer to the documentation or contact the development team.

## 📄 License

This project is developed for educational purposes. Please ensure compliance with OpenAI's usage policies when using the API integration.

---

**EduLink** - Empowering Malaysian students to discover their perfect career path through AI-powered guidance.