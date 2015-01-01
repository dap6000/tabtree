<?php
echo '<pre>';
var_dump(parseTabTree(file_get_contents('test.txt')));
echo '</pre>';
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
		  //going up, $accumulator should be completed leaf for most recent key
		  $key = array_pop($keys[$prevDepth]);
		  $leaves[$prevDepth][] = makePair($key, makeString($accumulator));
		  $accumulator = ''; // reset
		  //if we've jumped up more than one level then we need construct an object
		  //for the key currently associated with each level. the object may be a 
		  //single key : value pair or it could be a collection of siblings. 	  
		  for ($diff = $prevDepth - 1; $diff > $depth; $diff--) 
		  {      
		    //grab the key for depth = $diff...
		    $key = array_pop($keys[$diff]);	
		    //...take all the leaves at depth = $diff + 1...
		    $siblings = $leaves[$diff + 1];
		    unset($leaves[$diff + 1]);
		    $leaf = (count($siblings) > 1) ? implode(', ', $siblings) : $siblings[0];
		    //...turn them into an object and pair them with key, storing the 
		    //resulting string back in $leaves one level lower.
		    $leaves[$diff][] = makePair($key, makeObject($leaf));
		  }
		}
		else 
		{
		  //same depth, accumulate adding a space to seperate multi-line values
		  $accumulator .= ' ';
		}
		$accumulator .= $line;
		$prevDepth = $depth;	
  }
  //$leaves[1] ends up containing the string we've built, but it needs to be 
  //wrapped in braces. that's what makeObject() is for.
  return makeObject(array_pop($leaves[1])); 
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