<?php

/**
 * Dejo este archivo para evitar (en caso de falla de apache) que listen el directorio,
 * lo único que hago aquí es redireccionar a un 404
 */
header("HTTP/1.1 404 Not Found");
header("Location: /siame/api/error/error_404/Esta_intentando_acceder_al_index_que_está_fuera_de_la_carpeta_API");

