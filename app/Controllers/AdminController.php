<?php

namespace Blekan\Controllers;

use Slim\Views\Twig as View;
use Blekan\Models\Information;
use Blekan\Models\Category;
use Blekan\Models\Item;
use Blekan\Models\Hour;
use Blekan\Models\General;
use Blekan\Models\Slideshow;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as DB;

class AdminController extends Controller
{
    public function index($request, $response)
    {
        $hours = Hour::all();

        return $this->view->render($response, 'admin/home_admin.twig', ['home' => true, 'title' => 'Blekan Admin: Home/General', 'hours' => $hours]);
    }

    public function information($request, $response)
    {
        $information = Information::find(1);
        $slideshow = Slideshow::all();

        return $this->view->render($response, 'admin/information_admin.twig', ['information' => true, 'data' => $information, 'slideshow' => $slideshow, 'title' => 'Blekan Admin: Manage Information']);
    }

    public function menu($request, $response)
    {
        $categories = Category::where('is_takeaway', false)->orderBy('order_num', 'asc')->get();

        return $this->view->render($response, 'admin/menu_admin.twig', ['menu' => true, 'categories' => $categories, 'title' => 'Blekan Admin: Manage Menu']);
    }

    public function takeaway($request, $response)
    {
        $categories = Category::where('is_takeaway', true)->orderBy('order_num', 'asc')->get();

        return $this->view->render($response, 'admin/takeaway_admin.twig', ['takeaway' => true, 'categories' => $categories, 'title' => 'Blekan Admin: Manage Takeaway']);
    }

    public function getItem($request, $response)
    {
        $route = $request->getAttribute('route');
        $id = $route->getArgument('id');
        $path = $route->getArgument('path');
        $is_takeaway = boolval($route->getArgument('is_takeaway'));

        $item = Item::find($id);
        $categories = Category::where('is_takeaway', $is_takeaway)->orderBy('order_num', 'asc')->get();

        return $this->view->render($response, 'admin/admin_item_edit.twig', ['item' => $item, 'categories' => $categories, 'path' => $path, 'is_takeaway' => $route->getArgument('is_takeaway'), 'title' => 'Blekan Admin: Edit Item']);
    }

    public function getCategory($request, $response)
    {
        $categories = Category::where('is_takeaway', true)->orderBy('order_num', 'asc')->get();
        $route = $request->getAttribute('route');
        $id = $route->getArgument('id');
        $path = $route->getArgument('path');

        $category = Category::find($id);

        return $this->view->render($response, 'admin/admin_category_edit.twig', ['category' => $category, 'path' => $path, 'title' => 'Blekan Admin: Edit Category']);
    }

    public function newSlideshowImage($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'image_src' => v::notEmpty()->length(null, 255),
          'image_description' => v::optional(v::length(null, 255)),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect('admin.information');
        }

        $image_src = $request->getParam('image_src');
        $image_description = $request->getParam('image_description');
        $slideshow = new Slideshow();

        $slideshow->src = $image_src;
        $slideshow->description = $image_description;

        $slideshow->save();

        $this->flash->addMessage('info', 'You have successfuly added a new Image to the Slideshow!');

