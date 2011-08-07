<?php

namespace Xaav\GitBundle\Git;

/**
 * Represents any git ref, including branches.
 */
class GitRef extends GitItem
{
    /**
     * @var GitRepository
     */
    protected $repo;
    protected $hash;
    protected $name;

    public function __construct($repo)
    {
        $this->repo = $repo;

        $this->repo->persist($this);
    }

    public function unserialize($data)
    {
        $this->hash = $data;
    }

    public function serialize()
    {
        return $this->hash;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function write()
    {
        $path = sprintf('%s/%s', $this->repo->dir, $this->name);
        file_put_contents($path, Binary::sha1_hex($this->hash));
    }

    /**
     * Gets the object (usually a commit) that this ref points to.
     *
     * @return GitCommit
     */
    public function getObject()
    {
        return $this->repo->getObject($this->hash);
    }

    /**
     * Sets the object that this commit points to.
     */
    public function setObject(GitObject $object)
    {
        $this->hash = $object->getName();
    }
}