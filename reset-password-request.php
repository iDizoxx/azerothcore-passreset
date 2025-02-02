<?php
ob_start();

require 'conf/config.php';
require_once 'assets/func/functions.php';

function CreatePasswordResetToken($email, $username) {
    global $conn;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $escapedEmail = $conn->real_escape_string($email);
    $escapedUsername = $conn->real_escape_string($username);

    $stmt = $conn->prepare("SELECT id FROM account WHERE email = ? AND username = ?");
    $stmt->bind_param("ss", $escapedEmail, $escapedUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $userId = $result->fetch_assoc()['id'];
        $token = bin2hex(random_bytes(32)); // Generate a secure token
        $expiry = time() + 900; // 15 minutes token active

        $hashedToken = hash('sha256', $token);
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) 
                               VALUES (?, ?, FROM_UNIXTIME(?))");
        $stmt->bind_param("iss", $userId, $hashedToken, $expiry);
        if ($stmt->execute()) {
            return $token;
        }
    }

    return false;
}

// CSRF Token Generation and Validation 
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token if not already set
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token mismatch');
    }

    $email = $_POST['email'];
    $username = $_POST['username'];
    $token = CreatePasswordResetToken($email, $username);

    if ($token) {
        $domain = $_SERVER['HTTP_HOST'];
        $resetLink = "https://" . $domain . "/reset-password.php?token=$token";

        $subject = "Password Reset Request for Your Account";
        $body = "
            <html>
            <head>
                <title>Password Reset Request</title>
            </head>
            <body>
                <h2>Password Reset Request</h2>
                <p>Hi " . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . ",</p>
                <p>We received a request to reset your password. If you made this request, you can reset your password by clicking the link below:</p>
                <p><a href='$resetLink' style='color: #4CAF50;'>Reset your password</a></p>
                <p>If you didn't request this, please ignore this email.</p>
                <p>Best regards,</p>
                <p>Your lovely team</p>
            </body>
            </html>
        ";

        // Set headers for HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // Use PHPMailer or a similar library for sending emails in a production environment
        if (mail($email, $subject, $body, $headers)) {
            $response = ['message' => 'Password reset email sent.', 'class' => 'success-reg'];
        } else {
            $response = ['message' => 'Error sending email. Please try again.', 'class' => 'error-reg'];
        }
    } else {
        $response = ['message' => 'No account found with the provided email and username.', 'class' => 'error-reg'];
    }

    // Send JSON response
    echo json_encode($response);
    exit;
}

?>

<?php 
    includeCss();
?>

<section>
    <div class="container-account">
        <div class="row-account form-login-account">
            <h2>Request Password Reset</h2>

            <form id="resetRequestForm" method="post">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <label for="email">Enter your email:</label><br>
                <input type="email" name="email" id="email" required><br><br>

                <label for="username">Enter your username:</label><br>
                <input type="text" name="username" id="username" required><br><br>

                <input type="submit" class="btn-kb" value="Send Reset Link">
            </form>
            <div id="responseMessage"></div>
        </div>
        <div class="info-box-msg">Forgot your password? No problem! Enter your email address, and we'll send you a link to reset your password.</div>
    </div>
    <?php jqeryGet(); ?>

    <script>
        $(document).ready(function () {
            $('#resetRequestForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: 'reset-password-request.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        $('#responseMessage').text(response.message).attr('class', response.class);
                    },
                    error: function () {
                        $('#responseMessage').text('An error occurred. Please try again.').attr('class', 'error-reg');
                    }
                });
            });
        });
    </script>
</section>

<?php
ob_end_flush();
?>
