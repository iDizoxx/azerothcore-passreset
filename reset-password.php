<?php
ob_start();

require 'conf/config.php';
require 'assets/utilities/srp6_utils.php';
require_once 'assets/func/functions.php';

function redirectTo($url, $message = '') {
    if (!empty($message)) {
        $url .= '?message=' . urlencode($message);
    }
    header("Location: $url");
    exit;
}

if (!isset($_GET['token']) || empty($_GET['token'])) {
    redirectTo('reset-password-request.php', 'Token missing.');
}

$token = $_GET['token'];
$escapedToken = hash('sha256', $conn->real_escape_string($token)); // Hash token before using in SQL

$sql = "SELECT token, expires_at FROM password_resets WHERE token = ? AND expires_at > NOW()";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    redirectTo('reset-password-request.php', 'Database error.');
}
$stmt->bind_param('s', $escapedToken);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    redirectTo('reset-password-request.php', 'Invalid or expired token.');
}

function UpdatePassword($token, $newPassword) {
    global $conn;

    $escapedToken = hash('sha256', $conn->real_escape_string($token)); // Hash token before using in SQL

    $sql = "SELECT pr.user_id, a.username 
            FROM password_resets pr
            JOIN account a ON pr.user_id = a.id
            WHERE pr.token = ? AND pr.expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 'Error preparing SQL statement for token validation';
    }
    $stmt->bind_param('s', $escapedToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $userId = $row['user_id'];
        $username = $row['username'];

        try {
            $salt = random_bytes(32);

            if (strlen($salt) !== 32) {
                return 'Error: Salt is not 32 bytes in length';
            }

            $verifier = CalculateSRP6Verifier($username, $newPassword, $salt);

            if (strlen($verifier) !== 32) {
                return 'Error: Verifier is not 32 bytes in length';
            }

        } catch (Exception $e) {
            return 'Error generating salt or verifier';
        }
        
        $escapedSalt = $salt;
        $escapedVerifier = $verifier;

        $sql = "UPDATE account 
                SET salt = ?, verifier = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return 'Error preparing statement for password update';
        }
        $stmt->bind_param('ssi', $escapedSalt, $escapedVerifier, $userId);

        if ($stmt->execute()) {
            $sql = "DELETE FROM password_resets WHERE token = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return 'Error preparing statement to delete token';
            }
            $stmt->bind_param('s', $escapedToken);
            $stmt->execute();
            return true;
        } else {
            return 'Error executing password update';
        }
    } else {
        return 'Token not valid or user not found';
    }

    return 'An unknown error occurred during password reset';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($newPassword)) {
        $responseMessage = 'Password cannot be empty.';
        $responseClass = 'error-reg';
    } elseif ($newPassword !== $confirmPassword) {
        $responseMessage = 'Passwords do not match.';
        $responseClass = 'error-reg';
    } else {
        $result = UpdatePassword($token, $newPassword);

        if ($result === true) {
            $responseMessage = 'Password has been successfully reset.';
            $responseClass = 'success-reg';
        } else {
            $responseMessage = $result;
            $responseClass = 'error-reg';
        }
    }
} else {
    $responseMessage = '';
    $responseClass = '';
}

ob_end_clean();
?>

<?php 
    includeCss();
?>

<div class="container-account">
    <div class="row-account form-login-account">
        <h2>Reset Your Password</h2>
        
        <form id="resetPasswordForm" method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <label for="new_password">Enter new password:</label><br>
            <input type="password" name="new_password" id="new_password" required><br><br>

            <label for="confirm_password">Re-enter new password:</label><br>
            <input type="password" name="confirm_password" id="confirm_password" required><br><br>

            <input type="submit" value="Reset Password">
        </form>

        <?php if ($responseMessage): ?>
            <div id="responseMessage" class="<?php echo $responseClass; ?>">
                <?php echo htmlspecialchars($responseMessage); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
