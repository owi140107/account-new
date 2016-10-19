<?php
    
    function get_friend_request_users($user_id) {
        require __DIR__.'/../seed.php';
        
        $sql = "SELECT users.*
                FROM users INNER JOIN friendships
                ON users.id = friendships.sender_id
                WHERE friendships.receiver_id = :receiver_id
                AND friendships.accepted = 0;";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':receiver_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        
        $result = $statement->fetchAll();
        
        return $result;
    }

    function get_friend_users($user_id) {
        require __DIR__.'/../seed.php';
        
        // Friends gained by accepting a request
        $sql = "SELECT users.*
                FROM users INNER JOIN friendships
                ON users.id = friendships.sender_id
                WHERE friendships.receiver_id = :receiver_id
                AND friendships.accepted = 1;";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':receiver_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $accept_result = $statement->fetchAll();
        
        // Friends gained by sending a request
        $sql = "SELECT users.*
                FROM users INNER JOIN friendships
                ON users.id = friendships.receiver_id
                WHERE friendships.sender_id = :sender_id
                AND friendships.accepted = 1;";
        $statement = $pdo->prepare($sql);
        $statement->bindValue(':sender_id', $user_id, PDO::PARAM_INT);
        $statement->execute();
        $send_result = $statement->fetchAll();
        
        return array_merge($accept_result, $send_result);
    }

    function accept_friend_request($user_id, $friend_id) {
        require __DIR__.'/../seed.php';
        
        $statement = $pdo->prepare("UPDATE friendships
                                    SET accepted = 1
                                    WHERE receiver_id = :receiver_id
                                    AND sender_id = :sender_id");
        $statement->bindValue(':receiver_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':sender_id', $friend_id, PDO::PARAM_INT);
        $statement->execute();
    }

    function delete_friend_request($user_id, $friend_id) {
        require __DIR__.'/../seed.php';
        
        $statement = $pdo->prepare("DELETE FROM friendships
                                    WHERE receiver_id = :receiver_id
                                    AND sender_id = :sender_id");
        $statement->bindValue(':receiver_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':sender_id', $friend_id, PDO::PARAM_INT);
        $statement->execute();
    }

?>