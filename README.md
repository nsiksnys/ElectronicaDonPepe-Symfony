# ElectronicaDonPepe-Symfony
Advanced Programming II final project, ported to Symfony 7.

This project also uses SQLite and Boostrap 5.3.

## Bussiness logic
For more information regarding the project, please read the logic.md file

## Composer packages
Run `composer install` to install all required packages

## Environment variables
All envirnoment variables are managed using a .env file. In this case, we need to provide the url for the SQLite database, which by default looks like this

    DATABASE_URL="sqlite:///%kernel.project_dir%/var/app.db"

## Test data
Run `php bin/console make:migration` to create a database with test data.

## Running locally
For local development, we can use the [Symfony binary](https://symfony.com/download) to run the local server.
Once the binary is installed, run `symfony server:start`

## Screenshots
* Home page
![Home page](/screenshots/Home.png)

* Sales
    * List
    ![Sales list](/screenshots/Sales%20-%20list.png)

    * Add sale form
    ![Sales add](/screenshots/Sales%20-%20add.png)

* Campaigns
![Campaigns list](/screenshots/Campaigns%20-%20list.png)

* Amounts
![Amounts list](/screenshots/Amounts.png)

* Bonuses
    * List
    ![Bonuses list](/screenshots/Bonuses%20-%20list.png)

    * Details
    ![Bonuses details](/screenshots/Bonus%20-%20details.png)