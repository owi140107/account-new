<?php
    
    function get_user_by_email($email) {
        require __DIR__.'/../seed.php';
        
        $statement = $pdo->prepare("SELECT * FROM users WHERE email=:email;");
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll();
        
        if (count($result) == 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    function correct_password_for_user($user, $password) {
        return ($user != null && md5($password) == $user['password_hash']);
    }

?>