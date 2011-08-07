<?php

namespace Xaav\GitBundle\Tests;

use Xaav\GitBundle\Git\GitCommitStamp;

use Xaav\GitBundle\Git\GitBlob;

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

    public function testCommitFile()
    {
        $blob = new GitBlob($this->repo);
        $this->repo->persist($blob);
        $blob->data = 'Test Content';
        $blob->rehash();

        $tree = clone $this->repo->getTip()->getObject()->getTree();

        foreach ($tree->updateNode('README', 0100640, $blob->getName()) as $object) {
            $this->repo->persist($object);
        }

        $tree->rehash();
        $this->repo->persist($tree);

        $newcommit = new GitCommit($this->repo);
        $newcommit->tree = $tree->getName();
        $newcommit->parents = array($this->repo->getTip()->getObject()->getName());

        $stamp = new GitCommitStamp();
        $stamp->name = 'Test user';
        $stamp->email = 'test@test.com';
        $stamp->time = time();

        $stamp->offset = idate('Z', $stamp->time);

        $newcommit->author = $stamp;
        $newcommit->committer = $stamp;
        $newcommit->summary = 'Test summary';
        $newcommit->detail = 'Test detail';

        $newcommit->rehash();

        $this->repo->persist($newcommit);

        $ref = $this->repo->getTip();
        $ref->setObject($newcommit);

        /**
         * Write changes to disk.
         */
        $this->repo->flush();
        $ref->write();

    }
}