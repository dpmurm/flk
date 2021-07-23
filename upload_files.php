<!DOCTYPE HTML>
<html>
<head>
    <title>Автоматическая загрузка и обработка файлов</title>
    <meta charset="utf-8">
    <script src="js/jquery.min.js"></script>
</head>
<h1>Автоматическая загрузка и обработка файлов</h1>
<p>На этой странице происходит групповая загрузка файлов. Вернуться к списку <a href="index.php">протоколов ФЛК</a>.
    Перейти к списку <a href="flk_protokol_upload.php">загруженных файлов</a>.</p>
<?php
if (isset($_POST['submit'])) {

    // Count total files
    $countfiles = count($_FILES['file']['name']);

    // Looping all files
    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['file']['name'][$i];

        // Upload file
        move_uploaded_file($_FILES['file']['tmp_name'][$i], 'upload/' . $filename);

    }
}
?>
<h2>1. Загрузить файлы во временную папку upload на сервере:</h2>
<form method='post' action='' enctype='multipart/form-data'>
    <input type="file" name="file[]" id="file" multiple>

    <input type='submit' name='submit' value='Загрузить'>
</form>

<?php

//mb_internal_encoding("UTF-8");
/**
 * Поиск файла по имени во всех папках и подпапках
 *
 * @param string $folderName - пусть до папки
 * @param string $fileName - искомый файл
 */
// Поиск XML файла по папке и ID
function search_file($folderName, $fileName)
{
    $fileName = preg_replace('/-/', '', $fileName);
    //echo $fileName.' '; //id из EXCEL очищенные от - дефисов
    // ищем в папке XML файлы с ID в конце файла
    $xml_file = glob("$folderName/*$fileName.{xml,XML}", GLOB_BRACE);
    return !empty($xml_file) ? basename($xml_file[0]) : "";
}

// Рисуем таблицу с XML файлами
function outputFiles($path)
{
    echo '
                    <table id="tblData" border="1">
                      <tr>
                        <th>
                          <input type="checkbox" id="chkParent" />
                        </th>
                        <th>Файлы протоколов ФЛК в XLS формате</th>
                        <th>Файлы выгрузки с данными в XML формате</th>
                      </tr>
                     ';
    // Check directory exists or not
    if (file_exists($path) && is_dir($path)) {

        // Search the files in this directory
        $files = glob($path . "/*.xls");
        if (count($files) > 0) {
            // Loop through retuned array
            foreach ($files as $file) {
                if (is_file("$file")) {

                    $file_xls = basename($file);
                    //Вычисление id
                    //$id=preg_replace('/Протокол_ФЛК_/','', basename($file, ".xls"));
                    $result = preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', basename($file, ".xls"), $match);
                    //var_dump( $match);
                    $id = $match[0];
                    //echo '$id='.$id. "<br>";
                    $file_xml = search_file("upload", $id);

                    echo "<tr>";
                    //Определение кодировки EXCEL файла
                    $kodirovka = mb_detect_encoding($file_xls, 'UTF-8', TRUE);
                    if (!$kodirovka) {
                        $file_xls = iconv('windows-1251', 'UTF-8', $file_xls);
                    }
                    echo '<td><input type="checkbox" name="files[]" value="' . $file_xls . '||' . $file_xml . '" /></td>';
                    echo "<td>";
                    // Display only filename
                    echo '<a href="' . $path . '/' . $file_xls . '" title="скачать файл"> ' . rawurldecode($file_xls) . '</a> ';
                    ////////////////////////////////////////////////////////////////////////
                    echo "</td>";
                    echo "<td>";
                    echo '<a href="' . $path .  '/' . $file_xml . '" title="скачать файл"> ' . $file_xml . '</a> ' . "<br>";
                    echo "</td>";
                    ///////////////////////////////////////////////////////////////////////////
                    echo "</tr>";

                } else if (is_dir("$file")) {
                    // Recursively call the function if directories found
                    outputFiles("$file");
                }
            }
        } else {
            echo "ВНИМАНИЕ: Нет файлов в дирректории upload на сервере.";
        }

    } else {
        echo "ERROR: The directory does not exist.";
    }
    echo '</table>';
}

