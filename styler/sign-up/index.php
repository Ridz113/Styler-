<?php
session_start();
error_reporting(E_ERROR | E_PARSE);

include_once '/home/aky/public_html/styler/init.php';
$tpl = file_get_contents('sign-up.html'); // Load register page template

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(strtolower($_POST['username']));
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($email)) {
        $errors[] = 'All fields must be filled in.';
    }

    // Check if email already exists
    $user = fetch("SELECT email FROM users WHERE email = '".addslashes($email)."' LIMIT 1");
    if ($user['email']) {
        $errors[] = 'An account with this email address already exists';        
    }

    // Check if user already exists
    $user = fetch("SELECT username FROM users WHERE username = '".addslashes($username)."' LIMIT 1");
    if ($user['username']) {
        $errors[] = 'This username address already exists, please choose another';        
    }

    if(empty($errors)){
        //success

        // sanitise variables
        $username_slashed = addslashes($username);
        $email_slashed = addslashes($email);

        // CREATE USER
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        sql("INSERT INTO users SET username='".$username_slashed."', password='".addslashes($hashed_password)."', email='".$email_slashed."'");
        $user_id = insert_id();

        // CREATE WARDROBE
        sql("INSERT INTO wardrobes SET username='".$username_slashed."',user_id='".intval($user_id)."'");
        $wardrobe_id = insert_id();

        // Store user data in session
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;

        // Redirect to home page
        header("Location: /styler/index.php");
    }
}

// error management
$error_html = '';
if($errors){
    $error_html .= '<div class="alert">';
    foreach($errors as $error){
        $error_html .= '<div>'.$error.'</div>';
    }
    $error_html .= '</div>';
}
$tpl = str_replace("{error_messages}", $error_html, $tpl);

tpl_output($tpl); // Output the template
?>