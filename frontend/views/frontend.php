<?php

include_once "./../../utils/php_utils.php";

if ($_POST["type"] === "login") {

    // Set the boolean if it has access
    $dbresult = post_curl($_POST, "https://web.njit.edu/~krc9/coolproject/middle/middleLogin.php");

    echo json_encode($dbresult);

} else if ($_POST["type"] === "add_q" || $_POST["type"] === "update_q") {
    $data = $_POST;
    $data["testcases"] = array_filter(explode(';', $data["testcases"]));
    $result = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
    header("Location: homepage_instructor.php");

} else if ($_POST["type"] === "delete_q") {
    $data = $_POST;
    $result = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
    header("Location: homepage_instructor.php");

} else if ($_POST["type"] === "add_quiz") {
    $data = $_POST;
    $data["publish"] = "FALSE";
    $result = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
    header("Location: quiz_bank.php");
} else if ($_POST["type"] === "submit_quiz") {
    $data = $_POST;
    $data["max_quiz_points"] = array_sum($data["points"]);
    $data["testcases"] = array_filter(explode(';', $data["testcases"]));

    $post = curl_init();
    curl_setopt($post, CURLOPT_URL, "https://web.njit.edu/~krc9/coolproject/middle/betagrader.php");
    curl_setopt($post, CURLOPT_POST, 1);
    curl_setopt($post, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($post, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($post, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($post, CURLOPT_SSL_VERIFYPEER, 0);

    $result = curl_exec($post);
    $info = curl_error($post);

    if (curl_error($post)) {
        return Array("error" => curl_error($post));
    }

    curl_close($post);

    // echo $result;
    header("Location: homepage_student.php");
} else if ($_POST["type"] === "update_quiz") {
    $data = $_POST;
    $data["FULLL_GRADED_EXAM_COMMENTS"] = array();
    for ($i = 0; $i < sizeof($data["points"]); $i++) {
        $testcases = array();
        array_push($testcases, $data["testcases"][$i]);

        array_push($data["FULLL_GRADED_EXAM_COMMENTS"], Array(
            "Question_Final_Grade" => $data["points"][$i],
            "Function" => "",
		    "Parameters" => "",
            "Return" => "",
            "Student_Answer" => $data["answers"][$i],
		    "Output" => $data["comments"][$i],
		    "Testcases" => $testcases
        ));
    }
    array_push($data["FULLL_GRADED_EXAM_COMMENTS"], "Grade");
    array_push($data["FULLL_GRADED_EXAM_COMMENTS"], "{$data["quiz_name"]}");

    $result = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");

    header("Location: view_grades.php");
} else if ($_POST["type"] === "delete_quiz") {
    $data = $_POST;
    $result = post_curl($data, "https://web.njit.edu/~krc9/coolproject/middle/middle_to_db.php");
    header("Location: view_grades.php");
}
