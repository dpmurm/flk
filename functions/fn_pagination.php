<?php
function fn_count_pages($link, $on_page, $query_count_pages)
{
    // Получаем количество записей в базе
    $res_count = mysqli_query($link, $query_count_pages);
    $count_records = mysqli_fetch_array($res_count);
    $count_records = $count_records['count'];

    // Получаем количество страниц
    $num_pages = ceil($count_records / $on_page);
    if ($num_pages <= 0) {
        $num_pages = 1;
    }

    // Текущая страница
    // Если параметр не определен, то текущая страница равна 1
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Если текущая страница меньше единицы, то страница равна 1
    // Если текущая страница больше общего количества страниц, то
    // текущая страница равна количеству страниц
    if ($current_page < 1) {
        $current_page = 1;
    } elseif ($current_page > $num_pages) {
        $current_page = $num_pages;
    }

    // Начать получение данных от числа (текущая страница - 1) * количество записей на странице
    $start_from = ($current_page - 1) * $on_page;

    return array
    (
        'on_page' => $on_page,
        'num_pages' => $num_pages,
        'current_page' => $current_page,
        'start_from' => $start_from
    );
}

function fn_list_pages($current_page, $num_pages)
{
    $length_str = 4;
    $min = $current_page - $length_str;
    $max = $current_page + $length_str;

    if ($num_pages == 1) {
        return false;
    }

    echo '<div class="pagination">';
    if ($min <= 0) {
        $max = $current_page + $length_str - $min + 1;
    }
    if ($max > $num_pages) {
        $min = $num_pages - ($max - $current_page + $length_str);
    }

    for ($page = 1; $page <= $num_pages; $page++) {
        $spr = '&nbsp;&nbsp;';
        $spr_gap = '&nbsp;&nbsp;';
        if (($min < $page) and ($page < $max)) {
            if ($page == $current_page) {
                echo $spr . '<strong>' . $page . '</strong>' . $spr;
            } else {
                echo $spr . '<a href="?page=' . $page . '">' . $page . '</a>' . $spr;
            }
        } elseif ($page == 1) {
            if ($current_page > ($page + $length_str)) {
                $spr_gap = ' ...';
            }
            if ($page == $current_page) {
                echo $spr . '<strong>' . $page . '</strong>' . $spr_gap;
            } else {
                echo $spr . '<a href="?page=' . $page . '">' . $page . '</a>' . $spr_gap;
            }
        } elseif ($page == $num_pages) {
            if ($current_page < ($page - $length_str)) {
                $spr_gap = '... ';
            }
            if ($page == $current_page) {
                echo $spr_gap . '<strong>' . $page . '</strong>' . $spr;
            } else {
                echo $spr_gap . '<a href="?page=' . $page . '">' . $page . '</a>' . $spr;
            }
        }
    }
    echo '</div>';
}

