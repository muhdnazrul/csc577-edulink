// EduLink - Main JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initFormValidation();
    initProgressBar();
    initPDFExport();
});

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Remove existing error styling
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required.';
    }
    
    // Email validation
    if (fieldName === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address.';
        }
    }
    
    // Password validation
    if (fieldName === 'password' && value) {
        if (value.length < 6) {
            isValid = false;
            errorMessage = 'Password must be at least 6 characters long.';
        }
    }
    
    // Confirm password validation
    if (fieldName === 'confirm_password' && value) {
        const password = document.querySelector('input[name="password"]');
        if (password && value !== password.value) {
            isValid = false;
            errorMessage = 'Passwords do not match.';
        }
    }
    
    // Display error if validation failed
    if (!isValid) {
        field.classList.add('error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = errorMessage;
        field.parentNode.appendChild(errorDiv);
    }
    
    return isValid;
}

// Progress Bar for AI Processing
function initProgressBar() {
    const processForm = document.getElementById('profile-form');
    if (processForm) {
        processForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Show loading state on button immediately
            showButtonLoading();
            // Auto scroll to top to show progress bar - mobile compatible
            scrollToTop();
            showProgressBar();
            submitProfileForm(this);
        });
    }
}

// Button loading state functions
function showButtonLoading() {
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    
    if (submitBtn && btnText && btnLoading) {
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline-block';
    }
}

function hideButtonLoading() {
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');
    
    if (submitBtn && btnText && btnLoading) {
        submitBtn.disabled = false;
        btnText.style.display = 'inline-block';
        btnLoading.style.display = 'none';
    }
}

// Mobile-compatible scroll to top function
function scrollToTop() {
    // Try modern smooth scroll first
    if ('scrollBehavior' in document.documentElement.style) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        // Fallback for older browsers and mobile devices
        window.scrollTo(0, 0);
    }
    
    // Additional mobile-specific scroll handling
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    
    // Force scroll for stubborn mobile browsers
    setTimeout(() => {
        window.pageYOffset = 0;
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }, 50);
}

function showProgressBar() {
    const progressContainer = document.getElementById('progress-container');
    if (progressContainer) {
        progressContainer.style.display = 'block';
        animateProgress();
    }
}

function animateProgress() {
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        let width = 0;
        const interval = setInterval(() => {
            width += Math.random() * 15;
            if (width >= 90) {
                width = 90;
                clearInterval(interval);
            }
            progressBar.style.width = width + '%';
        }, 500);
    }
}

function completeProgress() {
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        progressBar.style.width = '100%';
        setTimeout(() => {
            const progressContainer = document.getElementById('progress-container');
            if (progressContainer) {
                progressContainer.style.display = 'none';
            }
        }, 1000);
    }
}

// AJAX Form Submission
function submitProfileForm(form) {
    const formData = new FormData(form);
    
    fetch('process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        completeProgress();
        hideButtonLoading();
        if (data.success) {
            window.location.href = 'results.php';
        } else {
            showAlert('error', data.message || 'An error occurred while processing your request.');
        }
    })
    .catch(error => {
        completeProgress();
        hideButtonLoading();
        console.error('Error:', error);
        showAlert('error', 'An error occurred while processing your request.');
    });
}

// Alert System
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// Helper function to parse recommendation sections
function parseRecommendationSections(description) {
    const sections = {
        description: '',
        match: '',
        prospects: '',
        nextSteps: ''
    };
    
    // Split by section headers
    const parts = description.split(/(?=Why This Matches You:|Career Prospects:|Next Steps:)/);
    
    parts.forEach(part => {
        part = part.trim();
        if (part.startsWith('Why This Matches You:')) {
            sections.match = part.replace('Why This Matches You:', '').trim();
        } else if (part.startsWith('Career Prospects:')) {
            sections.prospects = part.replace('Career Prospects:', '').trim();
        } else if (part.startsWith('Next Steps:')) {
            // Handle duplicate "Next Steps:" sections
            let nextStepsText = part.replace('Next Steps:', '').trim();
            // Remove duplicate "Next Steps:" if it appears again
            nextStepsText = nextStepsText.replace(/Next Steps:\s*/g, '');
            sections.nextSteps = nextStepsText;
        } else if (part && !part.includes('Why This Matches You:') && !part.includes('Career Prospects:') && !part.includes('Next Steps:')) {
            // This is the main description
            sections.description = part;
        }
    });
    
    return sections;
}

// PDF Export Functionality
function initPDFExport() {
    const exportBtn = document.getElementById('export-pdf');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportToPDF);
    }
}

