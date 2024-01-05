<?php

const IGNORED_FOLDER = [
	"./Folder ignored",
];

const BANNED = [
	"printf",
	"other"
];


function get_all_files($path) {
	global $argv;
	$files = scandir($path);
	$output = [];
	foreach ($files as $file_path) {
		if (
			$file_path[0] == "." ||
			$path . "/" . $file_path == "./" . $argv[0] ||
			in_array($path . "/" . $file_path, IGNORED_FOLDER)
		)
			continue;
		if (is_dir($path . "/" . $file_path)) {
			array_push($output, ...get_all_files($path . "/" . $file_path));
		} else {
			array_push($output, $path . "/" . $file_path);
		}
	}
	return $output;
}

$all_files = get_all_files(".");
var_dump($all_files);

$main = [];

foreach ($all_files as $file_path) {
	$line_number = 0;
	$file = fopen($file_path, "r");
	while ($content = fgets($file)) {
		$line_number++;
		if (preg_match("/\bmain\b/", $content)) {
			$main[] = [
				$file_path,
				$line_number
			];
		}
		foreach (BANNED as $banned_word) {
			if (preg_match("/\b" . $banned_word . "\b/", $content)) {
				echo "Banned word \033[1;31m$banned_word\033[0m have been found in \033[1;31m$file_path\033[0m, at line \033[1;31m$line_number\033[0m.\n";
			}
		}
	}
}

if (sizeof($main) == 0) {
	echo "The \033[0;33mmain\033[0m function have not been founded.\n";
} else {
	echo "The \033[0;33mmain\033[0m function have been founded \033[0;33m" . sizeof($main) . "\033[0m times.\n";
	foreach ($main as $tab) {
		echo "\tIn \033[0;33m", $tab[0], " \033[0m at line \033[0;33m", $tab[1], "\033[0m.\n";
	}
}