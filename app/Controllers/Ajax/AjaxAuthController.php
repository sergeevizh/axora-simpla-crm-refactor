<?php

namespace App\Controllers\Ajax;

use App\Controllers\Controller;
use App\Helpers\AuthHelper;
use App\Repositories\IUserDBRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AjaxAuthController extends Controller
{
    protected $middleware = ['ajax'];

    public function login(Request $request, JsonResponse $response, IUserDBRepository $userDBRepository, Session $session)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $userDBRepository->findByEmail($email);

        if (!$user || !AuthHelper::validatePassword($password, $user['password'])) {
            return $this->jsonResponse($response, [
                'code' => 422,
                'errors' => 'Неправильный логин или пароль'
            ], 422);
        }

        $session->set('user_id', $user['id']);

        $userDBRepository->update($user['id'], ['last_ip' => $_SERVER['REMOTE_ADDR']]);

        return $this->jsonResponse($response, ['redirectUrl' => $request->headers->get('referer')]);
    }

    public function register(Request $request, JsonResponse $response, IUserDBRepository $userDBRepository, Session $session)
    {
        $data = $request->request->all();
        $data['last_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['enabled'] = 1;

        if ($userDBRepository->findByEmail($data['email'])) {
            return $this->jsonResponse($response, [
                'code' => 422,
                'errors' => 'Такой Email уже существует'
            ], 422);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user_id = $userDBRepository->create($data);

        $session->set('user_id', $user_id);

        return $this->jsonResponse($response, ['redirectUrl' => $request->headers->get('referer')]);

    }
}