<?php
// Start a session to store the number and difficulty between requests
session_start();

// Set the difficulty level and range based on user input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['difficulty'])) {
    $difficulty = $_POST['difficulty'];
    $_SESSION['difficulty'] = $difficulty;

    switch ($difficulty) {
        case 'easy':
            $maxNumber = 10;
            break;
        case 'medium':
            $maxNumber = 30;
            break;
        case 'hard':
            $maxNumber = 50;
            break;
        default:
            $maxNumber = 10;
    }

    $_SESSION['number'] = rand(1, $maxNumber);
    $_SESSION['attempts'] = 0;
} elseif (isset($_SESSION['difficulty'])) {
    $difficulty = $_SESSION['difficulty'];

    switch ($difficulty) {
        case 'easy':
            $maxNumber = 10;
            break;
        case 'medium':
            $maxNumber = 30;
            break;
        case 'hard':
            $maxNumber = 50;
            break;
        default:
            $maxNumber = 10;
    }
} else {
    $difficulty = 'easy';
    $maxNumber = 10;
    $_SESSION['number'] = rand(1, $maxNumber);
    $_SESSION['attempts'] = 0;
}

$number = $_SESSION['number'];
$message = "";

// Handle the guessing logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guess'])) {
    $guess = (int)$_POST['guess'];
    $_SESSION['attempts']++;

    if ($guess < $number) {
        $message = "Too low! Try again.";
    } elseif ($guess > $number) {
        $message = "Too high! Try again.";
    } else {
        $message = "🎉 Congratulations! You guessed the number in " . $_SESSION['attempts'] . " attempts.";
        $gameOver = true;
    }
}

// Handle "Try Again" logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['try_again'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 2.5rem;
        }
        .game-container {
            background: #ffffff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        form {
            margin-top: 20px;
        }
        input[type="number"], input[type="radio"] {
            margin: 10px 0;
        }
        button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .try-again {
            background-color: #ff5722;
        }
        button.try-again:hover {
            background-color: #e64a19;
        }
        p {
            font-size: 1.2rem;
            color: #555;
        }
        .message {
            font-size: 1.5rem;
            color: #ff5722;
            font-weight: bold;
        }
        .difficulty-options label {
            font-size: 1.1rem;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="game-container">
        <h1>Guess the Number Game</h1>
        <?php if (!isset($_SESSION['difficulty'])) : ?>
            <p>Select a difficulty level to start:</p>
            <form method="post">
                <div class="difficulty-options">
                    <label>
                        <input type="radio" name="difficulty" value="easy" required> Easy (1-10)
                    </label><br>
                    <label>
                        <input type="radio" name="difficulty" value="medium"> Medium (1-30)
                    </label><br>
                    <label>
                        <input type="radio" name="difficulty" value="hard"> Hard (1-50)
                    </label>
                </div>
                <button type="submit">Start Game</button>
            </form>
        <?php else : ?>
            <?php if (isset($gameOver) && $gameOver) : ?>
                <p class="message"><?php echo $message; ?></p>
                <form method="post">
                    <button type="submit" name="try_again" class="try-again">Try Again</button>
                </form>
            <?php else : ?>
                <p>I'm thinking of a number between 1 and <?php echo $maxNumber; ?>. Can you guess what it is?</p>
                <?php if ($message) : ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="post">
                    <label for="guess">Your Guess:</label><br>
                    <input type="number" id="guess" name="guess" min="1" max="<?php echo $maxNumber; ?>" required><br>
                    <button type="submit">Submit</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
