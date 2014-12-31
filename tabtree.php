<?php

var_dump(parseTabTree(file_get_contents('test.txt')));

function parseTabTree($data)
{
	return json_decode(parseTabTreeToJSON($data), true);
}
function parseTabTreeToJSON($data)
{
  $accumulator = '';
  $keys = $leaves = [];
	$lines = explode(PHP_EOL, $data);
	$prevDepth = -1;
	$maxDepth = $prevDepth;
	foreach ($lines as $line)
	{
	  //count white space occurnaces for $depth
	  preg_match_all('/^[\s]*/', $line, $depth);
		$depth = strlen($depth[0][0]);
		
		//strip whitespace occurances for $line
		$line = preg_replace('/^\s+/', '', $line);
		
		if ($prevDepth < $depth)
		{
		  //going down
		  if ($accumulator !== '') {
		    //$accumulator holds a key if not empty
		    $keys[$depth][] = makeString($accumulator);
		  }
		  $accumulator = ''; // reset
		}
		else if ($prevDepth > $depth)
		{
		  //going up, $accumulator should be completed leaf
		  $leaves[$prevDepth][] = makeString($accumulator);
		  $accumulator = ''; // reset
		}
		else 
		{
		  //same depth, accumulate adding a space to seperate multi-line values
		  $accumulator .= ' ';
		}
		$accumulator .= $line;
		if ($depth > $maxDepth) { $maxDepth = $depth; }
		$prevDepth = $depth;
  }
  //count down from max depth we found, folding values in as we go
  for ($i = $maxDepth; $i > 0; $i--) 
  {
    $pairs = [];
    foreach ($keys[$i] as $index => $key)
    {
      $pairs[] = makePair($key, $leaves[$i][$index]);
    }
    //all the pairs we've made so far need to become an object whose key is
    //waiting on the next level down
    $leaves[$i - 1][] = makeObject(implode(', ', $pairs));
  }
  //the completed JSON string ends up at $leaves[0][0] so let's return that
  return $leaves[0][0];
}
//wraps a string in braces to become a JSON object
function makeObject($string)
{
  return '{' . $string . '}';
}
//turns a key and leaf string into a JSON key : value pair
function makePair($key, $leaf)
{
  return $key . ' : ' . $leaf;
}
//quotes a PHP string to be included in a JSON string
function makeString($string)
{
  return '"' . $string . '"';
}
?>