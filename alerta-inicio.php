<?php
function alertaYRedirigirInicio($mensaje) {
    echo "<script>
        alert('" . addslashes($mensaje) . "');
        window.location.href = 'index.php';
    </script>";
    exit();
}
?>