# azerothcore-passreset
Password reset for azeroth core.


AzerothCore WoW WotLK Password Reset Integration
This script provides a password reset feature for AzerothCore-based WoW WotLK servers. It generates a time-sensitive (15-minute) reset token, saves it to the database, and sends a password reset link to the player's email. The system works even if multiple accounts are registered with the same email but different usernames.

Requirements:
AzerothCore (WoW WotLK Server)
PHP 7.x or higher
MySQL/MariaDB for the acore_auth database
Access to modify your server's database and configuration files
Steps to Install:
1. Import the SQL File:
Find the SQL file in the sql folder of this package.
Import the file into your acore_auth database.
Important: Make sure to import the SQL file into the correct database (i.e., acore_auth). This will set up the necessary tables and fields for the password reset feature.

You can import the SQL file using a MySQL client like phpMyAdmin or command line:

sql
Copy
Edit
mysql -u username -p acore_auth < path_to_sql_file.sql
Replace username with your MySQL username and path_to_sql_file.sql with the actual path to the SQL file.

2. Configure the config.php File:
Go to the conf/config.php file.
Modify the configuration settings:
php
Copy
Edit
<?php
$host = 'your_database_host';  // Database host (e.g., localhost)
$db_username = 'your_db_username'; // Database username
$db_password = 'your_db_password'; // Database password
$db_name = 'acore_auth'; // Database name
Replace your_database_host, your_db_username, and your_db_password with the correct values for your server.
3. Update Your AzerothCore Server:
Ensure that your server is using the updated database schema, especially if you're enabling multiple accounts per email. Your server should already be capable of handling multiple usernames linked to the same email. If not, make the necessary adjustments in the code.
4. Test the Password Reset System:
After completing the setup, test the system to ensure it works as expected:
Generate a reset token and check if the email is received with the reset link.
Ensure the link expires after 15 minutes as intended.
Troubleshooting:
Email Not Sent: Ensure your server has a working mail setup (e.g., SMTP settings).
Database Issues: If the SQL import fails, ensure you have the correct permissions for your database user.
Token Expiry Issues: If the reset link doesn't expire correctly, review the token expiry time logic in the code.
This should get your password reset functionality up and running! If you need more advanced customizations, feel free to tweak the code as needed.
