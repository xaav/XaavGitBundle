<?php

namespace Xaav\GitBundle\Git;

/**
 * Represents any git ref, including branches.
 */
class GitRef
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
    }

    public function unserialize($data)
    {
        list($this->hash, $this->name) = $data;
    }

    public function serialize()
    {
        return array($this->hash, $this->name);
    }

    public function write()
    {
        $path = sprintf('%s/%s', $this->repo->dir, $this->name);
        file_put_contents($path, $this->hash);
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
}