<?php
function stamp($input) {
    echo trim(array_reduce($input, function ($carry, $row) { return $carry . PHP_EOL . implode('', $row); }, '')) . PHP_EOL;
}
$flags = FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES;
$input = array_map(function ($row) { return str_split($row); }, file(__DIR__ . '/inputPuzzle.txt', $flags));
$adjacents = [];
for ($y = 0; $y < count($input); ++$y) {
    for ($x = 0; $x < count($input[$y]); ++$x) {
        $adjacents[$y][$x] = [];
        foreach ([[-1, 0], [0, -1], [0, 1], [1, 0], [-1, -1], [-1, 1], [1, -1], [1, 1]] as $transition) {
            $ty = $y + $transition[0];
            $tx = $x + $transition[1];
            if ($ty >= 0 && $ty < count($input) && $tx >= 0 && $tx < count($input[$ty])) {
                array_push($adjacents[$y][$x], [$ty, $tx]);
            }
        }
    }
}

$hash = crc32(serialize($input));
$history = [$hash];

stamp($input);
for ($t = 1; $t <= 524; ++$t) {
    $nextInput = $input;
    for ($y = 0; $y < count($input); ++$y) {
        for ($x = 0; $x < count($input[$y]); ++$x) {
            if ($input[$y][$x] === '.') {
                $nofTrees = 0;
                foreach ($adjacents[$y][$x] as $adjacent) {
                    if ($input[$adjacent[0]][$adjacent[1]] === '|') {
                        $nofTrees++;
                    }
                }
                if ($nofTrees >= 3) {
                    $nextInput[$y][$x] = '|';
                }
            } else if ($input[$y][$x] === '|') {
                $nofLumberyards = 0;
                foreach ($adjacents[$y][$x] as $adjacent) {
                    if ($input[$adjacent[0]][$adjacent[1]] === '#') {
                        $nofLumberyards++;
                    }
                }
                if ($nofLumberyards >= 3) {
                    $nextInput[$y][$x] = '#';
                }
            } else {
                $nofTrees = 0;
                $nofLumberyards = 0;
                foreach ($adjacents[$y][$x] as $adjacent) {
                    if ($input[$adjacent[0]][$adjacent[1]] === '|') {
                        $nofTrees++;
                    }
                    if ($input[$adjacent[0]][$adjacent[1]] === '#') {
                        $nofLumberyards++;
                    }
                }
                if ($nofTrees === 0 || $nofLumberyards === 0) {
                    $nextInput[$y][$x] = '.';
                }
            }
        }
    }
    $input = $nextInput;
    echo 'After ' . $t . ' minutes:' . PHP_EOL;
    stamp($input);
    $hash = crc32(serialize($input));
    if (in_array($hash, $history)) {
        echo 'REPEATED PATTERN TIME: ' . $t . PHP_EOL;
        echo 'FIRST TIME PATTERN SHOWN: ' . array_search($hash, $history) . PHP_EOL;
        break;
    } else {
        $history[] = $hash;
    }
}
$result = [0, 0];
for ($y = 0; $y < count($input); ++$y) {
    for ($x = 0; $x < count($input[$y]); ++$x) {
        if ($input[$y][$x] === '|') {
            $result[0]++;
        } else if ($input[$y][$x] === '#') {
            $result[1]++;
        }
    }
}
echo 'RESULT: ' . ($result[0] * $result[1]) . PHP_EOL;