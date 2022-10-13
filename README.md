# About Vengi/Routing
Vengi/Routing is a simple Routing System for PHP.

## Installation
You can add this Library via composer.

    composer require vengi/routing

## Usage
Include the autoload.php:

    require_once('vendor/autoload.php');

Create a Routes.php File, require your controller file.
Example:

    require_once('Controllers/HomeController.php);
    use Controller\HomeController;

Add Vengi/Routing:

    use Vengi\Routing