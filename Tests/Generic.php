<?php

require __DIR__.'/autoload.php';

use Xaav\GitBundle\Git\Git;

// Create a link to the .git repository of GLIP, which you downloaded from
// git clone git://github.com/redotheoffice/glip.git
$repo = new Git(dirname(__FILE__).'/glip/.git');


// When using array access on a Git object, it will return a branch object, named by the key you provide
// Currently GLIP is unable to produce a list of available branches, see TODO list.
$branch = $repo['master'];  // returns GitBranch


// =========================================================================================================================
// GitCommit
// =========================================================================================================================

// The easiest way to work with a branch is to work with its commit tip, which is the latest commit in the branch
$commit = $branch->getTip(); // returns GitCommit

// Each commit object has several properties
echo "GitCommit->tree points to: ".get_class($commit->tree)." ".$commit->tree->getSha()->h(6)."n";
foreach ($commit->parents as $index => $parent)
{
  echo "GitCommit->parents[$index] points to: ".get_class($parent)." ".$parent->getSha()->h(6)."n";
}
echo "GitCommit->author:         ".$commit->author->serialize()."n";
echo "GitCommit->committer:      ".$commit->committer->serialize()."n";
echo "GitCommit->summary:        '".substr($commit->summary,0,40)."'n"; // summary is the first line of the message attached to the commit
echo "GitCommit->detail:         '".substr($commit->detail,0,40)."'n"; // detail holds all other lines of the message
// GitCommit->tree points to: GitTree fcad45
// GitCommit->parents[0] points to: GitCommit 6907e2
// GitCommit->author:         Sjoerd de Jong <sjoerd@weett.nl> 1255502607 +0700
// GitCommit->committer:      Sjoerd de Jong <sjoerd@weett.nl> 1255502607 +0700
// GitCommit->summary:        'fixed getHistory'
// GitCommit->detail:         ''

// You can iterate over the commit to see all files inside
$tree = null;
foreach ($commit as $name => $data)
{
  echo "$name => is a ".get_class($data)." ".$data->getSha()->h(6)."n";

  // this part of the code is just to get a GitTree object, to show the next feature.
  if ($data instanceof GitTree)
  {
    $tree = $data;
  }
}
// .gitignore => is a GitBlob 0bd313
// Doxyfile => is a GitBlob 6d0081
// HACKING => is a GitBlob ee36a4
// LICENSE => is a GitBlob d51190
// README => is a GitBlob 9b2bb6
// doc => is a GitTree d683aa
// lib => is a GitTree 2cf391
// test => is a GitTree a31e96


// =========================================================================================================================
// Sha objects
// =========================================================================================================================

// All objects inheriting from GitObject have a getSha() method
// Which exposes a small API for getting different sha values
echo "|GitObject|::getSha()->hex() = ".$commit->getSha()->hex()."n";
echo "|GitObject|::getSha()->h()   = ".$commit->getSha()->h().  "n";
echo "|GitObject|::getSha()->h(6)  = ".$commit->getSha()->h(6). "n"; // truncated hex encoding
echo "|GitObject|::getSha()->b64() = ".$commit->getSha()->b64()."n"; //binary 64 encoding
echo "|GitObject|::getSha()->bin() = ".$commit->getSha()->bin()."n"; //binary, should not be echo-ed as its binary
echo "|GitObject|::getSha()->b()   = ".$commit->getSha()->b().  "n"; //binary, should not be echo-ed as its binary
echo "|GitObject|::getSha()        = ".$commit->getSha().       "n"; //binary, should not be echo-ed as its binary
// |GitObject|::getSha()->hex() = 371107565e55fce7f2babd9acde491f6e14f2908
// |GitObject|::getSha()->h()   = 371107565e55fce7f2babd9acde491f6e14f2908
// |GitObject|::getSha()->h(6)  = 371107
// |GitObject|::getSha()->b64() = NxEHVl5V/Ofyur2azeSR9uFPKQg=
// |GitObject|::getSha()->bin() = 7◄V^Uⁿτ≥║╜Ü═Σæ÷ßO)
// |GitObject|::getSha()->b()   = 7◄V^Uⁿτ≥║╜Ü═Σæ÷ßO)
// |GitObject|::getSha()        = 7◄V^Uⁿτ≥║╜Ü═Σæ÷ßO)


// =========================================================================================================================
// GitTree
// =========================================================================================================================

// You can iterate over GitTree objects, to see their contents
$blob = null;
if ($tree instanceof GitTree)
{
  echo "Tree ".$tree->getSha()->h(6)." holds ".count($tree)." items.n";

  foreach ($tree as $name => $data)
  {
    echo "inside tree: $name => is a ".get_class($data)."n";

    // this part of the code is just to get a GitBlob object, to show the next feature.
    if ($data instanceof GitBlob)
    {
      $blob = $data;
    }
  }
  // Tree a31e96 holds 3 items.
  // inside tree: bootstrap => is a GitTree
  // inside tree: readme.txt => is a GitBlob
  // inside tree: unit => is a GitTree


// =========================================================================================================================
// GitBlob
// =========================================================================================================================

  // You can access all properties of a GitBlob
  if ($blob instanceof GitBlob)
  {
    echo "Blob holds data: '".substr($blob->data,0,40)."...'n";
  }
  // Blob holds data: 'These tests are all written for the LIME...'
}