# we-news

**Sample news portal written in Symfony 4**  
  
Installation  
  
1. Open terminal and clone this repository to desired folder **$ git clone https://github.com/omerimuni/we-news.git**  
2. Create mysql database named news with this command **$ php bin/console doctrine:database:create**
3. Run server by following command **$ php bin/console server:start**  
4. Create tables **$ php bin/console make:migration** and then **$ php bin/console doctrine:migrations:migrate** 
5. Fill tables with data **$ php bin/console doctrine:fixtures:load**  
6. Visit http://localhost to see project  
7. To add and manage news go http://localhost/editor and use **admin@admin.com** for username and **password** for password  
8. To send news statistics to desired email, setup cron to call following command **$ php bin/console statistics YOUR@EMAIL.ADDRESS**
