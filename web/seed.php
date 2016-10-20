<?php

    $pdo = null;

    $host = 'localhost:8889';
    $dbname = 'account'; 
    $username = "root";
    $password = "root";
    $dsn = "mysql:host=$host;charset=utf8";
    
    $database_url = getenv('CLEARDB_DATABASE_URL');
    if ($database_url) {
        $host = getenv('DATABASE_HOST');
        $dbname = getenv('DATABASE_DBNAME'); 
        $username = getenv('DATABASE_USERNAME');
        $password = getenv('DATABASE_PASSWORD');
        $dsn = $database_url;
    }

    try {
        $pdo = new PDO($dsn, $username, $password);
        $statement = $pdo->prepare("CREATE DATABASE IF NOT EXISTS $dbname;");
        $statement->execute();
        $pdo->query("use $dbname;");
    } catch (PDOException $exception) {
        echo 'Database error: ' . $exception->getMessage();
    }

    // Create the user table if necessary
    $statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS users (
        id int NOT NULL AUTO_INCREMENT,
        name varchar(255),
        email varchar(255),
        avatar_path varchar(255),
        password_hash varchar(32),
        PRIMARY KEY (id)
    );");
    $statement->execute();

    // Add some users if the table is empty
    $statement = $pdo->prepare("SELECT COUNT(*) FROM users;");
    $statement->execute();
    $numUsers = $statement->fetchColumn();
    if ($numUsers == 0) {
        $sql = "INSERT INTO users (name, email, avatar_path, password_hash)
                VALUES (:name, :email, :avatar_path, :password_hash);";
        $statement = $pdo->prepare($sql);

        $statement->bindValue(':name', 'Mark', PDO::PARAM_STR);
        $statement->bindValue(':email', 'mark@facebook.com', PDO::PARAM_STR);
        $statement->bindValue(':avatar_path', '/images/mark.jpg', PDO::PARAM_STR);
        $statement->bindValue(':password_hash', md5('friendface'), PDO::PARAM_STR);
        $statement->execute();

        $statement->bindValue(':name', 'Bill', PDO::PARAM_STR);
        $statement->bindValue(':email', 'bill@microsoft.com', PDO::PARAM_STR);
        $statement->bindValue(':avatar_path', '/images/bill.jpg', PDO::PARAM_STR);
        $statement->bindValue(':password_hash', md5('windows'), PDO::PARAM_STR);
        $statement->execute();

        $statement->bindValue(':name', 'Tim', PDO::PARAM_STR);
        $statement->bindValue(':email', 'tim@web.com', PDO::PARAM_STR);
        $statement->bindValue(':avatar_path', '/images/tim.png', PDO::PARAM_STR);
        $statement->bindValue(':password_hash', md5('www'), PDO::PARAM_STR);
        $statement->execute();

        $statement->bindValue(':name', 'Dennis', PDO::PARAM_STR);
        $statement->bindValue(':email', 'dennis@unix.com', PDO::PARAM_STR);
        $statement->bindValue(':avatar_path', '/images/dennis.jpg', PDO::PARAM_STR);
        $statement->bindValue(':password_hash', md5('c++'), PDO::PARAM_STR);
        $statement->execute();
    }

    // Create the friendship table if necessary
    $statement = $pdo->prepare("CREATE TABLE IF NOT EXISTS friendships (
        sender_id int NOT NULL,
        receiver_id int NOT NULL,
        accepted int(1),
        PRIMARY KEY (sender_id, receiver_id)
    );");
    $statement->execute();

    // Add some friendships if the table is empty
    $statement = $pdo->prepare("SELECT COUNT(*) FROM friendships;");
    $statement->execute();
    $numFriendships = $statement->fetchColumn();
    if ($numFriendships == 0) {
        $sql = "INSERT INTO friendships (sender_id, receiver_id, accepted)
                VALUES (:sender_id, :receiver_id, :accepted);";
        $statement = $pdo->prepare($sql);

        $statement->bindValue(':sender_id', 2, PDO::PARAM_INT);
        $statement->bindValue(':receiver_id', 1, PDO::PARAM_INT);
        $statement->bindValue(':accepted', 0, PDO::PARAM_INT);
        $statement->execute();

        $statement->bindValue(':sender_id', 3, PDO::PARAM_INT);
        $statement->bindValue(':receiver_id', 1, PDO::PARAM_INT);
        $statement->bindValue(':accepted', 0, PDO::PARAM_INT);
        $statement->execute();

        $statement->bindValue(':sender_id', 1, PDO::PARAM_INT);
        $statement->bindValue(':receiver_id', 4, PDO::PARAM_INT);
        $statement->bindValue(':accepted', 1, PDO::PARAM_INT);
        $statement->execute();
    }
?>