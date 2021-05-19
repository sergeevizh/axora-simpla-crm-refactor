<?php

namespace App\Controllers;

use App\Design;
use App\Repositories\IUserDBRepository;
use App\RequestRules\User\UpdateUserRequest;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    private $container;
    private $design;
    private $userDBRepository;
    protected $middleware = ['auth'];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->userDBRepository = $container->get(IUserDBRepository::class);
        $this->design = $container->get(Design::class);
    }

    public function index()
    {
        $this->design->assign('orders', []);
        $this->design->assign('user', auth()->user());

        $this->design->render('user.tpl');
    }

    public function edit()
    {
        $this->design->assign('user', auth()->user());

        $this->design->render('user-edit.tpl');
    }

    public function update(Request $request, Session $session)
    {
        $validation = makeValidation(
            $this->container->get(Validator::class),
            $request,
            UpdateUserRequest::rules()
        );

        if ($validation->errors()) {
            $session->set('errors', $validation->errors()->toArray());
            $session->set('old', $request->request->all());
        }
        $this->userDBRepository->update(
            auth()->id(),
            $validation->getValidatedData()
        );

        return redirect('/user');
//        $this->design->render('user-edit.tpl');
    }
}