        return $response->withRedirect($this->router->pathFor('admin.information'));
    }

    public function newOpenHour($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'value' => v::notEmpty()->length(null, 255),
          'value_en' => v::notEmpty()->length(null, 255),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect('admin.home');
        }

        $value = $request->getParam('value');
        $value_en = $request->getParam('value_en');
        $hour = new Hour();

        $hour->value = $value;
        $hour->value_en = $value_en;

        $hour->save();

        $this->flash->addMessage('info', 'You have successfuly added a new time slot in the Open Hours!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function editOpenHour($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'value' => v::notEmpty()->length(null, 255),
          'value_en' => v::notEmpty()->length(null, 255),
          'hour_id' => v::notEmpty()->digit(),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect('admin.home');
        }

        $value = $request->getParam('value');
        $value_en = $request->getParam('value_en');
        $hour = Hour::find(intval($request->getParam('hour_id')));

        $hour->value = $value;
        $hour->value_en = $value_en;

        $hour->save();

        $this->flash->addMessage('info', 'You have successfuly updated the Open hours!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function deleteOpenHour($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'hour_id' => v::notEmpty()->digit(),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content is not valid!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $hour_id = intval($request->getParam('hour_id'));

        Hour::destroy($hour_id);

        $this->flash->addMessage('info', 'You have successfuly deleted the time slot!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function editSlideshowImage($request, $response)
    {
        $validation = $this->validator->validate($request, [
        'image_src' => v::notEmpty()->length(null, 255),
        'image_description' => v::optional(v::length(null, 255)),
        'slideshow_id' => v::notEmpty()->digit(),
      ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect('admin.information');
        }

        $image_src = $request->getParam('image_src');
        $image_description = $request->getParam('image_description');
        $slideshow = Slideshow::find(intval($request->getParam('slideshow_id')));

        $slideshow->src = $image_src;
        $slideshow->description = $image_description;

        $slideshow->save();

        $this->flash->addMessage('info', 'You have successfuly updated the Image!');

        return $response->withRedirect($this->router->pathFor('admin.information'));
    }

    public function deleteSlideshowImage($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'slideshow_id' => v::notEmpty()->digit(),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content is not valid!');

            return $response->withRedirect($this->router->pathFor('admin.information'));
        }

        $slideshow_id = intval($request->getParam('slideshow_id'));

        Slideshow::destroy($slideshow_id);

        $this->flash->addMessage('info', 'You have successfuly deleted an image from the slideshow!');

        return $response->withRedirect($this->router->pathFor('admin.information'));
    }

    public function addNewCategory($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'cat_new_title_sv' => v::notEmpty()->length(null, 45),
          'cat_new_title_en' => v::notEmpty()->length(null, 45),
          'cat_new_desc_sv' => v::optional(v::length(null, 255)),
          'cat_new_desc_en' => v::optional(v::length(null, 255)),
          'cat_new_order' => v::notEmpty()->digit(),
        ]);

        $path = $request->getParam('path');

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255 for description, 45 for title) or the "order" contains invalid characters (must be a number)!');

            return $response->withRedirect($path);
        }

        $cat_new_title_sv = $request->getParam('cat_new_title_sv');
        $cat_new_title_en = $request->getParam('cat_new_title_en');
        $cat_new_description_sv = $request->getParam('cat_new_desc_sv');
        $cat_new_description_en = $request->getParam('cat_new_desc_en');
        $cat_new_order = $request->getParam('cat_new_order');
        $is_takeaway = intval($request->getParam('is_takeaway'));
        $category = new Category();

        $category->title = $cat_new_title_sv;
        $category->title_en = $cat_new_title_en;
        $category->description = $cat_new_description_sv;
        $category->description_en = $cat_new_description_en;
        $category->order_num = $cat_new_order;
        $category->is_takeaway = DB::raw($is_takeaway);

        $category->save();

        $this->flash->addMessage('info', 'You have successfuly created a new Category!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function addNewItem($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'item_new_title_sv' => v::notEmpty()->length(null, 45),
          'item_new_title_en' => v::notEmpty()->length(null, 45),
          'item_new_desc_sv' => v::optional(v::length(null, 255)),
          'item_new_desc_en' => v::optional(v::length(null, 255)),
          'item_new_price' => v::optional(v::digit()),
          'item_new_category' => v::notEmpty()->digit(),
        ]);
        $path = $request->getParam('path');

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255 for description, 45 for title) or the "price" contains invalid characters (must be a number) and the Item must belong to a Category!');

            return $response->withRedirect($this->router->pathFor($path));
        }

        $item_new_title_sv = $request->getParam('item_new_title_sv');
        $item_new_title_en = $request->getParam('item_new_title_en');
        $item_new_description_sv = $request->getParam('item_new_desc_sv');
        $item_new_description_en = $request->getParam('item_new_desc_en');
        $item_new_price = $request->getParam('item_new_price');
        $item_new_category = $request->getParam('item_new_category');
        $item = new Item();

        $item->title = $item_new_title_sv;
        $item->title_en = $item_new_title_en;
        $item->description = $item_new_description_sv;
        $item->description_en = $item_new_description_en;
        $item->price = $item_new_price;

        Category::find(intval($item_new_category))->items()->save($item);

        $this->flash->addMessage('info', 'You have successfuly created a new Item!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function deleteCategory($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'category_id' => v::notEmpty()->digit(),
          'path' => v::notEmpty()->length(null, 255),
        ]);

        $path = $request->getParam('path');

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content is not valid!');

            return $response->withRedirect($this->router->pathFor($path));
        }

        $category_id = intval($request->getParam('category_id'));

        $category = Category::find($category_id);

        foreach ($category->items as $item) {
            $item->delete();
        }

        $category->delete();

        $this->flash->addMessage('info', 'You have successfuly deleted a Category + all menu/takeaway items associated with it!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function deleteItem($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'item_id' => v::notEmpty()->digit(),
          'path' => v::notEmpty()->length(null, 255),
        ]);

        $path = $request->getParam('path');

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content is not valid!');

            return $response->withRedirect($this->router->pathFor($path));
        }

        $item_id = intval($request->getParam('item_id'));

        Item::destroy($item_id);

        $this->flash->addMessage('info', 'You have successfuly deleted a menu/takeaway item!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function editItem($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'item_title_sv' => v::notEmpty()->length(null, 45),
          'item_title_en' => v::notEmpty()->length(null, 45),
          'item_desc_sv' => v::optional(v::length(null, 255)),
          'item_desc_en' => v::optional(v::length(null, 255)),
          'item_price' => v::optional(v::digit()),
          'item_category' => v::notEmpty()->digit(),
        ]);
        $path = $request->getParam('path');
        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255 for description, 45 for title) or the "price" contains invalid characters (must be a number) and the Item must belong to a Category!');

            return $response->withRedirect($this->router->pathFor($path));
        }

        $item_title_sv = $request->getParam('item_title_sv');
        $item_title_en = $request->getParam('item_title_en');
        $item_description_sv = $request->getParam('item_desc_sv');
        $item_description_en = $request->getParam('item_desc_en');
        $item_price = $request->getParam('item_price');
        $item_category = $request->getParam('item_category');
        $item = Item::find(intval($request->getParam('item_id')));

        $item->title = $item_title_sv;
        $item->title_en = $item_title_en;
        $item->description = $item_description_sv;
        $item->description_en = $item_description_en;
        $item->category_id = $item_category;
        if (!empty($item_price)) {
            $item->price = intval($item_price);
        }

        $item->save();

        $this->flash->addMessage('info', 'You have successfuly updated the Item!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function editCategory($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'category_title_sv' => v::notEmpty()->length(null, 45),
          'category_title_en' => v::notEmpty()->length(null, 45),
          'category_desc_sv' => v::optional(v::length(null, 255)),
          'category_desc_en' => v::optional(v::length(null, 255)),
          'category_order' => v::notEmpty()->digit(),
        ]);
        $path = $request->getParam('path');
        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255 for description, 45 for title) or the "position" contains invalid characters (must be a number)!');

            return $response->withRedirect($this->router->pathFor($path));
        }

        $category_title_sv = $request->getParam('category_title_sv');
        $category_title_en = $request->getParam('category_title_en');
        $category_description_sv = $request->getParam('category_desc_sv');
        $category_description_en = $request->getParam('category_desc_en');
        $category_order = $request->getParam('category_order');
        $category = Category::find(intval($request->getParam('category_id')));

        $category->title = $category_title_sv;
        $category->title_en = $category_title_en;
        $category->description = $category_description_sv;
        $category->description_en = $category_description_en;
        $category->order_num = intval($category_order);

        $category->save();

        $this->flash->addMessage('info', 'You have successfuly updated the Category!');

        return $response->withRedirect($this->router->pathFor($path));
    }

    public function changeContactDetails($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'address' => v::notEmpty()->length(null, 255),
          'telephone' => v::notEmpty()->length(null, 255),
          'email' => v::noWhitespace()->notEmpty()->email()->length(null, 255),
          'email_contact' => v::noWhitespace()->notEmpty()->email()->length(null, 255),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255) or the email is not valid!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $address = $request->getParam('address');
        $telephone = $request->getParam('telephone');
        $email = $request->getParam('email');
        $email_contact = $request->getParam('email_contact');

        General::find(1)->setContactDetails($address, $telephone, $email, $email_contact);

        $this->flash->addMessage('info', 'You have successfuly change your contact details!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function changeMainColors($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'nav_color' => v::notEmpty()->length(null, 255),
          'header_color' => v::notEmpty()->length(null, 255),
          'footer_color' => v::notEmpty()->length(null, 255),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $nav_color = $request->getParam('nav_color');
        $header_color = $request->getParam('header_color');
        $footer_color = $request->getParam('footer_color');

        General::find(1)->setMainColors($nav_color, $header_color, $footer_color);

        $this->flash->addMessage('info', 'You have successfuly changed the home page\'s colors!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function changeParalaxImagesAndTitleLogo($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'parallax_img_1' => v::notEmpty()->length(null, 255),
          'parallax_img_2' => v::notEmpty()->length(null, 255),
          'parallax_img_3' => v::notEmpty()->length(null, 255),
          'logo_image' => v::optional(v::length(null, 255)),
          'main_title' => v::optional(v::length(null, 255)),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (255)!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $parallax_img_1 = $request->getParam('parallax_img_1');
        $parallax_img_2 = $request->getParam('parallax_img_2');
        $parallax_img_3 = $request->getParam('parallax_img_3');
        $logo_image = $request->getParam('logo_image');
        $main_title = $request->getParam('main_title');

        General::find(1)->setParalaxImagesAndTitleLogo($parallax_img_1, $parallax_img_2, $parallax_img_3, $main_title, $logo_image);

        $this->flash->addMessage('info', 'You have successfuly updated the home page\'s parallax images and logo title!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function changeHomeMessages($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'home_main_message' => v::notEmpty()->length(null, 511),
          'home_main_message_en' => v::notEmpty()->length(null, 511),
          'home_ribbon_message' => v::notEmpty()->length(null, 127),
          'home_ribbon_message_en' => v::notEmpty()->length(null, 127),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (511 for the text area and 127 for text field)!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $home_main_message = $request->getParam('home_main_message').'//'.$request->getParam('home_main_message_en');
        $home_ribbon_message = $request->getParam('home_ribbon_message').'//'.$request->getParam('home_ribbon_message_en');

        General::find(1)->setHomeMessages($home_ribbon_message, $home_main_message);

        $this->flash->addMessage('info', 'You have successfuly updated the home page\'s ribbon and main messages!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function changeMenuTakeawayMessages($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'menu_message' => v::notEmpty()->length(null, 511),
          'menu_message_en' => v::notEmpty()->length(null, 511),
          'takeaway_message' => v::notEmpty()->length(null, 511),
          'takeaway_message_en' => v::notEmpty()->length(null, 511),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (511)!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $menu_message = $request->getParam('menu_message').'//'.$request->getParam('menu_message_en');
        $takeaway_message = $request->getParam('takeaway_message').'//'.$request->getParam('takeaway_message_en');

        General::find(1)->setMenuTakeawayMessage($menu_message, $takeaway_message);

        $this->flash->addMessage('info', 'You have successfuly updated the home page\'s Menu and Takeaway messages!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function changeRestaurantInformation($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'food_type' => v::notEmpty()->length(null, 511),
          'food_type_en' => v::notEmpty()->length(null, 511),
          'restaurant_type' => v::notEmpty()->length(null, 511),
          'restaurant_type_en' => v::notEmpty()->length(null, 511),
          'other_type' => v::notEmpty()->length(null, 511),
          'other_type_en' => v::notEmpty()->length(null, 511),
          'atmosphere' => v::notEmpty()->length(null, 511),
          'atmosphere_en' => v::notEmpty()->length(null, 511),
          'payment' => v::notEmpty()->length(null, 511),
          'payment_en' => v::notEmpty()->length(null, 511),
          'services' => v::notEmpty()->length(null, 511),
          'services_en' => v::notEmpty()->length(null, 511),
          'seats' => v::notEmpty()->digit(),
        ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Some fields are empty or the content exceed the maximum characters allowed per field (511)!');

            return $response->withRedirect($this->router->pathFor('admin.home'));
        }

        $seats = $request->getParam('seats');
        $food_type = $request->getParam('food_type');
        $food_type_en = $request->getParam('food_type_en');
        $restaurant_type = $request->getParam('restaurant_type');
        $restaurant_type_en = $request->getParam('restaurant_type_en');
        $other_type = $request->getParam('other_type');
        $other_type_en = $request->getParam('other_type_en');
        $atmosphere = $request->getParam('atmosphere');
        $atmosphere_en = $request->getParam('atmosphere_en');
        $payment = $request->getParam('payment');
        $payment_en = $request->getParam('payment_en');
        $services = $request->getParam('services');
        $services_en = $request->getParam('services_en');

        Information::find(1)->setInformation($seats, $food_type, $food_type_en, $restaurant_type, $restaurant_type_en, $other_type, $other_type_en, $atmosphere, $atmosphere_en, $payment, $payment_en, $services, $services_en);

        $this->flash->addMessage('info', 'You have successfuly updated the home page\'s Information!');

        return $response->withRedirect($this->router->pathFor('admin.information'));
    }
}
