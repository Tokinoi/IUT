<?php
// Répertoire courant (celui qui contient autoload.php)
spl_autoload_register(function ($className) {
    @include strtr($className, "\\", "/") . ".php";
});
// Répertoire Classes du répertoire courant
spl_autoload_register(function ($className) {
    @include "Classes/".strtr($className, "\\", "/").".php";
});
