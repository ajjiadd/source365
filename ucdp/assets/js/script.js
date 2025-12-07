// 1. Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let valid = true;
    inputs.forEach(input => {
        if (!input.value.trim()) {
            valid = false;
            input.style.borderColor = 'red';
        } else {
            input.style.borderColor = '';
        }
    });
    return valid;
}

// 2. OTP timer (simulate for register - 60 seconds)
function startOTPTimer(buttonId) {
    const button = document.getElementById(buttonId);
    let timeLeft = 60;
    button.disabled = true;
    button.textContent = Resend OTP (${timeLeft}s);
    const timer = setInterval(() => {
        timeLeft--;
        button.textContent =Resend OTP (${timeLeft}s);
        if (timeLeft <= 0) { 
            clearInterval(timer);
            button.disabled = false;
            button.textContent = 'Resend OTP';
        }
    }, 1000);
}

// 3. File upload preview (for upload_document.php)
function previewFile(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    input.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.type === 'application/pdf') {
                preview.innerHTML = <p>Selected: ${file.name} (PDF ready to upload)</p>;
            } else {
                alert('Only PDF allowed!');
                input.value = '';
            }
        }
    });
}

// 4. Confirmation for share/delete (general)
function confirmAction(message, url) {
    if (confirm(message)) {
        window.location.href = url;
    }
}

 
console.log('UCDP JS loaded!');