<?php

namespace Xaav\GitBundle\File;

class Directory extends Object
{
    public function __construct($location)
    {
        $this->makeDirectory(realpath($location));
    }
}