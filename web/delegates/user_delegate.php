<?php
    
    function get_user($id) {
        require __DIR__.'/../seed.php';
        
        $statement = $pdo->prepare("SELECT * FROM users WHERE id=:id;");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->fetch();
    }

    function update_user_name($id, $name) {
        require __DIR__.'/../seed.php';
        
        $statement = $pdo->prepare("UPDATE users SET name=:name WHERE id=:id;");
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    function search_for_users($search) {
        require __DIR__.'/../seed.php';
        
        $search_with_wildcards = '%'.$search.'%';
        
        $statement = $pdo->prepare("SELECT * FROM users WHERE name LIKE :search;");
        $statement->bindValue(':search', $search_with_wildcards, PDO::PARAM_STR);
        $statement->execute();
        
        return $statement->fetchAll();
    }

?>