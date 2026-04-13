<?php

namespace Inforex\Lpmn\Request;

class User
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return array<string, string>
     */
    public function toArray()
    {
        return array(
            'username' => $this->username,
            'password' => $this->password,
        );
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
