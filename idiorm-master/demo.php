<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
    // ------------------- //
    // --- Idiorm Demo --- //
    // ------------------- //

    // Note: This is just about the simplest database-driven webapp it's possible to create
    // and is designed only for the purpose of demonstrating how Idiorm works.

    // In case it's not obvious: this is not the correct way to build web applications!

    // Require the idiorm file
    require_once("idiorm.php");

    // Connect to the demo database file
    ORM::configure('mysql:host=127.0.0.1;dbname=smartarenda');

    // This grabs the raw database connection from the ORM
    // class and creates the table if it doesn't already exist.
    // Wouldn't normally be needed if the table is already there.
    $db = ORM::get_db();
//    $db->exec("
//        CREATE TABLE IF NOT EXISTS contact (
//            id INTEGER PRIMARY KEY,
//            name TEXT,
//            email TEXT
//        );"
//    );
$sql = "SELECT * FROM users";
var_dump($db->query($sql)->fetchAll());