function exportToPDF() {
    // Wait a moment for jsPDF to fully load if needed
    setTimeout(() => {
        // Check if jsPDF is loaded (handle UMD module format)
        let jsPDF;
        
        if (typeof window.jspdf !== 'undefined' && window.jspdf.jsPDF) {
            // UMD format: window.jspdf.jsPDF
            jsPDF = window.jspdf.jsPDF;
        } else if (typeof window.jsPDF !== 'undefined') {
            // Alternative format: window.jsPDF
            jsPDF = window.jsPDF;
        } else if (typeof jsPDF !== 'undefined') {
            // Global jsPDF
            // jsPDF is already available
        } else {
            showAlert('error', 'PDF export library not loaded. Please refresh the page and try again.');
            console.error('jsPDF not found. Available objects:', Object.keys(window).filter(key => key.toLowerCase().includes('pdf')));
            return;
        }
        
        try {
            const doc = new jsPDF();
            
            // Get user data
            const userName = document.querySelector('.user-name')?.textContent || 'User';
            const recommendations = document.querySelectorAll('.recommendation-card');
            
            if (recommendations.length === 0) {
                showAlert('error', 'No recommendations found to export.');
                return;
            }
            
            // Add header
            doc.setFontSize(20);
            doc.text('EduLink - Career Recommendations', 20, 30);
            
            doc.setFontSize(14);
            doc.text(`Generated for: ${userName}`, 20, 45);
            doc.text(`Date: ${new Date().toLocaleDateString()}`, 20, 55);
            
            // Add recommendations with professional formatting
            let yPosition = 75;
            recommendations.forEach((card, index) => {
                const title = card.querySelector('.recommendation-title')?.textContent || `Recommendation ${index + 1}`;
                let description = card.querySelector('.recommendation-description')?.textContent || '';
                
                // Clean up emojis and special characters for PDF compatibility
                description = description
                    .replace(/🎯\s*Why This Matches You:/g, 'Why This Matches You:')
                    .replace(/🚀\s*Career Prospects:/g, 'Career Prospects:')
                    .replace(/📚\s*Next Steps:/g, 'Next Steps:')
                    .replace(/[^\x00-\x7F]/g, '') // Remove non-ASCII characters
                    .replace(/\s+/g, ' ') // Normalize whitespace
                    .trim();
                
                // Check if we need a new page
                if (yPosition > 220) {
                    doc.addPage();
                    yPosition = 30;
                }
                
                // Add recommendation title
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text(`${index + 1}. ${title}`, 20, yPosition);
                yPosition += 15;
                
                // Parse and format sections
                const sections = parseRecommendationSections(description);
                
                // Add main description if exists
                if (sections.description) {
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'normal');
                    const descLines = doc.splitTextToSize(sections.description, 170);
                    doc.text(descLines, 20, yPosition);
                    yPosition += (descLines.length * 4) + 8;
                }
                
                // Add "Why This Matches You" section
                if (sections.match) {
                    doc.setFontSize(11);
                    doc.setFont(undefined, 'bold');
                    doc.text('Why This Matches You:', 20, yPosition);
                    yPosition += 6;
                    
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'normal');
                    const matchLines = doc.splitTextToSize(sections.match, 170);
                    doc.text(matchLines, 25, yPosition);
                    yPosition += (matchLines.length * 4) + 6;
                }
                
                // Add "Career Prospects" section
                if (sections.prospects) {
                    doc.setFontSize(11);
                    doc.setFont(undefined, 'bold');
                    doc.text('Career Prospects:', 20, yPosition);
                    yPosition += 6;
                    
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'normal');
                    const prospectLines = doc.splitTextToSize(sections.prospects, 170);
                    doc.text(prospectLines, 25, yPosition);
                    yPosition += (prospectLines.length * 4) + 6;
                }
                
                // Add "Next Steps" section
                if (sections.nextSteps) {
                    doc.setFontSize(11);
                    doc.setFont(undefined, 'bold');
                    doc.text('Next Steps:', 20, yPosition);
                    yPosition += 6;
                    
                    doc.setFontSize(10);
                    doc.setFont(undefined, 'normal');
                    const stepsLines = doc.splitTextToSize(sections.nextSteps, 170);
                    doc.text(stepsLines, 25, yPosition);
                    yPosition += (stepsLines.length * 4) + 12;
                }
                
                // Add separator line between recommendations
                if (index < recommendations.length - 1) {
                    doc.setDrawColor(200, 200, 200);
                    doc.line(20, yPosition, 190, yPosition);
                    yPosition += 8;
                }
            });
            
            // Save the PDF
            doc.save('edulink-career-recommendations.pdf');
            showAlert('success', 'PDF exported successfully!');
            
        } catch (error) {
            console.error('PDF export error:', error);
            showAlert('error', 'Failed to export PDF. Please try again.');
        }
    }, 100); // Small delay to ensure library is loaded
}

// Utility Functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-MY', options);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Auto-hide alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});