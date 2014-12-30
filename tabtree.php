<?php

var_dump(parseTabTree(file_get_contents('test.txt')));

function parseTabTree($data)
{
	$root = array();
	$root['value'] = 'root';
	$obj = &$root;
	$current = '';
	$lines = explode("\r\n", $data);
	$curDepth = -1;
	foreach ($lines as $line)
	{
		preg_match_all('/^[\s]*/', $line, $depth);
		$depth = strlen($depth[0][0]);
		$line = preg_replace('/^\s+/', '', $line);
		// Shallow, push our current value in and switch target up a level.
		if ($depth < $curDepth)
		{
			$obj['value'] = $current;
			for ($cl = $curDepth - $depth; $cl > 0; --$cl)
				$obj = $obj['parent'];
			$last = $current = '';
		}
		// Deeper, push our current target to parent and step down a level.
		else if ($depth > $curDepth)
		{
			$nobj = array();
			$nobj['parent'] = $obj;
			$nobj['value'] = $current;
			if (!empty($current) && !isset($obj[$current])) $obj[$current] = array();
			$oc = &$obj[$current];
			var_dump($nobj);
			$oc[] = $nobj;
			$obj = $nobj;
			$current = '';
		}
		// Across
		else $obj['value'] += $current;
		$current .= $line;
		$curDepth = $depth;
	}
	return $root;
}

?>