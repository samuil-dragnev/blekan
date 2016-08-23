<?php
// Routes

// Guest
$app->group('', function () {
    $this->get('/signin', 'AuthController:getSignIn')->setName('signin');
    $this->post('/signin', 'AuthController:postSignIn');
    $this->get('/forgot', 'AuthController:getForgotPassword')->setName('forgot');
    $this->post('/forgot', 'AuthController:postForgotPassword');
})->add(new Blekan\Middleware\GuestMiddleware($container));

// Admin
$app->group('/admin', function () {

    // Auth
    $this->get('/signup', 'AuthController:getSignUp')->setName('signup');
    $this->post('/signup', 'AuthController:postSignUp');
    $this->get('/password/change', 'AuthController:getChangePassword')->setName('password.change');
    $this->post('/password/change', 'AuthController:postChangePassword');
    $this->get('/signout', 'AuthController:getSignOut')->setName('signout');

    // Navigation
    $this->get('', 'AdminController:index')->setName('admin.home');
    $this->get('/information', 'AdminController:information')->setName('admin.information');
    $this->get('/menu', 'AdminController:menu')->setName('admin.menu');
    $this->get('/takeaway', 'AdminController:takeaway')->setName('admin.takeaway');

    // Update stuff

    // General
    $this->post('/contactinfo', 'AdminController:changeContactDetails')->setName('contact.info');
    $this->post('/parallax', 'AdminController:changeParalaxImagesAndTitleLogo')->setName('parallax.images');
    $this->post('/colors', 'AdminController:changeMainColors')->setName('colors');
    $this->post('/homemessages', 'AdminController:changeHomeMessages')->setName('home.messages');
    $this->post('/menutakeawatmessages', 'AdminController:changeMenuTakeawayMessages')->setName('menu.takeaway.messages');
    $this->post('/newopenhour', 'AdminController:newOpenHour')->setName('hours.new');
    $this->post('/editopenhour', 'AdminController:editOpenHour')->setName('hours.edit');
    $this->post('/deleteopenhour', 'AdminController:deleteOpenHour')->setName('hours.delete');

    // Information
    $this->post('/restaurantinfo', 'AdminController:changeRestaurantInformation')->setName('restaurant.info');
    $this->post('/newimage', 'AdminController:newSlideshowImage')->setName('slideshow.new');
    $this->post('/editimage', 'AdminController:editSlideshowImage')->setName('slideshow.edit');
    $this->post('/deleteimage', 'AdminController:deleteSlideshowImage')->setName('slideshow.delete');

    // Menu/Takeaway
    // category
    $this->post('/newcategory', 'AdminController:addNewCategory')->setName('category.new');
    $this->get('/editcategory/{id}/{path}', 'AdminController:getCategory')->setName('category.edit');
    $this->post('/editcategory/{id}/{path}', 'AdminController:editCategory');
    $this->post('/deletecategory', 'AdminController:deleteCategory')->setName('category.delete');
    // item
    $this->post('/newitem', 'AdminController:addNewItem')->setName('item.new');
    $this->get('/edititem/{id}/{path}/{is_takeaway}', 'AdminController:getItem')->setName('item.edit');
    $this->post('/edititem/{id}/{path}/{is_takeaway}', 'AdminController:editItem');
    $this->post('/deleteitem', 'AdminController:deleteItem')->setName('item.delete');
})->add(new Blekan\Middleware\AuthMiddleware($container));

// Public WP Navigation
$app->get('/', 'HomeController:index')->setName('home');
$app->get('/information', 'HomeController:information')->setName('information');
$app->get('/menu', 'HomeController:menu')->setName('menu');
$app->get('/takeaway', 'HomeController:takeaway')->setName('takeaway');
$app->get('/contact', 'HomeController:contact')->setName('contact');
$app->post('/contact', 'HomeController:contactSubmit');
