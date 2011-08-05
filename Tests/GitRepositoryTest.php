<?php

namespace Xaav\GitBundle\Tests;

use Xaav\GitBundle\Git\Binary;

use Xaav\GitBundle\Git\GitRepository;

class GitRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testRepository()
    {
        $repo = new GitRepository(__DIR__.'/test.git');

        $this->assertEquals($repo->getTip('master'), $repo->getTip());
        $this->assertEquals($repo->getTip(), Binary::sha1_bin('549efd7972e9959fdfef9c02744eabc21913bd7a'));
    }
}