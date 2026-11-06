// Client-side validation functions
function validateName(input) {
    const errorElement = document.getElementById(input.id + 'Error');
    const value = input.value.trim();
    
    if (value === '') {
        errorElement.textContent = 'Course name is required';
        return false;
    }
    
    if (!/^[a-zA-Z\s]+$/.test(value)) {
        errorElement.textContent = 'Course name can only contain letters and spaces';
        return false;
    }
    
    if (value.length < 2 || value.length > 50) {
        errorElement.textContent = 'Course name must be between 2 and 50 characters';
        return false;
    }
    
    // Clear any previous error while we check for duplicates
    errorElement.textContent = 'Checking...';
    
    // Check if course already exists
    return checkCourseExists(value, errorElement);
}

function checkCourseExists(courseName, errorElement) {
    return new Promise((resolve) => {
        // Make AJAX call to check if course exists
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'check_course_exists.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.exists) {
                    errorElement.textContent = 'Course already existS';
                    resolve(false);
                } else {
                    errorElement.textContent = '';
                    resolve(true);
                }
            } else {
                errorElement.textContent = 'Course already exists!';
                resolve(false);
            }
        };
        
        xhr.onerror = function() {
            errorElement.textContent = 'Connection error';
            resolve(false);
        };
        
        xhr.send('course=' + encodeURIComponent(courseName));
    });
}

function validateDuration(input) {
    const errorElement = document.getElementById(input.id + 'Error');
    const value = input.value.trim();
    
    if (value === '') {
        errorElement.textContent = 'Duration is required';
        return false;
    }
    
    if (!/^[0-9.]+$/.test(value)) {
        errorElement.textContent = 'Duration must be a number';
        return false;
    }
    
    const durationValue = parseFloat(value);
    if (durationValue <= 0) {
        errorElement.textContent = 'Duration must be a positive number';
        return false;
    }
    
    if (durationValue < 0.5 || durationValue > 36) {
        errorElement.textContent = 'Duration must be between 0.5 and 36';
        return false;
    }
    
    errorElement.textContent = '';
    return true;
}

async function validateForm() {
    let isValid = true;
    
    // Validate course name
    const courseValid = await validateName(document.getElementById("course"));
    if (!courseValid) {
        isValid = false;
    }
    
    // Validate duration
    if (!validateDuration(document.getElementById("duration"))) {
        isValid = false;
    }
    
    return isValid;
}

// Attach event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add Course Form validation
    const addForm = document.getElementById("addCourseForm");
    if (addForm) {
        addForm.addEventListener("submit", async function(event) {
            event.preventDefault(); // Always prevent first to allow async validation
            
            const isValid = await validateForm();
            if (isValid) {
                this.submit(); // Submit if validation passes
            }
        });
        
        // Add onblur validation
        document.getElementById("course").addEventListener("blur", function() {
            validateName(this);
        });
        
        document.getElementById("duration").addEventListener("blur", function() {
            validateDuration(this);
        });
    }
    
    // Edit Course Form validation (similar to add form)
    const editForm = document.getElementById("editCourseForm");
    if (editForm) {
        editForm.addEventListener("submit", async function(event) {
            event.preventDefault();
            
            let isValid = true;
            
            // For edit form, we should pass the course ID to exclude it from duplicate check
            const courseId = document.getElementById("editCourseId").value;
            const courseName = document.getElementById("editCourse").value.trim();
            
            // Validate course name
            const courseValid = await validateName(document.getElementById("editCourse"));
            if (!courseValid) {
                isValid = false;
            }
            
            // Validate duration
            if (!validateDuration(document.getElementById("editDuration"))) {
                isValid = false;
            }
            
            if (isValid) {
                this.submit();
            }
        });
        
        // Add onblur validation for edit form
        document.getElementById("editCourse").addEventListener("blur", function() {
            validateName(this);
        });
        
        document.getElementById("editDuration").addEventListener("blur", function() {
            validateDuration(this);
        });
    }
});