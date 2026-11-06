// Name validation (only alphabets and spaces)
            function validateName(name) {
                const regex = /^[a-zA-Z\s]+$/;
                return regex.test(name);
            }

            // DOB validation (age > 17)
            function validateDOB(dob) {
                const today = new Date();
                const birthDate = new Date(dob);
                const age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    return age - 1 >= 17;
                }
                return age >= 17;
            }

            // Year validation (>= 2000)
            function validateYear(year) {
                return year >= 2000 && year <= 2030;
            }

            // Check if admission number already exists
            function isAdmnoUnique(admno, excludeId = null) {
                return !existingStudents.some(student => {
                    if (excludeId && student.id === excludeId) return false;
                    return student.admno === admno;
                });
            }

            // Check if student already exists (same name and DOB)
            function isStudentUnique(name, dob, excludeId = null) {
                return !existingStudents.some(student => {
                    if (excludeId && student.id === excludeId) return false;
                    return student.name.toLowerCase() === name.toLowerCase() && student.dob === dob;
                });
            }

            // Validate add student form
            function validateAddStudentForm() {
                let isValid = true;
                const name = document.getElementById('name').value.trim();
                const admno = document.getElementById('admno').value.trim();
                const dob = document.getElementById('dob').value;
                const year = document.getElementById('year').value;

                // Reset error messages
                document.getElementById('nameError').textContent = '';
                document.getElementById('admnoError').textContent = '';
                document.getElementById('dobError').textContent = '';
                document.getElementById('yearError').textContent = '';

                // Validate name
                if (!name) {
                    document.getElementById('nameError').textContent = 'Name is required';
                    isValid = false;
                } else if (!validateName(name)) {
                    document.getElementById('nameError').textContent = 'Name should contain only alphabets and spaces';
                    isValid = false;
                }

                // Validate admission number
                if (!admno) {
                    document.getElementById('admnoError').textContent = 'Admission number is required';
                    isValid = false;
                } else if (!isAdmnoUnique(admno)) {
                    document.getElementById('admnoError').textContent = 'Admission number already exists';
                    isValid = false;
                }
                

                // Validate date of birth
                if (!dob) {
                    document.getElementById('dobError').textContent = 'Date of birth is required';
                    isValid = false;
                } else if (!validateDOB(dob)) {
                    document.getElementById('dobError').textContent = 'Student must be at least 17 years old';
                    isValid = false;
                } else if (!isStudentUnique(name, dob)) {
                    document.getElementById('dobError').textContent = 'Student with this name and date of birth already exists';
                    isValid = false;
                }

                // Validate year
                if (!year) {
                    document.getElementById('yearError').textContent = 'Year is required';
                    isValid = false;
                } else if (!validateYear(year)) {
                    document.getElementById('yearError').textContent = 'Year must be between 2000 and 2030';
                    isValid = false;
                }

                return isValid;
            }

            // Validate edit student form
            function validateEditStudentForm() {
                let isValid = true;
                const id = document.getElementById('editId').value;
                const name = document.getElementById('editName').value.trim();
                const admno = document.getElementById('editAdmno').value.trim();
                const dob = document.getElementById('editDob').value;
                const year = document.getElementById('editYear').value;

                // Reset error messages
                document.getElementById('editNameError').textContent = '';
                document.getElementById('editAdmnoError').textContent = '';
                document.getElementById('editDobError').textContent = '';
                document.getElementById('editYearError').textContent = '';

                // Validate name
                if (!name) {
                    document.getElementById('editNameError').textContent = 'Name is required';
                    isValid = false;
                } else if (!validateName(name)) {
                    document.getElementById('editNameError').textContent = 'Name should contain only alphabets and spaces';
                    isValid = false;
                }

                // Validate date of birth
                if (!dob) {
                    document.getElementById('editDobError').textContent = 'Date of birth is required';
                    isValid = false;
                } else if (!validateDOB(dob)) {
                    document.getElementById('editDobError').textContent = 'Student must be at least 17 years old';
                    isValid = false;
                } else if (!isStudentUnique(name, dob, parseInt(id))) {
                    document.getElementById('editDobError').textContent = 'Student with this name and date of birth already exists';
                    isValid = false;
                }

                // Validate year
                if (!year) {
                    document.getElementById('editYearError').textContent = 'Year is required';
                    isValid = false;
                } else if (!validateYear(year)) {
                    document.getElementById('editYearError').textContent = 'Year must be between 2000 and 2030';
                    isValid = false;
                }

                return isValid;
            }

            // Real-time validation for add form
            if (addStudentForm) {
                document.getElementById('name').addEventListener('input', function() {
                    const name = this.value.trim();
                    const errorElement = document.getElementById('nameError');
                    
                    if (!name) {
                        errorElement.textContent = 'Name is required';
                    } else if (!validateName(name)) {
                        errorElement.textContent = 'Name should contain only alphabets and spaces';
                    } else {
                        errorElement.textContent = '';
                    }
                });

                document.getElementById('admno').addEventListener('input', function() {
                    const admno = this.value.trim();
                    const errorElement = document.getElementById('admnoError');
                    
                    if (!admno) {
                        errorElement.textContent = 'Admission number is required';
                    } else if (!isAdmnoUnique(admno)) {
                        errorElement.textContent = 'Admission number already exists';
                    } else {
                        errorElement.textContent = '';
                    }
                });

                document.getElementById('dob').addEventListener('change', function() {
                    const dob = this.value;
                    const name = document.getElementById('name').value.trim();
                    const errorElement = document.getElementById('dobError');
                    
                    if (!dob) {
                        errorElement.textContent = 'Date of birth is required';
                    } else if (!validateDOB(dob)) {
                        errorElement.textContent = 'Student must be at least 17 years old';
                    } else if (name && !isStudentUnique(name, dob)) {
                        errorElement.textContent = 'Student with this name and date of birth already exists';
                    } else {
                        errorElement.textContent = '';
                    }
                });

                document.getElementById('year').addEventListener('input', function() {
                    const year = this.value;
                    const errorElement = document.getElementById('yearError');
                    
                    if (!year) {
                        errorElement.textContent = 'Year is required';
                    } else if (!validateYear(year)) {
                        errorElement.textContent = 'Year must be between 2000 and 2030';
                    } else {
                        errorElement.textContent = '';
                    }
                });
            }

            // Simulate opening edit modal (for demonstration)
            document.querySelectorAll('.btn-primary').forEach(button => {
                if (button.textContent === 'Edit') {
                    button.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const cells = row.querySelectorAll('td');
                        
                        document.getElementById('editId').value = 1; // Simulated ID
                        document.getElementById('editName').value = cells[1].textContent;
                        document.getElementById('editAdmno').value = cells[0].textContent;
                        document.getElementById('editDob').value = cells[2].textContent;
                        document.getElementById('editYear').value = cells[3].textContent;
                        
                        // Show the modal
                        new bootstrap.Modal(document.getElementById('editStudentModal')).show();
                    });
                }
            });
    