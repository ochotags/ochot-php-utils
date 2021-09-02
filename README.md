# OchoT PHP Utils

These are the internal libraries for PHP projects.

## Libraries

### Logs

The library for generating logs.

### Curl

A helper library to use a Curl Connection. You can customize the curl options. You get a complete response with the headers and the body. 

## Installation via Composer

First step, you must include the repository in the composer.json file:

```
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/ochotags/ochot-php-utils.git"
    }
  ],
```

Then you must add the package in the "Require" section or "Require-dev":

```
  "require": {
    ...
    "ochotags/ochot-php-utils": "1.0.0",
    ...
  },
```

Finally, you must execute the composer update command:

```
$ composer update
```

## Validation and tests

### Phpsalm

```
// Normal run
$ ./vendor/bin/psalm

// Verbose run
$ ./vendor/bin/psalm --show-info=true
```

### Phpstan

```
$ ./vendor/bin/phpstan
```

### Phpunit

```
// Normal run
$ ./vendor/bin/phpunit

// With coverage report
$ ./vendor/bin/phpunit --coverage-html ./tests/coverage/html
```
