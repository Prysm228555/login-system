# DB setup for the login system
To create the database, please use this SQL request.  
The name of the database (db) can be changed but it must fit the "db.php" content, be sure this two matches, or you won't be able to create or use any account.  
The table name and attributes cannot be modified without altering the system's operation.
```SQL
CREATE DATABASE db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    mail VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    role TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    desactivated TINYINT(1) UNSIGNED NOT NULL DEFAULT 0
);
```  
<br>

# DB INFO
### id
user id, automatic
<br>

### name, mail, password
user name, mail and password (hashed)
<br>

### remember_token
user's cookie token in case the user want to stay connected (can be null, hashed)
<br>

### role
0 : guest (can use the website, can't change infos)  
1 : normal user (0 + can change his own password and info)  
2 : moderator (1 + can set role to 0 or 1, can desactivate account)  
3 : administrator (2 + can delete passwords, accounts and edit infos)  
4 : root (all rights, use only if necessary)
<br>

### desactivated
0 : activated (user can use his account)  
1 : desactivated (user can't use his account but the account still exist and can be recovered)  
2 : deleted (user can't use his account but the account doesn't exist anymore and can't be recovered)  