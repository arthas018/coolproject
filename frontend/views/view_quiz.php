<?php
$current_page = 'View quiz';
include_once "./../../utils/php_utils.php";

$graded = "TRUE";
$quiz_name = $_POST["quiz_name"];
$quiz = post_curl(Array("quiz_name" => $_POST["quiz_name"], "type" => "get_quiz"), "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
$max_score = 0;
$student_score = 0;
if (isset($_POST["publish"])) {
    $data = Array(
        "quiz_name" => $_POST["quiz_name"],
        "publish" => "TRUE",
        "type" => "publish_quiz"
    );
    $out = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
    header("Location: view_grades.php");

} else if (isset($_POST["view"])) {
     for($i = 0; $i < sizeof($quiz); $i++) {
        $max_score += $quiz[$i]["maxpoints"];
     }

     if ($quiz[0]["answer"] === "NULL" || $quiz[0]["answer"] === NULL) {
        $graded = "FALSE";

    } else {
        $current_page = "Review Graded Quiz";

        for($i = 0; $i < sizeof($quiz); $i++) {
            $student_score += $quiz[$i]["points"];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="../css/style.css" rel="stylesheet">
    <title>Homepage</title>
</head>
<body>

    <?php include_once "./header.php"; ?>

    <div class="app">
        <div class="container-single" id="view-quiz">
            <h2><?php echo $quiz_name?></h2>
            <h3>Score: <?php echo $student_score ?> / <?php echo $max_score ?></h3>
            <form style="margin: .5em .5em; margin-right: auto;" method="POST" action="view_grades.php">
                <input type="submit" value="Go Back">
            </form>

            <?php
            if ($graded === "TRUE") {
                echo "<form method='POST' action='frontend.php'>
                <input type='hidden' name='type' value='update_quiz_prof'>
                <input type='hidden' name='quiz_name' value='{$quiz_name}'>
                <input type='hidden' name='publish' value='{$quiz[0]["publish"]}'/>";


                for ($i=0; $i < sizeof($quiz); $i++) {
                    $comments = $quiz[$i]["comments"];
                    $dbcomments = $quiz[$i]["comments"];
                    // Split comments by ; delimiter
                    $comments = explode(";", $comments);
                    // remove empty elements
                    $comments = implode("\n", $comments);
		            $cmts = explode("\n", $comments);

                    $comments = array_values($cmts);
                    $comments = array_filter($cmts);
                    $comments = implode("\n", $comments);
                    echo "<input type='hidden' name='questions[]' value='{$quiz[$i]["question"]}'>
                    <input type='hidden' name='answers[]' value='{$quiz[$i]["answer"]}'>
                    <input type='hidden' name='testcases[]' value='{$quiz[$i]["testcases"]}'/>
                    ";
                    echo "<div class='view-quiz-question'>
                    <h2>Q$i</h2>
                    <strong><label>Question:</label></strong>
                    <p>{$quiz[$i]["question"]}</p>
                    <strong><label>Student Answer:</label></strong>
                    <pre><code>{$quiz[$i]["answer"]}</code></pre>
                    <strong><label>Points:</label></strong>
                    <input type='number' name='points[]' placeholder='Points' value='{$quiz[$i]["points"]}' max='{$quiz[$i]["maxpoints"]}' min='0' style='width: 55px'><br>
                    <strong><label>Comments:</label></strong>
                    <textarea class='textarea-input' name='comments[]' style='font-size: 12px;'>{$comments}</textarea>
                    </div>";
                }

                echo "<input type='submit' value='Submit'>
                </form>";
            } else {
                for ($i=0; $i < sizeof($quiz); $i++) {
                    echo "<div class='view-quiz-question'>
                        <strong><label>Question:</label></strong>
                        <p>{$quiz[$i]["question"]}</p>
                        <strong><label>Max Points:</label></strong>
                        <p>{$quiz[$i]["maxpoints"]}</p>
                        <strong><label>Test cases:</label></strong>
                        <p>{$quiz[$i]["testcases"]}</p>
                        </div>";
                }

            }
            ?>


        </div>
    </div>


    <script type="text/javascript" src="../js/utils.js"></script>

</body>
</html>
