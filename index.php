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
    <script src="https://cdn.tailwindcss.com"></script>
</head>


<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">
    <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
        <h1 class="text-3xl font-bold text-center mb-6">Math Quiz using PHP</h1>

        <!-- (HTML) Quiz Settings Section -->
        <form method="post" class="mb-6">
            <h2>Settings</h2>

            <!-- Level -->
            <label>Level:</label>
            <select name="level">
                <option value="1" <?php echo $_SESSION['quiz_settings']['level'] == 1 ? 'selected' : ''; ?>>1-10</option>
                <option value="2" <?php echo $_SESSION['quiz_settings']['level'] == 2 ? 'selected' : ''; ?>>1-100</option>
            </select><br>

            <!-- Operator -->
            <label>Operator:</label>
            <select name="operator">
                <option value="addition" <?php echo $_SESSION['quiz_settings']['operator'] == 'addition' ? 'selected' : ''; ?>>Addition</option>
                <option value="subtraction" <?php echo $_SESSION['quiz_settings']['operator'] == 'subtraction' ? 'selected' : ''; ?>>Subtraction</option>
                <option value="multiplication" <?php echo $_SESSION['quiz_settings']['operator'] == 'multiplication' ? 'selected' : ''; ?>>Multiplication</option>
            </select><br>

            <!-- Labels -->
            <label>Number of Questions:</label>
            <input type="number" name="num_questions" value="<?php echo $_SESSION['quiz_settings']['num_questions']; ?>" min="1" max="20"><br>

            <label>Max Difference:</label>
            <input type="number" name="max_difference" value="<?php echo $_SESSION['quiz_settings']['max_difference']; ?>" min="1" max="10"><br>

            <button type="submit" name="set_settings">Save Settings</button>
        </form>

        
        <!-- The Start Quiz Button only shows if the quiz has not started yet. -->
        <!-- Quiz Not Started Yet -->
        <?php if ($current_question === -1): ?>
            <h2>Welcome to the Math Quiz!</h2>
            <form method="post">
                <button type="submit" name="start_quiz">Start Quiz</button>
            </form>

        <!-- Quiz Finished -->
        <?php elseif ($quiz_finished): ?>
            <h2>Your Score: <?php echo $_SESSION['quiz_settings']['correct_answers'] . " / $num_questions"; ?></h2>
            <form method="post">
                <button type="submit" name="start_quiz">Restart Quiz</button>
            </form>

        <!-- Quiz In Progress -->
        <?php else: ?>

            <?php if (isset($_SESSION['quiz_settings']['questions'][$current_question])): ?>
                <?php
                $question_data = $_SESSION['quiz_settings']['questions'][$current_question];
                $correct_answer = $question_data['answer'];
                $choices = [$correct_answer];


                // Generating 3 Incorrect Choices
                while (count($choices) < 4) {
                    // Generate Random Incorrect Answer
                    $incorrect_answer = rand($correct_answer - $_SESSION['quiz_settings']['max_difference'], $correct_answer + $_SESSION['quiz_settings']['max_difference']);
                    
                    // Ensuring that the Incorrect Answer is not the same as the Correct Asnwer.
                    if ($incorrect_answer !== $correct_answer && !in_array($incorrect_answer, $choices)) {
                        $choices[] = $incorrect_answer;
                    }
                }

                // Shuffle Choices
                shuffle($choices);
                ?>
                
                <h2>Question <?php echo $current_question + 1; ?>:</h2>
                <p><?php echo $question_data['question']; ?> = ?</p>
                <form method="post">
                    <?php foreach (array_slice($choices, 0, 4) as $choice): ?>
                        <input type="radio" name="answer" value="<?php echo $choice; ?>" required> <?php echo $choice; ?><br>
                    <?php endforeach; ?>
                    <button type="submit" name="submit_answer">Submit</button>
                    <button type="submit" name="end_quiz">End Quiz</button>
                </form>

            <!-- Validator if Question Data cannot detect -->
            <?php else: ?>
                <p>Error: Question data is missing.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>