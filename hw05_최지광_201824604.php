<?php
    ini_set('display_errors', 1);   // 에러 메시지 출력 On

    // Initialize variables
    $mode = "select";
    $err = "";

    // Check and assignment POST parameters
    if(isset($_POST["mode"])) {
        $mode = $_POST["mode"];
    }

    $studentId = isset($_POST["studentId"]) ? $_POST["studentId"] : "";
    $name = isset($_POST["name"]) ? $_POST["name"] : "";
    
    // Connect to MySQL
    $mysqli = new mysqli("localhost", "root", "", "201824604");
    if($mysqli->connect_errno) {
        die("Failed to connect to MYSQL");
    }
    
    // Sql query processing according to mode
    if ($mode == "insert")
    {
        if($mysqli->query("insert into HW05 set studentId = '".$_POST["studentId"].
        "', name = '".$_POST['name']."'") !== TRUE){
            die("Failed to Insert Data");
        }
    }
    else if ($mode == "delete")
    {
        if($mysqli->query("delete from HW05 where studentId = '".$POST["studentId"]."'") !== TRUE){
            die("Failed to Delete Data");
        }
    }
    else if ($mode == "update")
    {
        if($mysqli->query("update HW05 set name = '".$POST["name"]."' where studentId = '".$POST["studentId"]."'") !== TRUE)
        {
            die("Faile to Update Data");
        }
    }
    
    // Execute select query
    if($result = $mysqli->query("select * from HW05")){
        echo "Select returned " . $result->num_rows . " rows.<br/>";
        while($data_arr = $result->fetch_array()) {
            echo "학번: " . $data_arr['studentId']. ", 이름: " . $data_arr['name'] . "<br/>";
        }
        $result->close();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Q2</title>
        <style>
            p { margin: 0px; }
            input[type = "text"] { width: 250px; }
            span { font-size: 14px; text-decoration: underline; color: blue;}
            span:hover { cursor: pointer; }
            ul { margin-top: 0; }
            li { list-style-type: none; width: 150px;}
            li:nth-child(even) {background-color: lightblue;}
            #moreBtn {visibility: hidden;}
        </style>
        <script>
            let curShowCount = 5;
            let studentArr = <?php echo json_encode($data_arr);?>;    // php 배열을 javascript 배열에 바로 대입하고 싶을 때는 json을 활용
            
            function makeAlert()    // php에서 생성한 에러 메시지를 경고창으로 띄워주는 함수
            {
                var err = "<?php echo $err;?>";
                if (err.length > 0)
                {
                    window.alert(err);
                    return;
                }
            }
            function loadStudents() 
            {
                //let length = localStorage.length;
                let length = studentArr.length;
                //studentArr = new Array();     // 전역 변수로 수정

                //for (let i = 0; i < length; ++i) 
                //{
                //    let student = {id: localStorage.key(i), name: localStorage[localStorage.key(i)]};
                //    studentArr.push(student);
                //}
                //studentArr.sort(compareStudent);      // php에서 db에서 가져올 때 미리 정렬

                let markup = "<div><ul>";
                for (let i = 0; i < curShowCount; i++)
                {
                    if (i >= length)
                        break;
                    markup += "<li><span id = '" + studentArr[i].studentId + "' onclick = 'deleteInfo(" + i + ")'>" 
                        + studentArr[i].name + "</span>" + "(" +
                        "<span id = '" + studentArr[i].studentId + "' onclick = 'editInfo(" + i + ")'>" 
                        + studentArr[i].studentId + "</span>" + ")</li>";
                }
                markup += "</ul></div>";
                document.getElementById("studentBook").innerHTML = markup;

                if (curShowCount < length)
                    document.getElementById("moreBtn").style.visibility = "visible";
                else
                    document.getElementById("moreBtn").style.visibility = "hidden";
            }
            function saveInfo() 
            {
                let studentId = document.getElementById("studentId");
                let name = document.getElementById("name");                
                if(studentId.value == "")
                {
                    window.alert("학번을 입력하세요.");
                }
                else if(name.value == "")
                {
                    window.alert("이름을 입력하세요.");
                }
                else
                {
                    //localStorage.setItem(studentId.value, name.value);
                    //studentId.value = "";
                    //name.value = "";                    
                    //loadStudents();
                    if (document.getElementById("mode").value != "update") 
                        document.getElementById("mode").value = "insert";
                    document.forms[0].submit();
                }
            }
            function deleteInfo( index ) 
            {
                if (confirm(studentArr[index].name + " 학생의 정보를 지우시겠습니까?"))
                {
                    //localStorage.removeItem( studentId );
                    //loadStudents();
                    document.getElementById("studentId").value = studentArr[index].studentId;
                    document.getElementById("mode").value = "delete";
                    document.forms[0].submit();
                }
            }
            function editInfo( index )
            {
                document.getElementById("studentId").value = studentArr[index].studentId;
                document.getElementById("name").value = studentArr[index].name;
                document.getElementById("mode").value = "update";
                loadStudents();
            }
            function showMoreStudent()
            {
                curShowCount += 5;
                loadStudents();
            }
            function start()
            {
                makeAlert();    // 시작할 때 경고 메시지 먼저 보여줌
                let saveBtn = document.getElementById( "saveBtn" );
                saveBtn.addEventListener( "click", saveInfo, false );
                let moreBtn = document.getElementById( "moreBtn" );
                moreBtn.addEventListener( "click", showMoreStudent, false );
                loadStudents();
            }

            window.addEventListener( "load", start, false );
        </script>
    </head>
    <body>
        <h1>간이 학생명부</h1>
        <form method="POST" action="Q2_base.php">
            <p><input id = "studentId" name = "studentId" type = "text" placeholder = "학번을 입력하세요"></p>
            <p><input id = "name" name = "name" type = "text" placeholder = "이름을 쓰세요">
            <input type = "button" value = "Save" id = "saveBtn"></p>
            <!--submit 구분을 위한 hidden input 추가-->
            <input type="hidden" name="mode" id="mode" value="insert"/>
        </form>
        <h1>저장된 학생 정보</h1>
        <div id = "studentBook"></div>
        <input type = "button" value = "더보기" id = "moreBtn"></p>
    </body>
</html>