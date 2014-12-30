function parseTabTree(data) {
	var root = { value: 'root' }, obj = root, current = '', lines = data.split('\r\n'), curDepth = -1;
	for (ix in lines) {
		var line = lines[ix].replace(/^\s+/, '');
		var depth = lines[ix].match(/^[\s]*/g)[0].length;
		// Shallow, push our current value in and switch target up a level.
		if (depth < curDepth) {
			obj.value = current;
			for (var cl = curDepth - depth; cl > 0; --cl) obj = obj.parent;
			last = current = '';
		}
		// Deep, push our current target to parent and step down a level.
		else if (depth > curDepth) {
			nobj = {parent: obj, value: current};
			if (!obj[current]) obj[current] = [];
			obj[current].push(nobj);
			obj = nobj;
			current = '';
		}
		// Across
		else obj.value += current;
		current += line;
		curDepth = depth;
	}
	return root;
}
