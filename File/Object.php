<?php

namespace Xaav\GitBundle\File;

class Object
{
    protected function makeDirectory($directory)
    {
        if(!is_dir($directory))
        {
            if(!mkdir($directory, 0777, true))
            {
                throw new \UnexpectedValueException(sprintf('%s could not be created', $directory));
            }
        }
    }
}