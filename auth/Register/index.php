<?php require_once '../../config/config.php'; ?>
<?php
// session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the desired page, e.g., the dashboard
    header("Location: $base_url");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <?php require_once '../../includes/headlinks.php'; ?>
</head>
<body style="background-color: #f8f9fa;">
<h1 class="text-center my-4">MARKET PLACE</h1>
<div class="d-flex justify-content-center">
    <a href="<?= $base_url; ?>" class="btn btn-primary mb-3">Go to Home</a>
</div>


<p class="text-center text-muted">NOTE: IN PLACEHOLDERS YOU CAN FIND INSTRUCTIONS FOR VALID INPUTS</p>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg rounded-3" style="width: 100%; max-width: 450px;">

        <h3 class="text-center mb-3">Create an Account</h3>

        <form id="registerForm" novalidate autocomplete="off">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required minlength="3" autocomplete="off">
                <div class="invalid-feedback">Name must be at least 3 characters.</div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <input type="email" class="form-control" id="email" name="email" placeholder="example@mail.com" required autocomplete="off">
                    <button type="button" class="btn btn-outline-primary" id="verifyEmailBtn" onclick="sendOTP()">Verify</button>
                </div>
                <div class="invalid-feedback">Enter a valid email.</div>
                <small class="text-success d-none" id="emailVerifiedText">Email verified ✔️</small>
            </div>

            <div class="mb-3 d-none" id="otpSection">
                <label for="otp" class="form-label">Verification Code (check your email)</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter 6-digit code" maxlength="6">
                    <button type="button" class="btn btn-outline-success" onclick="verifyOTP()">Verify Code</button>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" class="btn btn-link p-0" onclick="resendOTP()">Resend Code</button>
                    <button type="button" class="btn btn-link p-0" onclick="changeEmail()">Change Email</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="1234567890" required pattern="[0-9]{10,15}" autocomplete="off">
                <div class="invalid-feedback">Enter a valid phone number (10-15 digits).</div>
            </div>

            <div class="mb-3">
                <label for="user_type" class="form-label">Register As</label>
                <select class="form-control" id="user_type" name="user_type" required>
                    <option value="">Select Role</option>
                    <option value="poster">Job Poster</option>
                    <option value="bidder">Bidder</option>
                </select>
                <div class="invalid-feedback">Select a user role.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="********" required minlength="6" autocomplete="new-password">
                <div class="invalid-feedback">Password must be at least 6 characters.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="registerBtn" disabled>Register</button>
            <p class="text-center text-muted small mt-2">NOTE: The Register button will be enabled only if all the fields are valid, verified, and filled.</p>
        </form>

        <!-- Register & Login Links -->
        <div class="mt-3 text-center">
            <p class="mb-1">Already have an account?</p>
            <a href="<?= $base_url; ?>auth/Login" class="btn btn-outline-primary w-100">Login</a>
        </div>
    </div>
</div>


<?php require_once '../../includes/footerlinks.php'; ?>

<script>
let otpSent = false;
let canResend = false;
let resendTimer;

function updateSubmitButton() {
    const isValid = $('#registerForm')[0].checkValidity() && otpSent;
    $('#registerBtn').prop('disabled', !isValid);
}

function setLoading(btn, isLoading) {
    const button = $(btn);
    button.prop('disabled', isLoading);
    button.html(isLoading ? 
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...' : 
        'Verify');
}

function sendOTP() {
    const emailInput = $('#email');
    if (!emailInput[0].checkValidity()) {
        emailInput.addClass('is-invalid');
        return;
    }

    setLoading('#verifyEmailBtn', true);
    
    $.ajax({
        type: "POST",
        url: "../../api/auth/send_otp.php",
        data: { email: emailInput.val() },
        success: function(response) {
    // console.log('Server response:', response);

    // No need to parse, it's already a JSON object
    if (response.status === 'success') {
        otpSent = true;
        emailInput.prop('readonly', true);
        $('#otpSection').removeClass('d-none');
        $('#verifyEmailBtn').addClass('d-none');
        $('#emailVerifiedText').removeClass('d-none');
        startResendTimer();
        Toastify({
            text: "Verification code sent to your email",
            backgroundColor: "#28a745",
            duration: 3000
        }).showToast();
    } else {
        // console.warn('OTP sending failed:', response.message);
        Toastify({
            text: response.message || "Failed to send OTP",
            backgroundColor: "#dc3545",
            duration: 3000
        }).showToast();
    }
    setLoading('#verifyEmailBtn', false);
    updateSubmitButton();
},

        error: function() {
            setLoading('#verifyEmailBtn', false);
            Toastify({
                text: "Error connecting to server",
                backgroundColor: "#dc3545",
                duration: 3000
            }).showToast();
        }
    });
}

