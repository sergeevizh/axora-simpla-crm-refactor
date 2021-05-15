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
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Design
     */
    private $design;

    /**
     * @var IUserDBRepository
     */
    private $userDBRepository;

    protected $middleware = ['auth'];

    public function __construct(ContainerInterface $container, IUserDBRepository $userDBRepository, Design $design)
    {
        $this->container = $container;
        $this->userDBRepository = $userDBRepository;
        $this->design = $design;
    }

    public function index()
    {
        $this->design->assign('orders', []);
        $this->design->assign('meta_title', auth()->user()['name']);

        $this->design->render('user.tpl');
    }

    public function edit()
    {
        dump($_SESSION);
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
            back();
        }

        $this->userDBRepository->update(
            auth()->id(),
            $validation->getValidatedData()
        );

        $this->design->render('user-edit.tpl');
    }
}
