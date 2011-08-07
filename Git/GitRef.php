<?php

namespace Xaav\GitBundle\Git;

/**
 * Represents any git ref, including branches.
 */
class GitRef extends GitItem
{
    protected $object;
    protected $name;

    public function setName($name)
    {
        $this->name = $name;
        $this->setModified();
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the object (usually a commit) that this ref points to.
     *
     * @return GitCommit
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the object that this commit points to.
     */
    public function setObject(GitObject $object)
    {
        $this->object = $object;
        $this->setModified();
    }
}