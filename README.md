# php-shopping

This is a php project meant as a mandatory assignment. The idea was a simple (code-wise) e-commerce website. The time(or the lack of it), made it a requirement to make things work in a non-optimal way. This is the reason for the code to have an object oriented aproach but in some pages using built-in scripting.

## Instructions

1. Import Database
Make sure to import the products.sql to a mysql database engine. After that, make sure it is still running

2. Open Apache server
Open an apache server in this repository root, with command 
 > php -S [host]:[port]
example:
 > php -S localhost:8000 
3. Go to [host]:[port]/index.php
If you navigate to [host]:[port]/index.php you should be redirected to login page (if you are not logged in). Register and login with your new account. Don't use a real password, since passwords are not encrypted in database.

To become an admin, go to your database, users table and change 'is_admin' field to 1.