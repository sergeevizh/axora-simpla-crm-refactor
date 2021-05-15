<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Session\Session;

class AuthManager
{
    /**
     * @var $user array
     */
    private $user;

    /**
     * @var IAuthProvider
     */
    private $authProvider;

    /**
     * @var Session
     */
    private $session;

    public function __construct(IAuthProvider $authProvider, Session $session)
    {
        $this->authProvider = $authProvider;
        $this->session = $session;
        $this->user = $this->getUser();
    }

    public function user(): array
    {
        return $this->user;
    }

    public function guest(): bool
    {
        return (bool)!$this->user;
    }

    public function id(): int
    {
        return $this->user['id'];
    }

    private function getUser(): array
    {
        if ($this->user) {
            return $this->user;
        }

        $userId = $this->session->get('user_id');

        return $userId ? $this->authProvider->getUser($userId) : [];
    }
}