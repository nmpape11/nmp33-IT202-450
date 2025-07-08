<?php
require(__DIR__ . "/../../partials/nav.php");
$email = "";
$username = "";

?>

<script>
    function validate(form) {
        const email = form.email.value.trim();
        const username = form.username.value.trim();
        const password = form.password.value.trim();
        const confirm = form.confirm.value.trim();

        let valid = true;
        const flashDiv = document.getElementById("flash");
        if (flashDiv) flashDiv.innerHTML = "";

        // Email format
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailPattern.test(email)) {
            flash("Valid email required.", "danger");
            valid = false;
        }

        // Username check
        const usernamePattern = /^[a-z0-9_-]+$/;
        if (!username || !usernamePattern.test(username)) {
            flash("Username must be lowercase, alphanumerical, and can only contain _ or -", "danger");
            valid = false;
        }

        // Password length
        if (!password || password.length < 8) {
            flash("Password must be at least 8 characters.", "danger");
            valid = false;
        }

        // Confirm match
        if (password !== confirm) {
            flash("Passwords do not match.", "danger");
            valid = false;
        }

        return valid;
    }
</script>
<?php
if (isset($_POST["email"], $_POST["password"], $_POST["confirm"], $_POST["username"])) {

    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se($_POST, "confirm", "", false);
    $username = se($_POST, "username", "", false);
    // TODO 3: validate/use
    $hasError = false;

    if (empty($email)) {
        flash("Email must not be empty.", "danger");
        $hasError = true;
    }
    // Sanitize and validate email
    $email = sanitize_email($email);
    if (!is_valid_email($email)) {
        flash("Invalid email address.", "danger");
        $hasError = true;
    }
    if (!is_valid_username($username)) {
        flash("Username must be lowercase, alphanumerical, and can only contain _ or -", "danger");
        $hasError = true;
    }
    if (empty($password)) {
        flash("Password must not be empty.", "danger");
        $hasError = true;
    }

    if (empty($confirm)) {
        flash("Confirm password must not be empty.", "danger");
        $hasError = true;
    }

    if (!is_valid_password($password)) {
        flash("Password must be at least 8 characters long.", "danger");
        $hasError = true;
    }

    if (!is_valid_confirm($password, $confirm)) {
        flash("Passwords must match.", "danger");
        $hasError = true;
    }

    if (!$hasError) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB(); // available due to the `require()` of `functions.php`
        // Code for inserting user data into the database
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES (:email, :password, :username)");
        try {
            $stmt->execute([':email' => $email, ':password' => $hashed_password, ':username' => $username]);
   
            flash("Successfully registered! You can now log in.", "success");
        } catch(PDOException $e) {
            // Handle duplicate email/username
            users_check_duplicate($e);
        }
        catch (Exception $e) {
            flash("There was an error registering. Please try again.", "danger");
            error_log("Registration Error: " . var_export($e, true)); // log the technical error for debugging
        }
    }
}
?>

<h3>Register</h3>
<form onsubmit="return validate(this)" method="POST">
    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="<?php se($email); ?>" required />
    </div>
    <div>
        <label for="username">Username</label>
        <input type="text" name="username" value="<?php se($username); ?>" required maxlength="30" />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <input type="submit" value="Register" />
</form>

<?php
require(__DIR__ . "/../../partials/flash.php");
reset_session();
?>