<?php

namespace Xaav\GitBundle\File;

class File extends Object
{
    protected $location;

    public function __construct($location)
    {
        $this->location = realpath($location);
    }

    public function setContents($contents)
    {
        if(!is_dir(dirname($this->location)))
        {
            $this->makeDirectory(dirname($this->location));
        }
        if(!file_put_contents($this->location, $contents))
        {
            throw new \UnexpectedValueException(sprintf('%s could not be written to', $this->location));
        }
    }

    public function getContents()
    {
        return file_get_contents($this->location);
    }
}