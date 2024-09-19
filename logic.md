# Business logic
## Introduction:
Electrónica Don Pepe is a small electronics retail business. It has three stores, a warehouse and a few employees.

## Problem description:
Salespeople are paid a base amount and a bonus calculated based on the sales during each month. Bonuses are calculated based on the following criteria
* Commissions
    * Sales comission
    * Product commission
* Awards
    * Best salesman in a month
    * Best salesman in a product campaign

At the moment, paychecks are calculated like this:
* Once a sale is completed, the cashier leaves a copy of the receipt in the corresponding salesman's box.
* At the end of the month, Accounting counts the sales, calculates commissions and awards.
* Once those are calculated, Accounting enters them in the payroll system, so they can be added to salespeople's paycheck as a bonus.

This is a well known cause of compaints among salespeople, as cashiers can easily misplace receipts by accident. On the other side, employees in Accounting have stated in the past that manually calculating bonuses is a grating and repetitive task.

## Main goal
Automate as much as possible bonus calculation and avoid mistakes and complaints.

## Commissions and awards
### How to calculate

**Sales commission:** A plus based on the amount of sales completed during the month. The rules are:

    Between 1 and 5 sales => $ 200
    Between 6 and 10 sales => $ 400
    Between 11 and 15 sales => $ 700
    More than 15 sales => $1000
    
(Eg. : Salesman X has 14 sales  => Sales commission = $700)

**Product commission:** A plus given to every salesman that sold a product in particular. Is calculated according to the following:

    Product units sold * fixed amount

Eg.: 

    * Salesman X sold 10 units of "Product Z"
    * Each sale of "Product Z" gets $6
    ==> Product commission for "Product Z" is 10 * $ 6 = $ 60

**Best salesman of the month:** The salesman with the most sales is given an aditional of $2000.

Eg.:

    * Salesman X: 10 sales
    * Salesman Y: 6 sales
    * Salesman Z: 15 sales
    ==> Salesman Z gets $2000

**Best salesman of a campaign:** The salesman with the most sales of a product in particular is given an aditional of $1000.

Eg.: 

    * Salesman X: 10 sales
    * Salesman Y: 6 sales
    * Salesman Z: 15 sales
    ==> Salesman Z gets $1000

## Other rules
* Commissions apply to all salespeople.
* Awards only apply to the salesman that meets the specific criteria.
* A salesman can be win all commissions and awards, some of them, or none.
* Aditional amounts can be changed, but previous bonus calculations can't be fixed retroactivly.
