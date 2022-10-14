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

    use Vengi\Route;

### Creation of Routes
Creation of Simple Routes:

    Route::view('/',function(){
        echo 'Hello World';     // When the path is equal to '/', execute the anonymous function.
    });
    
Creation of GET,POST,PUT,DELETE Routes:

    Route::get('/home',HomeController::class,'index_method');
    
    Route::post('/home/store',HomeController::class,'store_method');
    
    Route::put('/home/update/?id',HomeController::class,'update_method');
    
    Route::delete('/home/delete/?id',HomeController::class,'delete_method');

    Route::run(); // Execute routing, for routes Route::view() is not necessary.

Example in monolithic application:
#### Routes

    Route::view('/',function(){
        echo 'Welcome';
    });

    Route::get('/home',HomeController::class,'index_method');
    
    Route::get('/home/create',HomeController::class,'create_method');
    
    Route::post('/home/store',HomeController::class,'store_method');
    
    Route::get('/home/edit',HomeController::class,'edit_method');
    
    Route::post('/home/update/?id',HomeController::class,'update_method');
    
    Route::post('/home/delete',HomeController::class,'delete_method');
    
    Route::get('/home',HomeController::class,'index_method');

    Route::run(); // Execute routing.

#### HTML Forms

    Create Form:
    <form action="<?php hostPath('/home/store') ?>" method="post">
        <?= csrf() ?>
        <input type="text" name="hello">
        <input type="submit" value="Send Data">
    </form>

    Update Form:
    <form action="<?php hostPath('/home/update/?id='.$id) ?>" method="post">
        <?= csrf() ?>
        <input type="text" name="hello">
        <input type="submit" value="Send Data">
    </form>

Note: for submissions through normal HTML forms, use GET and POST.

Example using the Vengi/Engine Library:
#### Routes

    use Vengi\View;

    Route::view('/home/create',function(){
        return View::set('create')
    });

    Route::post('/home/store',HomeController::class,'store_method');

    Route::run(); // Execute routing.

#### HTML Forms

    Create Form:
    <form action="{= hostPath('/home/store') }}" method="post">
        {= csrf() }}
        <input type="text" name="hello">
        <input type="submit" value="Send Data">
    </form>

    Update Form:
    <form action="{= hostPath('/home/update/?id='.$id) }}" method="post">
        {= csrf() }}
        <input type="text" name="hello">
        <input type="submit" value="Send Data">
    </form>

### Important
In the /public folder add a .htaccess file.

It must include the following:

    <IfModule mod_rewrite.c>
        <IfModule mod_negotiation.c>
            Options -MultiViews -Indexes
        </IfModule>

        RewriteEngine On
        
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </IfModule>