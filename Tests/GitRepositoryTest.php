<?php

namespace Xaav\GitBundle\Tests;

use Xaav\GitBundle\Git\GitCommit;
use Xaav\GitBundle\Git\GitTree;
use Xaav\GitBundle\Git\Binary;
use Xaav\GitBundle\Git\GitRepository;

class GitRepositoryTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;

    public function setUp()
    {
        $this->repo = new GitRepository(__DIR__.'/test.git');
    }

    public function testAssumeMaster()
    {
        $this->assertEquals($this->repo->getTip('master'), $this->repo->getTip());
    }

    public function testGetTip()
    {
        $this->assertEquals($this->repo->getRefName('refs/heads/master'), Binary::sha1_bin('549efd7972e9959fdfef9c02744eabc21913bd7a'));
    }

    public function testGetObject()
    {
        $this->assertTrue($this->repo->getObject(Binary::sha1_bin('549efd7972e9959fdfef9c02744eabc21913bd7a')) instanceof GitCommit);
    }

    public function testGetRef()
    {
        $this->assertEquals($this->repo->getRef('refs/heads/master'), $this->repo->getTip('master'));
    }

    public function testGetCommitFromRefObject()
    {
        $this->assertTrue($this->repo->getRef('refs/heads/master')->getObject() instanceof GitCommit);
    }

    public function testGetCommitTreeFromRefObject()
    {
        $this->assertTrue($this->repo->getRef('refs/heads/master')->getObject()->getTree() instanceof GitTree);
    }

    public function testGetSubtreeFromTree()
    {
        $this->assertTrue($this->repo->getRef('refs/heads/master')->getObject()->getTree()->child('folder') instanceof GitTree);
    }
}