//Форма выделения файлов и импорта в конкретный протокол
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Инициализация переменных
if (isset($_POST['number'])) {
    $number = $_SESSION['fpu']['number'] = $_POST['number'];
} elseif (isset($_SESSION['fpu']['number'])) {
    $number = $_SESSION['fpu']['number'];
} else {
    $number = 0;
}

if (isset($_POST['date'])) {
    $date = $_SESSION['fpu']['date'] = $_POST['date'];
} elseif (isset($_SESSION['fpu']['date'])) {
    $date = $_SESSION['fpu']['date'];
} else {
    $date = "";
}

if (isset($_POST['period_start'])) {
    $period_start = $_SESSION['fpu']['period_start'] = $_POST['period_start'];
} elseif (isset($_SESSION['fpu']['period_start'])) {
    $period_start = $_SESSION['fpu']['period_start'];
} else {
    $period_start = "";
}

if (isset($_POST['period_stop'])) {
    $period_stop = $_SESSION['fpu']['period_stop'] = $_POST['period_stop'];
} elseif (isset($_SESSION['fpu']['period_stop'])) {
    $period_stop = $_SESSION['fpu']['period_stop'];
} else {
    $period_stop = "";
}
if (isset($_POST['vid_object'])) {
    $vid_object = $_SESSION['fpu']['vid_object'] = $_POST['vid_object'];
} elseif (isset($_SESSION['fpu']['vid_object'])) {
    $vid_object = $_SESSION['fpu']['vid_object'];
} else {
    $vid_object = "0";
}

