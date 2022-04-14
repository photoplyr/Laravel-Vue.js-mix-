# veritap
Total Gym Health Analytic CRM

This project was built by laravel 7.0 framework (laravel+vue.js stack)
Required:  php v8.0 +, pgsql, npm, composer

# How to install
1. download source code
2. run command:  composer install
3. run command:  npm install
4. set .env for db connection
5. import postgres-202202140618.sql to your db
6. run command:  php artisan serve
7. run command:  npm run watch
8. login account: 
    root:    chanthai@aabocrm.com  pwd: 123456789
    wellness member:  dmytro@aabocrm.com  pwd: Admin
# Test Task
Create Dashboard for Corporate Wellness Member
This is the new landing page for the Corporate Wellness Member. We are repurposing the code from the Member tab for insurance with some changes to the design.
Now when club_member user login, it redirects to activity/calendar dashbarod page.
https://prnt.sc/26vosmg
If loggedin user is corp_wellness, he should be in the same page but we need some changes to the design
https://prnt.sc/26vou1s
