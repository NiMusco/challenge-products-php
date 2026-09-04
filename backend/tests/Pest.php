<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Database isolation
|--------------------------------------------------------------------------
|
| Feature tests hit a PHP built-in server pointed at Docker service db_test
| (separate MySQL container/volume from the app db service).
| Truncate before each test so cases do not leak rows into each other.
|
*/

beforeEach(function () {
    $this->refreshDatabase();
});
