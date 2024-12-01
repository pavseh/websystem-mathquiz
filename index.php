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

// Handling the Quiz Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['start_quiz'])) {

        // Start Quiz

        $_SESSION['quiz_settings']['current_question'] = 0;
        $_SESSION['quiz_settings']['correct_answers'] = 0;
        $_SESSION['quiz_settings']['questions'] = [];

        // Generate Questions based on the Settings below

        for ($i = 0; $i < $_SESSION['quiz_settings']['num_questions']; $i++) {
            $_SESSION['quiz_settings']['questions'][] = generate_question(
                $_SESSION['quiz_settings']['level'],
                $_SESSION['quiz_settings']['operator']
            );
        }
        

    } elseif (isset($_POST['set_settings'])) {

        // Save Quiz Settings (Level, Operator, Amount of Questions, and the Max Difference)
        $_SESSION['quiz_settings']['level'] = $_POST['level'];
        $_SESSION['quiz_settings']['operator'] = $_POST['operator'];
        $_SESSION['quiz_settings']['num_questions'] = (int)$_POST['num_questions'];
        $_SESSION['quiz_settings']['max_difference'] = (int)$_POST['max_difference'];
    

    } elseif (isset($_POST['submit_answer'])) {
        
        // Reviewing Answers
        $current_question = $_SESSION['quiz_settings']['current_question'];
        
        if (isset($_SESSION['quiz_settings']['questions'][$current_question])) {
            $correct_answer = $_SESSION['quiz_settings']['questions'][$current_question]['answer'];
            if ((int)$_POST['answer'] === $correct_answer) {
                $_SESSION['quiz_settings']['correct_answers']++;
            }
        }
        $_SESSION['quiz_settings']['current_question']++;

        
    } elseif (isset($_POST['end_quiz'])) {
        // End Quiz Session
        $_SESSION['quiz_settings']['current_question'] = $_SESSION['quiz_settings']['num_questions'];
    }
}


// Display Quiz
$current_question = $_SESSION['quiz_settings']['current_question'];
$num_questions = $_SESSION['quiz_settings']['num_questions'];
$quiz_finished = $current_question >= $num_questions;

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