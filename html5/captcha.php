<?php
$a = random_int(1, 15);
$b = random_int(1, 15);

$ops = ['+', '-', '*'];
$op  = $ops[random_int(0, 1)];

$answer = match($op) {
    '+' => $a + $b,
    '-' => abs($a - $b),
};

if ($op === '-' && $a < $b) {
    [$a, $b] = [$b, $a];
}

$_SESSION['captcha_question'] = "Cât face $a $op $b?";
$_SESSION['captcha_answer']   = (string) $answer;
