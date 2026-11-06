    function validateName(name) {
                const regex = /^[a-zA-Z\s]+$/;
                return regex.test(name);
            }
    
function validateLocation(comment) {
    
    // 1. Check if empty
    if (empty(comment)) {
        return "Please add a comment";
    }
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
        }
         // Form validation
        function validateForm() {
            // Validate all fields
            validateName(document.getElementById("name"));
            validateEmail(document.getElementById("email"));
            validateRegno(document.getElementById("comment"));
        
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
        document.getElementById("feedbackForm").onsubmit = function() {
            return validateForm();
        };