function verifyOTP() {
    const otp = $('#otp').val().trim();
    if (otp.length !== 6) {
        Toastify({
            text: "Enter 6-digit code",
            backgroundColor: "#dc3545",
            duration: 3000
        }).showToast();
        return;
    }

    $.ajax({
        type: "POST",
        url: "../../api/auth/verify_otp.php",
        data: { otp: otp },
        success: function(response) {
    // console.log('Server response:', response);

    // No need to parse, response is already a JSON object
    if (response.status === 'success') {
        otpSent = true;
        $('#otpSection').addClass('d-none');
        $('#emailVerifiedText').removeClass('d-none');
        Toastify({
            text: "Email verified successfully!",
            backgroundColor: "#28a745",
            duration: 3000
        }).showToast();
    } else {
        Toastify({
            text: response.message || "Invalid verification code",
            backgroundColor: "#dc3545",
            duration: 3000
        }).showToast();
    }
    updateSubmitButton();
}

    });
}

function resendOTP() {
    if (!canResend) return;

    $.ajax({
        type: "POST",
        url: "../../api/auth/resend_otp.php",
        success: function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                startResendTimer();
                Toastify({
                    text: "New code sent to your email",
                    backgroundColor: "#28a745",
                    duration: 3000
                }).showToast();
            }
        }
    });
}

function changeEmail() {
    otpSent = false;
    $('#email').prop('readonly', false).focus();
    $('#otpSection').addClass('d-none');
    $('#verifyEmailBtn').removeClass('d-none');
    $('#emailVerifiedText').addClass('d-none');
    clearTimeout(resendTimer);
    updateSubmitButton();
}

function startResendTimer() {
    canResend = false;
    let seconds = 30;
    $('[onclick="resendOTP()"]').text(`Resend Code (${seconds})`);
    
    resendTimer = setInterval(() => {
        seconds--;
        $('[onclick="resendOTP()"]').text(`Resend Code (${seconds})`);
        if (seconds <= 0) {
            clearInterval(resendTimer);
            canResend = true;
            $('[onclick="resendOTP()"]').text('Resend Code');
        }
    }, 1000);
}
$(document).ready(function () {
    // Handle form input to reset validation states
    $('#registerForm').on('input', function () {
        $(this).removeClass('was-validated');
        updateSubmitButton();
    });

    // Handle form submission
    $('#registerForm').submit(function (e) {
        e.preventDefault();
        const form = this;

        // Validate form and OTP status
        if (!form.checkValidity() || !otpSent) {
            $(form).addClass('was-validated');
            return;
        }

        // Serialize form data
        const formData = $(form).serialize();

        // AJAX request to register the user
        $.ajax({
            type: "POST",
            url: "../../api/auth/register_process.php",
            data: formData,
            dataType: "json", // Automatically parse the response
            success: function (response) {
                // console.log('Server response:', response);

                if (response.status === "success") {
                    Swal.fire({
                        title: "Registration Successful!",
                        text: "Your account has been created. Click OK to go to the login page.",
                        icon: "success",
                        confirmButtonText: "OK",
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = <?php echo $base_url?>.'auth/Login/';
                        }
                    });
                } else {
                    Toastify({
                        text: response.message || "Registration failed. Please try again.",
                        backgroundColor: "#dc3545",
                        duration: 3000
                    }).showToast();
                }
            },
            error: function (xhr, status, error) {
                // console.error("AJAX Error:", error);
                Toastify({
                    text: "An error occurred. Please try again later.",
                    backgroundColor: "#dc3545",
                    duration: 3000
                }).showToast();
            }
        });
    });
});

</script>
</body>
</html>
