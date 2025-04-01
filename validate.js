(function () {
    'use strict';

    var form = document.querySelector('.needs-validation');
    var salutation = document.querySelector('#salutation');

    form.addEventListener('submit', function (event) {
        // If salutation is still 'no' (the placeholder option), mark it as invalid
        if (salutation.value == "0") {
            event.preventDefault();
            event.stopPropagation();
            salutation.classList.add('is-invalid');
            salutation.classList.remove('is-valid'); // Ensure no valid class is applied
        } else {
            salutation.classList.remove('is-invalid'); // If valid, remove the invalid class
            salutation.classList.add('is-valid'); // Mark as valid
        }

        // Check if the form is valid; if not, prevent submission
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            // Show success alert if form is valid
            alert('Form submitted successfully!');
        }

        // Add the 'was-validated' class to display validation feedback
        form.classList.add('was-validated');
    });

    // If user changes the selection, remove the invalid class
    salutation.addEventListener('input', function () {
        if (salutation.value != "0") {
            salutation.classList.remove('is-invalid'); // Remove invalid if valid option is selected
            salutation.classList.add('is-valid'); // Mark as valid
        } else {
            salutation.classList.remove('is-valid'); // Remove valid class if the user selects the default
        }
    });
})();
