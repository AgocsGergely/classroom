<?php

define('NAME', 0);
define('CLASSES', 1);
define('GENDER', 2);
define('SUBJECTS', 3);

session_start();

require_once "classroom-helper.php";
require_once "classroom-data.php";
$data = getData();
htmlHead();

$book = generateSchoolbook(DATA);

function showNav($data){ //BUTTONS
    
    echo '
<nav class="mt-4">
<h1 class="text-center">Osztálynapló 2024-2025</h1>
    <form name="nav" method="post" action="" class="text-center align-middle">
        <div class="d-flex justify-content-center gap-3">
            <button type="submit" name="btn-all" value="1" class="btn btn-primary">Összes diák</button>';
            foreach ($data["classes"] as $class) {
                echo '<button type="submit" name="' . $class . '" value="1" class="btn btn-outline-primary">' . $class . '</button>';
            }
        echo '
        </div>
    </form>
</nav>
<form method="POST">
    <div class=text-center>
    <br>
        <button type="submit" name="generate_csv" class="btn btn-success btn-lg rounded-pill shadow">Napló Mentése CSV fájlba</button>
    </div>
</form>';
} //

function showFunction($book) { 
    
    if (isset($_POST["btn-all"])) {
        echo '<h1 class="text-center text-primary bg-light p-3 rounded">Kiválasztva: Összes osztály</h1>'.
        '<div class="container mt-4" style="max-width: 40%;">
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
                <tr>
                    <th>Diák neve</th>
                    <th>Osztály</th>
                    <th>Nem</th>
                    <th>Osztályzatok</th>
                </tr>
              </thead>
              <tbody>';
        // Wrap the table with a container div and set the width to 75%
        
        
        for ($i = 0; $i < count($book); $i++) { // $i == Student
            echo '<tr>'.
            '<td class="text-center align-middle">' . '<p class="fs-2 fw-bold text-success">'. $book[$i][NAME] . '</p>' . '</td>'.
            '<td class="text-center align-middle">' . $book[$i][CLASSES] . '</td>'.
            '<td class="text-center align-middle">' . $book[$i][GENDER] . '</td>'.
            '<td>';
    
            // Osztályzatok táblázata
            echo '<div class="table-responsive">
            <table class="table table-sm table-bordered">
            <thead class="table-light">
                    <tr>
                        <th>Tantárgy</th>
                        <th>Jegyek</th>
                    </tr>
                  </thead>
                  <tbody>';
            
            foreach ($book[$i][SUBJECTS] as $subject => $grades) {
                echo '<tr>'.
                '<td class="text-center align-middle">' . $subject . '</td>'.
                '<td>' . implode(', ', $grades) . '</td>'.
                '</tr>';
            }
            
            echo '</tbody>
            </table>
            </div>
    
            </td>
            </tr>';
        }
    
        echo '</tbody>
        </table>
        </div>
        </div>'; // Close the container
    }
    
    else{
        $selectedClass = "Nincs kiválasztva"; // Default value
    
        for ($i = 0; $i < count($book); $i++) {
            if ($book[$i][CLASSES] == isset($_POST[$book[$i][CLASSES]])) {
                $selectedClass = $book[$i][CLASSES];
                break;
            }
        }
        echo
        "<h1 class='text-center text-primary bg-light p-3 rounded'>Kiválasztva: $selectedClass</h1>".
         '<div class="container mt-4" style="max-width: 40%;">
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
                <tr>
                    <th>Diák neve</th>
                    <th>Nem</th>
                    <th>Osztályzatok</th>
                </tr>
              </thead>
              <tbody>';
    
    
    for ($i = 0; $i < count($book); $i++) {
        if ($book[$i][CLASSES] == isset($_POST[$book[$i][CLASSES]])) {
            /*echo subjectAVG($book);*/
            echo '<tr>
             <td class="text-center align-middle">' . '<p class="fs-2 fw-bold text-success">'. $book[$i][NAME] . '</p>' . '</td>
             <td class="text-center align-middle">' . $book[$i][GENDER] . '</td>
             <td>';
    
            // Osztályzatok táblázata
            echo '<div class="table-responsive">
             <table class="table table-sm table-bordered">
             <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle">Tantárgy</th>
                        <th class="text-center align-middle">Jegyek</th>
                    </tr>
                  </thead>
                  <tbody>';
            
            foreach ($book[$i][SUBJECTS] as $subject => $grades) {
                echo '<tr>
                 <td class="text-center align-middle">' . $subject . '</td>
                 <td>' . implode(', ', $grades) . '</td>
                 </tr>';
            }
            
            echo '</tbody>
             </table>
             </div>
    
             </td>
             </tr>';
        }
    }
    
    echo '</tbody>
    </table>
    </div>
    </div>'; // Close the container
}    
}
function exportToCSV($data, $filename = "export/schoolbook.csv") {
    if(is_dir("export")){
        
    }
    else{ mkdir("export");}
    $file = fopen($filename, "w");
    fwrite($file, "\xEF\xBB\xBF");  //UTF-8 Beállítás

    // Define subjects and CSV header
    $DATA = getData();
    $subjects = [];
    for($i = 0; $i < count($DATA["subjects"]); $i++)
    {
        $subjects[] = $DATA["subjects"][$i];
    }
    
    $header = ['ID', 'Name', 'Firstname', 'Lastname', 'Gender'];
    $header = array_merge($header, array_map('ucfirst', $subjects)); // Add subjects to the header
    fputcsv($file, $header, ";");

    $idCounter = 0;
    foreach ($data as $student) {
        // Generate ID, split name, and convert gender
        $id = $student[1] . '-' . $idCounter++;
        $nameParts = explode(' ', $student[0]);
        $firstname = $nameParts[0];
        $lastname = $nameParts[1];
        $gender = $student[2] == "W" ? 2 : 1;

        // Initialize row with student details
        $row = [$id, $student[0], $firstname, $lastname, $gender];

        // Add grades for each subject
        foreach ($subjects as $subject) {
            $grades = isset($student[3][$subject]) ? implode(",", $student[3][$subject]) : ''; // Format grades as CSV string
            $row[] = $grades; // Add grades to the row
        }

        // Write the row to the CSV
        fputcsv($file, $row, ";");
    }

    fclose($file);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle class selection
    foreach (DATA['classes'] as $class) {
        if (isset($_POST[$class])) {
            $_SESSION['selected_class'] = $class; // Store selected class in session
            break;
        }
    }
    if (isset($_POST['btn-all'])) {
        $_SESSION['selected_class'] = null; // Set to null for all classes
    }

    // Handle CSV generation
    if (isset($_POST['generate_csv'])) {
        $selectedClass = $_SESSION['selected_class'] ?? null;

        // Filter the book data based on the selected class
        $filteredBook = [];
        if ($selectedClass) {
            foreach ($book as $student) {
                if ($student[CLASSES] === $selectedClass) {
                    $filteredBook[] = $student;
                }
            }
        } else {
            // If no specific class is selected, include all students
            $filteredBook = $book;
        }

        // Export to CSV
        if ($selectedClass === null) {
            echo "<p>Nem lett kiválasztva osztály.</p>";
        } else {
            // Generate the CSV first
            exportToCSV($filteredBook);
        
            // Rename the file after confirming it exists
            $originalFile = "export/schoolbook.csv";
            $newFile = "export/" . $selectedClass . "_" . date("Y-m-d-H.i.s") . ".csv";
            if (file_exists($originalFile)) {
                rename($originalFile, $newFile);
                echo "<p>Sikeres mentés: class <strong>{$selectedClass}</strong></p>";
            } else {
                echo "<p>Hiba: Az exportált fájl nem található.</p>";
            }
        }
        
        
    }
}

//
//LEKÉRDEZÉSEK
//

/*function subjectAVG($classes){
    
    $DATA = getData();
    $subjects = [];
    $returnArray = [];
    for($i = 0; $i < count($DATA["subjects"]); $i++)
    {
        $subjects[] = $DATA["subjects"][$i];
    }
    foreach($subjects as $subject){
        for($i = 0; $i < count($classes); $i++){
            $allGrades = 0;
            foreach($classes[$i][SUBJECTS][$subject] as $grade){
                $allGrades += $grade;
            }
        }
    $returnArray[$subject] = $allGrades / count($classes[$i][SUBJECTS][$subject]);

    }
    return $returnArray;
}*/
showNav($data);
/*var_dump($book[1][SUBJECTS]["math"]);*/
showFunction($book);
//if session isset classes