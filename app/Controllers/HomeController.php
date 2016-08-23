<?php

namespace Blekan\Controllers;

use Slim\Views\Twig as View;
use Blekan\Models\Slideshow;
use Blekan\Models\Information;
use Blekan\Models\Category;
use Blekan\Models\General;
use Blekan\Models\Hour;
use Respect\Validation\Validator as v;

class HomeController extends Controller
{
    public function index($request, $response)
    {
        $hours = Hour::all();

        return $this->view->render($response, 'home.twig', ['home' => true, 'title' => 'Restaurang Blekan: Hem', 'hours' => $hours]);
    }

    public function information($request, $response)
    {
        $slideshow = Slideshow::all();
        $information = Information::find(1);
        $hours = Hour::all();

        return $this->view->render($response, 'information.twig', ['information' => true,  'slides' => $slideshow, 'title' => 'Restaurang Blekan: Information', 'data' => $information, 'hours' => $hours]);
    }

    public function menu($request, $response)
    {
        $categories = Category::where('is_takeaway', false)->orderBy('order_num', 'asc')->get();

        return $this->view->render($response, 'menu.twig', ['menu' => true, 'categories' => $categories, 'title' => 'Restaurang Blekan: Meny']);
    }

    public function takeaway($request, $response)
    {
        $categories = Category::where('is_takeaway', true)->orderBy('order_num', 'asc')->get();

        return $this->view->render($response, 'takeaway.twig', ['takeaway' => true, 'categories' => $categories, 'title' => 'Restaurang Blekan: Takeaway']);
    }

    public function contact($request, $response)
    {
        return $this->view->render($response, 'contact.twig', ['contact' => true, 'title' => 'Restaurang Blekan: Kontakta']);
    }

    public function contactSubmit($request, $response)
    {
        $validation = $this->validator->validate($request, [
        'first_name' => v::notEmpty()->length(null, 255),
        'last_name' => v::notEmpty()->length(null, 255),
        'phone' => v::notEmpty()->length(null, 255),
        'email' => v::notEmpty()->length(null, 255)->email(),
        'message' => v::notEmpty()->length(5, null),
      ]);

        if ($validation->failed()) {
            $this->flash->addMessage('error', 'Alla fält är obligatoriska och måste vara i ett giltigt format! // All fields are required and must be in a valid format!');

            return $response->withRedirect($this->router->pathFor('contact'));
        }

        $data = [
          'name' => $request->getParam('first_name').' '.$request->getParam('last_name'),
          'phone' => $request->getParam('phone'),
          'email' => $request->getParam('email'),
          'message' => $request->getParam('message'),
          'to' => General::find(1)->email_contact,
        ];

        $this->mailer->send('emails/contact_email.twig', ['data' => $data], function ($message) use ($data) {
            $message->to($data['to']);
            $message->subject('Blekan: Meddelande från kontaktformuläret');
            $message->from($data['email']);
            $message->fromName($data['name']);
        });

        $this->flash->addMessage('info', 'Tack för din förfrågan. Din förfrågan är nu på väg till oss. Vi kommer att bearbeta dem så fort som möjligt och komma tillbaka till dig! // Thanks for your inquiry. Your inquiry is now on its way to us. We will process them as quickly as possible and get back to you!');

        return $response->withRedirect($this->router->pathFor('contact'));
    }
}
