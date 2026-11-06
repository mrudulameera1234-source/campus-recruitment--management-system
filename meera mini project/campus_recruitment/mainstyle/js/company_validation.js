
        // Validation functions
        function validateName(field) {
            const error = document.getElementById("nameError");
           
            if (field.value.trim() === "") {
                showError(field, error, "Name is required.");
            }  else {
                clearError(field, error);
            }
        }


          function validateDescription(field) {
            const error = document.getElementById("descriptionError");
           
            if (field.value.trim() === "") {
                showError(field, error, "description is required.");
            }  else {
                clearError(field, error);
            }
        }
        
function validateRegno($conn, $regno) {
    $regno = trim($regno);
    
    // 1. Check if empty
    if (empty($regno)) {
        return "Registration number is required";
    }
    
    // 2. Validate length and format
    if (!preg_match('/^[A-Za-z0-9]{8,15}$/', $regno)) {
        return "Invalid format. Use 8-15 alphanumeric characters";
    }
    
    // 3. Check for special patterns (country-specific)
    if (preg_match('/[^A-Za-z0-9]/', $regno)) {
        return "Special characters not allowed";
    }
    
    // 4. Check uniqueness in database
    $query = "SELECT company_id FROM company WHERE reg_number = " .  mysqli_real_escape_string($conn, $regno) ;
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return "Registration number already exists";
    }
    
    return ""; // No errors
}

        
        function validateEmail(field) {
            const error = document.getElementById("emailError");
            const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const email = field.value.trim();
            
            if (email === "") {
                showError(field, error, "Email is required.");
                return;
            }
            
            if (!pattern.test(email)) {
                showError(field, error, "Enter a valid email.");
                return;
            }
            
            // Check email availability via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "check_email.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    if (xhr.responseText.trim() === "exists") {
                        showError(field, error, "Email already exists.");
                    } else {
                        clearError(field, error);
                    }
                }
            };
            xhr.send("email=" + encodeURIComponent(email));
        }
        
        function validatePhone(field) {
            const phone = field.value.trim();
            const error = document.getElementById("phoneError");
            const phonePattern = /^\d{10}$/;
            
            if (phone === "") {
                showError(field, error, "Phone number is required.");
            } else if (!phonePattern.test(phone)) {
                showError(field, error, "Please enter a valid 10-digit phone number.");
            } else {
                clearError(field, error);
            }
        }
        
        function validatePassword(field) {
            const error = document.getElementById("passwordError");
            
            if (field.value === "") {
                showError(field, error, "Password is required.");
            } else if (field.value.length < 6) {
                showError(field, error, "Password must be at least 6 characters.");
            } else {
                clearError(field, error);
            }
        }
        
        function validateConfirmPassword(field) {
            const error = document.getElementById("confirmPasswordError");
            const pwd = document.getElementById("password").value;
            
            if (field.value === "") {
                showError(field, error, "Please confirm your password.");
            } else if (pwd !== field.value) {
                showError(field, error, "Passwords do not match.");
            } else {
                clearError(field, error);
            }
        }

function validateLocation($location) {
    $location = trim($location);
    
    // 1. Check if empty
    if (empty($location)) {
        return "Location is required";
    }
    
    // 2. Validate length
    if (strlen($location) > 255) {
        return "Location too long (max 255 characters)";
    }
    
    // 3. Validate address format
    if (!preg_match('/^(.+),\s*(.+),\s*(.+)$/', $location)) {
        return "Please use: Street, City, Country format";
    }
    
    // 4. Check for invalid characters
    if (preg_match('/[<>{}|\\^~\[\]`]/', $location)) {
        return "Invalid characters in location";
    }
    
    return ""; // No errors
}

function validateLink(input) {
    const linkError = document.getElementById('linkError');
    const url = input.value.trim();
    
    // Regular expression for URL validation
    const pattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
    
    if (url === '') {
        linkError.textContent = 'Website address is required';
        input.classList.add('is-invalid');
        return false;
    }
    
    if (!pattern.test(url)) {
        linkError.textContent = 'Please enter a valid website address (e.g., https://example.com)';
        input.classList.add('is-invalid');
        return false;
    }
    
    // If URL doesn't start with http:// or https://, suggest adding it
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
        linkError.textContent = 'Consider using full URL with http:// or https://';
        input.classList.add('is-warning');
        return true; // This is just a warning, not an error
    }
    
    linkError.textContent = '';
    input.classList.remove('is-invalid');
    input.classList.remove('is-warning');
    return true;
}
        // Helper functions
        function showError(field, errorElement, message) {
            errorElement.textContent = message;
            field.classList.add("error-border");
        }
        
        function clearError(field, errorElement) {
            errorElement.textContent = "";
            field.classList.remove("error-border");
        }
        
        // Form validation
        function validateForm() {
            // Validate all fields
            validateName(document.getElementById("name"));
            validateEmail(document.getElementById("email"));
            validateRegno(document.getElementById("regno"));
            validateDescription(document.getElementById("description"));
            validatePhone(document.getElementById("phone"));
            validatePassword(document.getElementById("password"));
            validateLink(document.getElementById("link"));
            validateLocation(document.getElementById("location"));
            validateConfirmPassword(document.getElementById("confirm_password"));
            
            // Check if any errors exist
            const errors = document.querySelectorAll(".error");
            for (let error of errors) {
                if (error.textContent !== "") {
                    return false;
                }
            }
            return true;
        }
        
        // Attach form validation
        document.getElementById("registrationForm").onsubmit = function() {
            return validateForm();
        };
