<?php

namespace Netgen\Bundle\EzFormsBundle\Tests\Form\Mock;

class FileMock
{
    public function getRealPath()
    {
        return '/some/path';
    }

    public function getClientOriginalName()
    {
        return 'file_name';
    }

    public function getSize()
    {
        return 123;
    }
}
