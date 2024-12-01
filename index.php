<!-- Ivern Buala -->
<!-- Math Quiz using Php -->


<?php
session_start();

// Session Start here
if (!isset($_SESSION['quiz_settings'])) {
    $_SESSION['quiz_settings'] = [
        'level' => 1,
        'operator' => 'addition',
        'num_questions' => 5,
        'max_difference' => 10,
        'current_question' => -1, // -1 because the quiz has not started yet.
        'correct_answers' => 0,
        'questions' => [],
    ];
}

// (Function) Generate Random Quiz Question
function generate_question($level, $operator)
{
    $max = $level === 1 ? 10 : 100;
    $num1 = rand(1, $max);
    $num2 = rand(1, $max);
    $answer = 0;

    switch ($operator) {
        case 'addition':
            $answer = $num1 + $num2;
            $op = '+';
            break;
        case 'subtraction':
            $answer = $num1 - $num2;
            $op = '-';
            break;
        case 'multiplication':
            $answer = $num1 * $num2;
            $op = '×';
            break;
    }

    return [
        'question' => "$num1 $op $num2",
        'answer' => $answer,
    ];
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Math Quiz</title>
</head>
<body>
    
</body>
</html>