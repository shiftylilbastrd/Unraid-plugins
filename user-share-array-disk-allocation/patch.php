<?php
/*
 * User share array disk allocation
 * Adds a column to Unraid's existing User Shares table using the same effective
 * global and per-share include/exclude rules as the stock ShareList renderer.
 */

$pagePath = '/usr/local/emhttp/plugins/dynamix/ShareList.page';
$listPath = '/usr/local/emhttp/plugins/dynamix/include/ShareList.php';

$headerBlock = <<<'BLOCK'
<!-- USAD:BEGIN header -->
                <td>Array disks</td>
                <!-- USAD:END header -->
BLOCK;
$logicBlock = <<<'BLOCK'
		/* USAD:BEGIN allocation logic */
		$usadDiskNumbers = [];
		$include = $share['include'] ?? '';
		$usadUseCache = $share['useCache'] ?? '';
		$usadArrayTarget = ($usadUseCache === 'no')
			|| (in_array($usadUseCache, ['yes', 'prefer'], true) && empty($share['cachePool2']));
		$usadArrayDiskNames = [];
		foreach ($disks as $usadDiskName => $usadDiskInfo) {
			if (preg_match('/^disk[0-9]+$/', (string)$usadDiskName)
				&& ($usadDiskInfo['fsStatus'] ?? '') === 'Mounted') {
				$usadArrayDiskNames[] = $usadDiskName;
			}
		}
		if ($usadArrayTarget) {
			$usadAllowedDiskNames = array_filter(
				array_diff(array_intersect($myDisks, $usadArrayDiskNames), explode(',', $share['exclude'] ?? '')),
				'shareInclude'
			);
			foreach ($usadAllowedDiskNames as $usadDiskName) {
				if (preg_match('/^disk([0-9]+)$/', (string)$usadDiskName, $usadMatch)) {
					$usadDiskNumbers[] = (int)$usadMatch[1];
				}
			}
		}
		$usadDiskNumbers = array_values(array_unique($usadDiskNumbers));
		sort($usadDiskNumbers, SORT_NUMERIC);
		$usadRanges = [];
		if ($usadDiskNumbers) {
			$usadRangeStart = $usadRangeEnd = $usadDiskNumbers[0];
			for ($usadIndex = 1; $usadIndex < count($usadDiskNumbers); $usadIndex++) {
				$usadNumber = $usadDiskNumbers[$usadIndex];
				if ($usadNumber === $usadRangeEnd + 1) {
					$usadRangeEnd = $usadNumber;
					continue;
				}
				$usadRanges[] = ($usadRangeStart === $usadRangeEnd)
					? (string)$usadRangeStart
					: $usadRangeStart . '-' . $usadRangeEnd;
				$usadRangeStart = $usadRangeEnd = $usadNumber;
			}
			$usadRanges[] = ($usadRangeStart === $usadRangeEnd)
				? (string)$usadRangeStart
				: $usadRangeStart . '-' . $usadRangeEnd;
		}
		$usadDisplay = (count($usadArrayDiskNames) > 0 && count($usadDiskNumbers) === count($usadArrayDiskNames))
			? 'All'
			: implode(',', $usadRanges);
		$usadDiskList = htmlspecialchars($usadDisplay, ENT_QUOTES, 'UTF-8');
		/* USAD:END allocation logic */
BLOCK;
$cellBlock = <<<'BLOCK'
		/* USAD:BEGIN allocation cell */
		echo "<td class='usad-array-disk-allocation'>", $usadDiskList, "</td>";
		/* USAD:END allocation cell */
BLOCK;
$detailBlock = <<<'BLOCK'
			/* USAD:BEGIN detail cell */
			echo "<td></td>";
			/* USAD:END detail cell */
BLOCK;

function read_source($path) {
	if (!is_file($path) || !is_readable($path)) {
		throw new RuntimeException("Cannot read " . $path);
	}
	$data = file_get_contents($path);
	if ($data === false) throw new RuntimeException("Cannot read " . $path);
	return $data;
}
function replace_once($source, $search, $replacement, $label) {
	$count = substr_count($source, $search);
	if ($count !== 1) throw new RuntimeException("Expected one " . $label . " anchor; found " . $count);
	return str_replace($search, $replacement, $source);
}
function write_atomic($path, $contents) {
	$tmp = $path . '.usad.tmp';
	if (file_put_contents($tmp, $contents, LOCK_EX) === false) {
		throw new RuntimeException("Cannot write " . $tmp);
	}
	@chmod($tmp, fileperms($path) & 0777);
	if (!rename($tmp, $path)) {
		@unlink($tmp);
		throw new RuntimeException("Cannot replace " . $path);
	}
}
function remove_all($source, $needle) {
	return str_replace($needle, '', $source);
}

$remove = isset($argv[1]) && $argv[1] === 'remove';
try {
	$page = read_source($pagePath);
	$list = read_source($listPath);

	if ($remove) {
		$page = remove_all($page, "\n" . $headerBlock);
		$list = remove_all($list, $logicBlock . "\n");
		$list = remove_all($list, "\n" . $cellBlock);
		$list = remove_all($list, $detailBlock . "\n");
		write_atomic($pagePath, $page);
		write_atomic($listPath, $list);
		echo "User share array disk allocation column removed.\n";
		exit(0);
	}

	if (strpos($page, 'USAD:BEGIN header') === false) {
		$page = replace_once($page, '<td>_(Storage)_</td>', '<td>_(Storage)_</td>' . "\n" . $headerBlock, 'Storage header');
	}
	if (strpos($list, 'USAD:BEGIN allocation logic') !== false) {
		$logicPattern = '/\t\t\/\* USAD:BEGIN allocation logic \*\/.*?\t\t\/\* USAD:END allocation logic \*\/\n/s';
		if (preg_match_all($logicPattern, $list) !== 1) {
			throw new RuntimeException('Expected exactly one existing allocation logic block.');
		}
		$list = preg_replace_callback($logicPattern, function() use ($logicBlock) {
			return $logicBlock . "\n";
		}, $list, 1);
	} else {
		$anchor = "\tif (array_key_exists(\$name, \$ssz1)) {";
		$list = replace_once($list, $anchor, $logicBlock . "\n" . $anchor, 'share row');
	}
	$cacheEcho = 'echo "<td>$cache</td>";';
	if (substr_count($list, 'USAD:BEGIN allocation cell') === 0) {
		if (substr_count($list, $cacheEcho) !== 2) throw new RuntimeException('Expected two Storage cell outputs.');
		$list = str_replace($cacheEcho, $cacheEcho . "\n" . $cellBlock, $list);
	}
	if (strpos($list, 'USAD:BEGIN detail cell') === false) {
		$detailAnchor = "\t\t\techo \"<td>\", my_scale(\$disksize, \$unit), \" \$unit</td>\";";
		$list = replace_once($list, $detailAnchor, $detailBlock . "\n" . $detailAnchor, 'computed disk detail row');
	}

	/* Write both files only after every source anchor has been confirmed. */
	write_atomic($pagePath, $page);
	write_atomic($listPath, $list);
	echo "User share array disk allocation column applied.\n";
} catch (Throwable $e) {
	fwrite(STDERR, "User share array disk allocation: " . $e->getMessage() . "\n");
	exit(1);
}
