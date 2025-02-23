<?php
    try{
        $username = getenv("POSTGRES_USER");
        $password = getenv("POSTGRES_PASSWORD");
        $db_name = getenv("POSTGRES_DB");
        $port = 5432;
        $dsn = "pgsql:host=db;port=$port;dbname=$db_name";
        $db = new PDO($dsn, $username, $password);
    }catch(PDOException $e){
        echo "Connection failed";
    }
?>