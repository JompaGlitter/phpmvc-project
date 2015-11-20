#Allt om programmering
*- För dig som älskar programmering, i alla former*

This project is the final assignment in the phpmvc course held by Blekinge Tekniska Högskola (BTH). It is a discussion forum built with the [Anax-MVC](https://github.com/mosbth/Anax-MVC "Anax-MVC on GitHub") using the module [CDatabase](https://github.com/mosbth/cdatabase "CDatabase on GitHub") for database connection and [CForm](https://github.com/mosbth/cdatabase "CForm on GitHub") for form generation. The modules are installed into the framwork via dependency injection, so just follow the installation guide below and you should be good to go with your own copy.

##Installation
The guide assumes that you have git and composer installed on your computer. If you dont know what they are, google it! =)

1) First and foremost, clone the project to you computer by entering the following line in your terminal: 
<code>git clone https://github.com/JompaGlitter/phpmvc-project.git</code>

2) Now, enter the project folder and delete the vendor folder. By NOT doing this you prevent the dependency installation done in the next step.

3) Install the dependencies by entering the following line in the terminal:
<code>composer update</code>

4) Now go to the app-->config folder, open the database_mysql.php file and enter the correct database connection details. *DO NOT* use the table_prefix setting as this will prevent the project from working properly.

5) Finally, open your web brower, go to the webroot folder of the project and add 'setup' (i.e. phpmvc_project/webroot/setup) to setup the database tables and populate with som default content. After this you will be automatically redirected to the startpage of the forum.


##Warning
The discussion forum do so far **not support editing existing questions, answers or comments**. So be careful what you right or you will be forced to manually remove the input from the database or use the setup sequence to restore the entire forum to default values.

Use the forum like you use instant chats like IRC, Skype, Facebook Messenger etc. That is with care!
