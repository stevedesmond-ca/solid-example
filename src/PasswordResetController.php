<?php

require_once __DIR__ . '/Controller.php';

class PasswordResetController extends Controller
{
    public function get()
    {
        $view_model['message'] = $_GET['staff']
        ? 'Enter the username whose password to reset.'
        : 'Please complete the form to reset your password.';

        return $this->view('form.php', $view_model);
    }

    public function post()
    {
        $db = mysqli_connect('localhost', 'user', null, 'app');
        $result = mysqli_query($db, "SELECT * FROM users WHERE username='{$_POST['username']}'");
        $user = $result->fetch_assoc();
        $token = md5(rand());
        mysqli_query($db, "INSERT INTO password_reset_tokens (username, token, created) VALUES ('{$user['username']}', '$token' NOW());");
        $body = 'Please <a href="/reset_password.php?token=' . $token . '">click here</a> to reset your password.';
        if ($_GET['staff']) {
            $body = "A password reset was requested on your behalf. $body";
        }

        mail($user['email'], 'Password Reset', $body);
        return $this->view('sent.php');
    }
}
