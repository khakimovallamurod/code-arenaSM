<?php
    include_once 'config.php';
    $db = new Database();
    $user_id  = intval($_POST['user_id']);
    $cn_problem_id = intval($_POST['problem_id']);
    $tests = $db->get_data_by_table_all('contest_tests', "WHERE cn_problem_id = '$cn_problem_id'");

    function run_code($language, $code, $stdin) {
        $url = "http://api.sampc.uz/api/v2/execute";

        $payload = [
            "language" => $language,
            "version" => "*",
            "files" => [["content" => $code]],
            "stdin" => $stdin
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $resp = curl_exec($ch);
        curl_close($ch);

        return json_decode($resp, true);
    }

    function check_problem($language, $code, $tests) {
        $test_count = 0;
        $last_runTime = 0;
        $last_memory = 0;
        $total_tests = count($tests);
        foreach ($tests as $test) {
            $test_input = $test['input'];
            $expected_output = $test['output'];

            $result = run_code($language, $code, $test_input);

            $actual_output = trim($result['run']['stdout'] ?? '');
            $stderr = $result['run']['stderr'] ?? '';
            $last_runTime = $result['runTime'] ?? 0;
            $last_memory = $result['run']['memoryUsage'] ?? 0;

            if ($stderr !== "") {
                return [
                    "tests_passed" => $test_count,
                    "total_tests" => $total_tests,
                    "status" => "Runtime Error",
                    "runTime" => $last_runTime,
                    "memory" => $last_memory
                ];
            }

            if ($actual_output === $expected_output) {
                $test_count++;
            } else {
                return [
                    "tests_passed" => $test_count + 1,
                    "total_tests" => $total_tests,
                    "status" => "Wrong Answer",
                    "runTime" => $last_runTime,
                    "memory" => $last_memory
                ];
            }
        }

        return [
            "tests_passed" => $test_count,
            "total_tests" => $total_tests,
            "status" => "Accept",
            "runTime" => $last_runTime,
            "memory" => $last_memory
        ];
    }
    
    
    $language = $_POST['language'];
    $contest_id = intval($_POST['contest_id']);
    $code = $_POST['code'];
    $result = check_problem($language, $code, $tests);
    $arr = [
        "user_id"      => $user_id,       
        "problem_id"   => $cn_problem_id,               
        "contest_id"   => $contest_id,               
        "language"     => $language,             
        "code"         => $code,
        "status"       => $result['status'],           
        "tests_passed" => $result['tests_passed'],                  
        "total_tests"  => $result['total_tests'],                   
        "runTime"  => $result['runTime'],                   
        "memory"  => $result['memory']                
    ];
    $insert  = $db->insert("contest_attempts", $arr);
    $problem = $db->get_data_by_table('contest_problems', ['id'=>$cn_problem_id]);
    $user_reyting = $db->get_data_by_table('contest_reyting', ['user_id'=>$user_id, 'problem_id'=>$cn_problem_id]);
    if ($insert!=0) {
        $difficulty = $problem['difficulty'];
        $score = 0;
        switch ($difficulty) {
            case 'beginner':$score = 100;break;
            case 'easy':$score = 200;break;
            case 'medium':$score = 300;break;
            case 'hard':$score = 400;break;
            case 'expert':$score = 500;break;
            default:$score = 0;break;
        }
        $reyting_arr = [
            "user_id" => $user_id,
            "problem_id" => $cn_problem_id,
            "contest_id" => $contest_id,
            "score" => $score,             
            "attempted" => 1,           
            "solved" => 0,           
        ];
        if ($result['status'] == "Accept"){
            $reyting_arr['solved'] += 1;
        }
        if ($user_reyting == NULL){
            $insert_reyting  = $db->insert("contest_reyting", $reyting_arr);
        }else{
            $reyting_id = $user_reyting['id'];
            $attempted = $user_reyting['attempted'] += 1;
            $solved = max($reyting_arr['solved'], $user_reyting['solved']);
            $db->update(
                'contest_reyting', 
                [
                    'solved'=>$solved,
                    'attempted'=>$attempted
                ], 
                "id = $reyting_id"
            );
        }
        echo json_encode(['success' => true, 'message' => 'Dasturingiz yuborildi!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Yuborishda xatolik!']);
    }
?>
