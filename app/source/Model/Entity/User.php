<?php

namespace AppTest\Model\Entity;

class User
{
    protected $id;

    protected $name;

    public function __construct($id = null, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function isAuthorized()
    {
        return isset($this->id);
    }

    public function getName()
    {
        return $this->name;
    }
}
