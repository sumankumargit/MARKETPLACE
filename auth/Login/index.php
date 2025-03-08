

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
    <title>Login</title>
    <?php require_once '../../includes/headlinks.php'; ?>
</head>
<body style="background-color: #f8f9fa;">

<h1 class="text-center mb-4">MARKET PLACE</h1>
<div class="text-center mb-4">
    <a href="<?= $base_url; ?>" class="btn btn-primary">Go to Home</a>
</div>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    
    <div class="card p-4 shadow-lg" style="width: 400px; border-radius: 12px;">
        <h2 class="text-center mb-4">Login</h2>

        <form id="loginForm" novalidate>
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" class="form-control rounded" id="email" name="email" required>
            </div>

            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control rounded" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded">Login</button>

            <!-- Register Link -->
            <div class="mt-3 text-center">
            <p class="mb-1">Dont have an account?</p>

                <a href="<?= $base_url; ?>auth/register" class="btn btn-secondary w-100 rounded">Register</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../includes/footerlinks.php'; ?>

<script>
   $(document).ready(function () {
    $('#loginForm').submit(function (e) {
        e.preventDefault();
        
        const form = this;
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        const formData = $(form).serialize();
        const loginButton = $('button[type="submit"]');

        // Show loading state
        loginButton.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Logging in...
        `);

        $.ajax({
            type: "POST",
            url: "../../api/auth/login.php",
            data: formData,
            dataType: "json",
            success: function (response) {
                // console.log('Server response:', response);

                if (response.status === "success") {
                    Swal.fire({
                        title: "Login Successful!",
                        text: "Welcome back! Redirecting to your dashboard...",
                        icon: "success",
                        confirmButtonText: "OK",
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?php echo $base_url ?>';
                        }
                    });
                } else {
                    Toastify({
                        text: response.message || "Login failed. Please try again.",
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
            },
            complete: function () {
                // Reset button state after request completes
                loginButton.prop('disabled', false).html('Login');
            }
        });
    });
});

</script>

</body>
</html>
