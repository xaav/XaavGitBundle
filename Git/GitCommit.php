<?php
/*
 * Copyright (C) 2008, 2009 Patrik Fimml
 *
 * This file is part of glip.
 *
 * glip is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.

 * glip is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with glip.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Xaav\GitBundle\Git;

class GitCommit extends GitObject
{
    /**
     * @brief (string) The tree referenced by this commit, as binary sha1
     * string.
     */
    public $tree;

    /**
     * @brief (array of string) Parent commits of this commit, as binary sha1
     * strings.
     */
    public $parents;

    /**
     * @brief (GitCommitStamp) The author of this commit.
     */
    public $author;

    /**
     * @brief (GitCommitStamp) The committer of this commit.
     */
    public $committer;

    /**
     * @brief (string) Commit summary, i.e. the first line of the commit message.
     */
    public $summary;

    /**
     * @brief (string) Everything after the first line of the commit message.
     */
    public $detail;

    public function __construct($repo)
    {
	parent::__construct($repo, GitRepository::OBJ_COMMIT);
    }

    public function _unserialize($data)
    {
    	$lines = explode("\n", $data);
    	unset($data);
    	$meta = array('parent' => array());
    	while (($line = array_shift($lines)) != '')
    	{
    	    $parts = explode(' ', $line, 2);
    	    if (!isset($meta[$parts[0]]))
    		$meta[$parts[0]] = array($parts[1]);
    	    else
    		$meta[$parts[0]][] = $parts[1];
    	}

    	$this->tree = Binary::sha1_bin($meta['tree'][0]);
    	$this->parents = array_map('Xaav\GitBundle\Git\Binary::sha1_bin', $meta['parent']);
    	$this->author = new GitCommitStamp;
    	$this->author->unserialize($meta['author'][0]);
    	$this->committer = new GitCommitStamp;
    	$this->committer->unserialize($meta['committer'][0]);

    	$this->summary = array_shift($lines);
    	$this->detail = implode("\n", $lines);

        $this->history = NULL;
    }

    public function _serialize()
    {
    	$s = '';
    	$s .= sprintf("tree %s\n", Binary::sha1_hex($this->tree));
    	foreach ($this->parents as $parent)
    	    $s .= sprintf("parent %s\n", Binary::sha1_hex($parent));
    	$s .= sprintf("author %s\n", $this->author->serialize());
    	$s .= sprintf("committer %s\n", $this->committer->serialize());
    	$s .= "\n".$this->summary."\n".$this->detail;
    	return $s;
    }

    /**
     * @brief Get commit history in topological order.
     *
     * @returns (array of GitCommit)
     */
    public function getHistory()
    {
        if ($this->history)
            return $this->history;

        /* count incoming edges */
        $inc = array();

        $queue = array($this);
        while (($commit = array_shift($queue)) !== NULL)
        {
            foreach ($commit->parents as $parent)
            {
                if (!isset($inc[$parent]))
                {
                    $inc[$parent] = 1;
                    $queue[] = $this->repo->getObject($parent);
                }
                else
                    $inc[$parent]++;
            }
        }

        $queue = array($this);
        $r = array();
        while (($commit = array_pop($queue)) !== NULL)
        {
            array_unshift($r, $commit);
            foreach ($commit->parents as $parent)
            {
                if (--$inc[$parent] == 0)
                    $queue[] = $this->repo->getObject($parent);
            }
        }

        $this->history = $r;
        return $r;
    }

    /**
     * Get the detail of this commit.
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set the detail of this commit.
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
        $this->setModified();
    }

    /**
     * Get the summary of this commit.
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set the summary of this commit.s
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        $this->setModified();
    }

    /**
    * Get the commiter of this commit.
    */
    public function getCommitter()
    {
        return $this->committer;
    }

    /**
     * Set the commiter of this commit.
     */
    public function setCommitter(GitCommitStamp $committer)
    {
        $this->committer = $committer;
        $this->setModified();
    }

    /**
     * Get the author of this commit.
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set the author of this commit.
     */
    public function setAuthor(GitCommitStamp $author)
    {
        $this->author = $author;
        $this->setModified();
    }

    /**
     * Set the tree refrenced by this commit.
     */
    public function setTree(GitTree $tree)
    {
        $this->tree = $tree->getName();
        $this->setModified();
    }

    /**
     * Get the tree refrenced by this commit.
     *
     * @return GitTree
     */
    public function getTree()
    {
        return $this->repo->getObject($this->tree);
    }

    static public function treeDiff($a, $b)
    {
        return GitTree::treeDiff($a ? $a->getTree() : NULL, $b ? $b->getTree() : NULL);
    }
}

