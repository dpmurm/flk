<?php
// Функция очистки URL от параметров запроса
function clear_url()
{
    echo '<script type="text/javascript">
	window.history.replaceState(null, null, window.location.pathname);
	</script>';

}
