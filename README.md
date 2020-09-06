![Console example](/assets/console.png?raw=true "Console example")

# Exadsrcise
> Candidate Exercise - PHP Developer, for **_Exads_**
> 
> Solved by [Francisco Carvalho](http://francisco-carvalho.eu)


Table of contents
=================

<!--ts-->
   * [Architecture](#architecture)
   * [Usage](#usage)
   * [Solution](#solution)
        * [1. FizzBuzz](#1-fizzbuzz)
        * [2. 500 Element Array](#2-500-element-array)
        * [3. Database Connectivity](#3-database-connectivity)
        * [4. Lottery](#4-lottery)
        * [5. A/B Testing](#5-ab-testing)
   * [Improvements](#improvements)
<!--te-->

Architecture
============
The current solution was implemented using [DDD approach](https://martinfowler.com/bliki/DomainDrivenDesign.html).
> **Domain-driven design (DDD)** is the concept that the structure and language of software code (class names, class methods, class variables) should match the business domain.

However, every logic behind required solution for this Exercise is under `Domain` folder (for all exercises except [3. Database Connectivity](#3-database-connectivity) - this is preserved under `Persistency` directory).
> #### NOTE
> DB connection requires the Database configuration under `.env` file. We have a copy of that file under the root directory (named `.env.example`) - just copy it and fill with right database configuration.

Usage
=====
This solution uses composer as a package manager.
To run it locally, you'll need at least `PHP 7.4` (the code has some [_typed properties_](https://stitcher.io/blog/typed-properties-in-php-74)) and `composer` installed.
After check it, you can run:
```bash
➥ cd /ROOT_DIR_OF_PROJECT
➥ composer install
```


I also used `symfony/console` package in order to build Console Application (in order to execute code easily).

Console commands are under `Exadsrcise\Application\Commands` namespace and only handles the structure of the console command (string description/help, arguments/options) and the logic execution.

In order to execute the program, the user should:
```bash
➥ cd /ROOT_DIR_OF_PROJECT
➥ php exads
```

Every command could be executed under `php exads run:command`.
```bash
➥ cd /ROOT_DIR_OF_PROJECT
➥ php exads run:fizz-buzz 1 500
Fizz
Fizz
Buzz
Fizz
Fizz
...
```

User could check all the commands doing
```bash
➥ cd /ROOT_DIR_OF_PROJECT
➥ php exads list
```

Every command has an `--help` option. This gives the information about command usage.
```bash
➥ cd /ROOT_DIR_OF_PROJECT
➥ php exads run:fizz-buzz --help
Description:
  Write a PHP script that prints all integer values from min to max.

Usage:
  run:fizz-buzz <min> <max>

Arguments:
  min                   Min Number
  max                   Max Number

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Write "Fizz" in multiple of three and "Buzz" for the multiples of five.Values which are multiples of both three and five should output as "FizzBuzz".
```

Solution
========

#1. FizzBuzz
============
> Write a PHP script that prints all integer values from 1 to 100.
> 
> For multiples of three output "Fizz" instead of the value and for the multiples of five output "Buzz". Values which are multiples of both three and five should output as "FizzBuzz".

I created the solution under `Exadsrcise\Domain\FizzBuzz\NumberRangeChecker` class.
```php
use Exadsrcise\Domain\FizzBuzz\NumberRangeChecker;

$numberRangeChecker = new NumberRangeChecker(1, 500);
// Iterate over \Generator
foreach ($numberRangeChecker->fetch() as $number)
            echo $number->output();
```
`NumberRangeChecker` has a method `fetch` that returns a [Generator](https://www.php.net/manual/en/class.generator.php) - this way, we improve the memory used bu the application, since **we do not need to create the array** (keeping it in memory) only to write the desired output.


#2. 500 Element Array
=====================
> Write a PHP script to generate a random array of 500 integers (values of 1 – 500 inclusive). Randomly remove and discard an arbitary element from this newly generated array.
>
> Write the code to efficiently determine the value of the missing element. Explain your reasoning with comments.

Assuming we have an unordered array with random repeated numbers, we can solve this using one of two ways:
1. Append removed item to a local array - fastest way, do not need to iterate the array;
2. Generate an ordered array and check natively with `array_diff function.
 
 I crated the solution under `Exadsrcise\Domain\ElementArray\IntegerGenerator` class. Some code is not being used in order to work (using solution **1**).

#3. Database Connectivity
=========================
The solution of this exercise is under `Infrastructure/DB` class - this is a simple class to abstract both connection and Query (`SELECT` and `INSERT`). It's been used internally in `Exadsrcise\Persistency\Repositories\TestRepository` class (Repository pattern).
> #### NOTE
> DB class is using .env configuration file to get database connection configuration. Please configure it before use this command, otherwise it will fail.

**Take into consideration:**
- Fluent API - it enables chaining method
```php
use Exadsrcise\Infrastructure\DB;

// Iterate internally
DB::instance()->selectFrom("exads_test")->all(function($row){
    echo $row['name'];
});

// Save rows in memory
$rows = DB::instance()->selectFrom("exads_test")->all();
```
- Enables multiple row insert
```php
use Exadsrcise\Infrastructure\DB;

// Insert single row
DB::instance()->insertRow('exads_test', ['name', 'age', 'job_title'], ['Francisco', 28, 'Software Developer']);

// Insert multiple rows in a single statement
$rows = [
    ['Francisco', 28, 'Software Developer'],
    ['Elon Must', 49, 'Entrepreneur']
];
DB::instance()->insertRow('exads_test', ['name', 'age', 'job_title'], $rows);
```

#4. Lottery
===========
I've created a class `Exadsrcise\Domain\Datetime\DatetimeConverter` that abstracts the calculation of next date based on given week day.
```php
use Exadsrcise\Domain\Datetime\DatetimeConverter;
$converter = new DatetimeConverter();

$tuesday = $converter->getNextDateOfWeekDay('friday');
echo "The weekend is not so far, is on the next day '{$tuesday->format('Y-m-d')}'";
```
**It enables:**
- different timezones (given on constructor)
- optionally supplied date and time.

#5. A/B Testing
===============
The class `Exadsrcise\Domain\Promotion\PromotionSelector` was created to abstract the promotion selection based on its percentage.
```php
use Exadsrcise\Domain\Promotion\PromotionSelector;
use Exadsrcise\Model\Promotion;

$promotionPicker = new PromotionSelector();

$promotionPicker->pushPromotion(new Promotion("Design no. 1", 50));
$promotionPicker->pushPromotion(new Promotion("Design no. 2", 25));
$promotionPicker->pushPromotion(new Promotion("Design no. 3", 25));

$selected = $promotionPicker->pickOne(); // Will return the selected item.

// Developer can actually 'force' a minimum percentage to filter.
// Just pass the minimum required percentage value
$selectedForPremium = $promotionPicker->pickOne(50); // Design no. 1 will always return here
```

The solution has performance improved, since we only iterate one time trough all promotion list.

Improvements
============
This application contains all solved exercises. However, there are some missing features that were ignored in order to focus on the solution.

#### 1. DI Container:
Use a different approach to register dependencies (such as **Database** or even the **Commands**) on the boot of the application. This way, we can divide responsability of those features' lifecycle, allow dependency injection trough constructor (SOLID best practices), etc.
Some cool packages that already helps this approach:
- [php-di/php-di](https://packagist.org/packages/php-di/php-di)
- [pimple/pimple](https://packagist.org/packages/pimple/pimple)
- [league/container](https://packagist.org/packages/league/container)

#### 2. Code Coverage:
This solution doesn't have any code coverage. There was my intention to provide that (at least Unit test coverage), but once again it was not requested as part of the exercise, so this Application only provides the solution of it.

#### 3. Presentation layer abstraction:
All the output of the commands are done in the command itself.