if (isset($_POST['type_unloading'])) {
    $type_unloading = $_SESSION['fpu']['type_unloading'] = $_POST['type_unloading'];
} elseif (isset($_SESSION['fpu']['type_unloading'])) {
    $type_unloading = $_SESSION['fpu']['type_unloading'];
} else {
    $type_unloading = "0";
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<form enctype="multipart/form-data" method="POST" name="flk_donwload" action="">';
// Call the function
outputFiles("upload");
//echo 'Результат поиска:'.search_file("upload", 'aee0f60c-a459-47e6-82c3-40ca99d0fa82');
echo '<h2>' . '2. Импорт выделенных файлов в протокол:' . '</h2>';
echo '№ протокола <input required="" type="number" class="number w40" name="number" value="' . $number . '">';
echo 'Дата создания <input required="" type="date" class="date w130" name="date"  value="' . $date . '">';
echo 'Начало периода <input required="" type="date" class="date w130" name="period_start"  value="' . $period_start . '">';
echo 'Конец периода <input required="" type="date" class="date w130" name="period_stop"  value="' . $period_stop . '">';
/*
echo '<select class="main" name="vid_object">
                    <option value="ЗУ">ЗУ</option>
                    <option value="ОКС">ОКС</option>
                    <option value="ПИК">ПИК</option>                
      </select>';
*/
echo 'Тип выгрузки <select class="main" name="type_unloading">
                    <option value="П" >Периодическая</option>
                    <option value="Г">Ежегодная</option>
                    <option value="К">Корректирующая</option>
                    <option value="Т">Тестовая</option>
                    <option value="С">СВЕРКА (с ФЛК)</option>
                    <option value="Ф">ФЛК (без ФЛК)</option>                
       </select>';
//echo '<input required="" type="file" class="button fileupload w100pt" name="filexls" accept="application/vnd.ms-excel">';
//echo '<input required="" type="file" class="button fileupload w100pt" name="filexml" accept="text/xml">';
echo '<button class="button" type="submit" name="flk_upload_submit" value="add">Импортировать</button>';
echo '</form >';
//Обработка выделенных файлов из таблицы
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (isset($_POST['flk_upload_submit']) and $_POST['flk_upload_submit'] === 'add' and !empty($_POST['files']) and !empty($_POST['number']) and !empty($_POST['date']) and !empty($_POST['type_unloading'])) {
    echo 'Информация по обработке файлов' . "<br>";
    //print_r($_POST);
    //Подгружаем функцию загрузки function flk_protokol_parsing()
    require_once("functions/fn_protokol_upload.php");
    //Подгружаем $link
    require_once("config.php");
    require_once('matching.php');
    require_once('PHPExcel/PHPExcel.php');


    foreach ($_POST['files'] as $file) {
        list($xls, $xml) = explode("||", $file);
        echo 'xls:' . $xls . '; xml:' . $xml . "<br>";


        if (urlencode(urldecode($xls)) === $xls) {
            //echo 'string urlencoded';
            $file_xls = urldecode($xls);
        } else {
            //echo 'string is NOT urlencoded';
            $file_xls = $xls;
        }
        // if( preg_match("/%/", $xls) ) {
        //    $file_xls = urldecode($xls);
        // } else {$file_xls =$xls;}

        $kodirovka2 = mb_detect_encoding($xls, 'UTF-8', TRUE);
        //Если кодировка UTF-8 возвращаем родную для винды
        if ($kodirovka2) {
            $tmp_file_xls = iconv('UTF-8', 'windows-1251', $xls);
        }

        if (file_exists("upload/$tmp_file_xls")) {
            $tmp_file_xls = "upload/$tmp_file_xls";
        } else {
            echo 'Файл не найден: ' . "upload/$tmp_file_xls";
        }
        $file_xml = $xml;

        //Загрузка
        //function flk_protokol_parsing($link, $arr_xls_heads,$number, $date, $period_start, $period_stop, $visible, $type_unloading, $vid_object, $protokol_uid, $file_xls, $tmp_file_xls, $file_xml)
        flk_protokol_parsing($link, $arr_xls_heads, $number, $date, $period_start, $period_stop, 1, $type_unloading, 0, 0, $file_xls, $tmp_file_xls, $file_xml);
        if ($good === 1) {
            //Кодировка win1251 для сохранения в системе виндовс
            $type_unloading_save = iconv('utf-8', 'windows-1251', $type_unloading);
            //Перемещение xls файлов
            if (file_exists("upload/$xls") && file_exists("storage/$date--$type_unloading_save--$number")) {
                $text_xls = iconv('utf-8', 'windows-1251', urldecode($xls));
                if (rename("upload/$xls", "storage/$date--$type_unloading_save--$number/$text_xls")) {
                    echo "Файл $xls перемещен в папку storage/$date--$type_unloading_save--$number" . "<br>";
                };
            } else {
                mkdir("storage/$date--$type_unloading_save--$number");
                $text_xls = iconv('utf-8', 'windows-1251', urldecode($xls));
                if (rename("upload/$xls", "storage/$date--$type_unloading_save--$number/$text_xls")) {
                    echo "Файл $xls перемещен в папку storage/$date--$type_unloading_save--$number" . "<br>";
                };
            }
            //Перемещение xml файлов
            if (file_exists("upload/$xml") && file_exists("storage/$date--$type_unloading_save--$number")) {
                $text_xml = iconv('utf-8', 'windows-1251', urldecode($xml));
                if (rename("upload/$xml", "storage/$date--$type_unloading_save--$number/$text_xml")) {
                    echo "Файл $xml перемещен в папку storage/$date--$type_unloading_save--$number" . "<br>";
                };
            } else {
                mkdir("storage/$date--$type_unloading_save--$number");
                $text_xml = iconv('utf-8', 'windows-1251', urldecode($xml));
                if (rename("upload/$xml", "storage/$date--$type_unloading_save--$number/$text_xml")) {
                    echo "Файл $xml перемещен в папку storage/$date--$type_unloading_save--$number" . "<br>";
                };
            }
        }


    }
}

?>

<script>
    $(document).ready(function () {
        $('#chkParent').click(function () {
            var isChecked = $(this).prop("checked");
            $('#tblData tr:has(td)').find('input[type="checkbox"]').prop('checked', isChecked);
        });

        $('#tblData tr:has(td)').find('input[type="checkbox"]').click(function () {
            var isChecked = $(this).prop("checked");
            var isHeaderChecked = $("#chkParent").prop("checked");
            if (isChecked == false && isHeaderChecked)
                $("#chkParent").prop('checked', isChecked);
            else {
                $('#tblData tr:has(td)').find('input[type="checkbox"]').each(function () {
                    if ($(this).prop("checked") == false)
                        isChecked = false;
                });
                console.log(isChecked);
                $("#chkParent").prop('checked', isChecked);
            }
        });
    });
</script>
</body>
</html>