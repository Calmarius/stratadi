# StraTaDi: Strategy, Tactics and Dimplomacy

**ABOUT**

It's a browser game made in my college years (in 2009). 
While I made several changes recently (like ditching mysql in favor of mysqli, and made SQL usage a bit safer), 
the code would need a major refactor to be a good code.
Nowadays I would do many things different. Do not judge my current abilities from this code. 

**INSTALLATION**

If you want to install and actually play this stuff,  
you will need a PHP and MySQL enabled webserver. (LAMP or XAMPP server).

Then do the following:

1. Dump the contents of this repo to your webserver. 
2. Set the database passwords and admin e-mail in the shadow.php
3. Visit install.php (it should create all the tables the game needs).
4. Now read the configuration.php there you can tweak pretty much everything. 

Fields you should set in configuration.php:
- adminMail
- adminName
- closed to false
- facebook related things (if you want a fanpage)
- gameStarted to the date and time you would like to start the game.
- serverLanguage (hu or en as these are two languages supported).
- serverSpeed (set it to 1 for a normal server.)
- timezone (UTC + bias of the server)

If everything went well you should be able to register yourself into the game.
Then you can give yourself an admin permission by updating the wtfb2_accesses table.

**LICENSE**

Do whatever you want with this code. But attribution is highly recommended.
