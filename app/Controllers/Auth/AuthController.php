<?php

namespace Blekan\Controllers\Auth;

use Blekan\Models\User;
use Blekan\Models\General;
use Blekan\Controllers\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
    public function getChangePassword($request, $response)
    {
        return $this->view->render($response, 'auth/change.password.twig', ['changepass' => true, 'title' => 'Blekan Admin: Change password']);
    }

    public function getForgotPassword($request, $response)
    {
        return $this->view->render($response, 'auth/forgot_password.twig', ['forgot' => true, 'title' => 'Blekan Admin: Forgot password']);
    }

    public function getSignUp($request, $response)
    {
        return $this->view->render($response, 'auth/signup.twig', ['title' => 'Blekan Admin: Register new Admin']);
    }

    public function getSignOut($request, $response)
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('home'));
    }

    public function getSignIn($request, $response)
    {
        return $this->view->render($response, 'auth/signin.twig', ['title' => 'Blekan Admin: Sign in']);
    }

    public function postChangePassword($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->auth->getCurrentUser()->password),
          'password' => v::noWhitespace()->notEmpty(), ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('password.change'));
        }

        $this->auth->getCurrentUser()->setPassword($request->getParam('password'));

        $this->flash->addMessage('info', 'You have successfuly changed your password!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function postSignIn($request, $response)
    {
        $validation = $this->validator->validate(
          $request,
          [
            'email' => v::noWhitespace()->notEmpty()->email(),
            'password' => v::noWhitespace()->notEmpty(),
          ]
        );

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('signin'));
        }

        $email = $request->getParam('email');
        $password = $request->getParam('password');
        $auth = $this->auth->attemptAuthentication($email, $password);

        if (!$auth) {
            $this->flash->addMessage('error', 'Authentication failed! Please be sure to provide the correct creadentials!');

            return $response->withRedirect($this->router->pathFor('signin'));
        }

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function postSignUp($request, $response)
    {
        $validation = $this->validator->validate($request, [
          'first_name' => v::noWhitespace()->notEmpty()->alpha(),
          'last_name' => v::noWhitespace()->notEmpty()->alpha(),
          'email' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
          'password' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('signup'));
        }

        $name = $request->getParam('first_name').' '.$request->getParam('last_name');
        $email = $request->getParam('email');
        $password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
        $user = User::create([
          'name' => $name,
          'email' => $email,
          'password' => $password,
        ]);

        $this->flash->addMessage('info', 'You have successfuly created a new Admin!');

        return $response->withRedirect($this->router->pathFor('admin.home'));
    }

    public function postForgotPassword($request, $response)
    {
        $validation = $this->validator->validate($request, ['email' => v::noWhitespace()->notEmpty()->email()]);

        if ($validation->failed()) {
            return $response->withRedirect($this->router->pathFor('forgot'));
        }

        $email = $request->getParam('email');
        $user = User::where('email', $email)->first();

        if (empty($user->email)) {
            $this->flash->addMessage('info', 'This email is not registered!');

            return $response->withRedirect($this->router->pathFor('forgot'));
        }

        $new_password = $user->generateStrongPassword(9);

        $user->setPassword($new_password);

        $company_email = General::find(1)->email;

        $data = [
          'password' => $new_password,
          'name' => $user->name,
          'client_email' => $email,
          'company_email' => $company_email,
        ];

        $this->mailer->send('emails/password_reset_email.twig', ['data' => $data], function ($message) use ($data) {
            $message->to($data['company_email']);
            $message->subject('Blekan Admin: Password reset');
            $message->from($data['company_email']);
            $message->fromName($data['name']);
        });

        $this->mailer->send('emails/password_reset_email.twig', ['data' => $data], function ($message) use ($data) {
            $message->to($data['client_email']);
            $message->subject('Blekan Admin: Password reset');
            $message->from($data['company_email']);
            $message->fromName($data['name']);
        });

        $this->flash->addMessage('info', 'An email containing your new password has been sent to your email and the official company email address!');

        return $response->withRedirect($this->router->pathFor('signin'));
    }
}
