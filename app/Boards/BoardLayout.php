<?php

declare(strict_types=1);

namespace App\Boards;

class BoardLayout
{
    
    private $_layout;

    public function __construct()
    {
        $this->_layout = [];
    }

    public function getLayout() : array
    {
        return $this->_layout;
    }

    public function setLayout(array $updated_path) : void
    {
        $this->_layout = $updated_path;
    }

}
