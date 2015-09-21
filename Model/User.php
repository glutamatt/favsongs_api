<?php

namespace Model;

use Lib\Persistence;

class User extends Persistence
{
    protected $id;
    protected $name;
    protected $email;

    protected function hydrate($raw)
    {
        $this->id    = (int) $raw['id'];
        $this->name  = $raw['name'];
        $this->email = $raw['email'];
    }

    static protected function getTableName()
    {
        return 'user';
    }

    function jsonSerialize()
    {
        return [
            'user_id' => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
        ];
    }
}
