<?php
session_start();
error_reporting(E_ERROR | E_PARSE);

include_once '/home/aky/public_html/styler/init.php';
$tpl = file_get_contents('sign-in.html'); // Load login page template

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim(strtolower($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $errors[] = 'All fields must be filled in.';
    }
    


    // Retrieve user from database and check if user exists
    $user = fetch("SELECT user_id, password FROM users WHERE (username = '".addslashes($username)."' OR email = '".addslashes($username)."') LIMIT 1");
    if (!$user['user_id']) {
        $errors[] = 'User could not be found';        
    }


    // check password
    if ($user && !password_verify($password, $user['password'])) {
        $errors[] = 'Incorrect username or password';    
    }
    

    $user_id = $user['user_id'];
    if(empty($errors)){
        // successfull
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user_id;
        header("Location: /styler/index.php"); // Redirect to homepage
    }
}
// error management
$error_html = '';
if(!empty($errors)){
    $error_html .= '<div class="alert">';
    foreach($errors as $error){
        $error_html .= '<div>'.$error.'</div>';
    }
    $error_html .= '</div>';
}
$tpl = str_replace("{error_messages}", $error_html, $tpl);

$errors[] = $_SESSION['wardrobe_error'];



tpl_output($tpl); // Output the template
?>