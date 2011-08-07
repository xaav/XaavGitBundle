<?php

namespace Xaav\GitBundle\Git;

class GitItem
{
    /**
     * Marks the object as modified.
     */
    protected function persist()
    {
        $this->repo->persist($this);
    }
}