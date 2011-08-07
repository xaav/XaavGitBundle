<?php

namespace Xaav\GitBundle\Git;

class GitItem
{
    protected $modified = false;

    /**
     * Marks the object as modified.
     */
    protected function persist()
    {
        $this->repo->persist($this);
    }

    public function isModified()
    {
        return $this->modified;
    }

    protected function setModified()
    {
        $this->modified = true;
    }
}