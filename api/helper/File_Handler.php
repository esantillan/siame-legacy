<?php

namespace helper;

use config\Constants as Constants;

/**
 * Clase manejadora de archivos (para subir o descargar)
 * @PENDING subida de múltiples archivos
 */
class File_Handler {

    /**
     * OpenOffice, Word 97-2003, Word 'actual'
     */
    const DOC = [
        'application/vnd.oasis.opendocument.text; charset=binary',
        'application/msword; charset=binary',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document; charset=binary',
    ];

    /**
     * JPEG, PNG, BMP
     */
    const IMG = [
        'image/jpeg; charset=binary',
        'image/png; charset=binary',
        'image/x-ms-bmp; charset=binary',
    ];

    /**
     * OpenOffice, Excel 97-2003, Excel 'actual'
     */
    const HOJA_DE_CALCULO = [
        'application/vnd.oasis.opendocument.spreadsheet; charset=binary',
        'application/vnd.ms-excel; charset=binary',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=binary'
    ];

    /**
     * OpenOffice, PowerPoint 97-2003, PowerPoint 'actual'
     */
    const PPT = [
        'application/vnd.openxmlformats-officedocument.presentationml.presentation; charset=binary',
        'application/vnd.oasis.opendocument.presentation; charset=binary',
        'application/vnd.ms-powerpoint; charset=binary',
    ];

    /**
     * PDF
     */
    const PDF = 'application/pdf; charset=binary';

    /**
     * CUIDADO: Cualquier tipo de archivo!
     */
    const ALL = 'application/octet-stream; charset=binary';

    /**
     *  Arreglo asociativo CONSTANTE_PREDEFINIDA_PHP => 'MENSAJE'
     * @var array Mensajes de error 
     */
    private $err_msg = [
        UPLOAD_ERR_OK => 'Archivo subido correctamente.',
        UPLOAD_ERR_INI_SIZE => 'El tamaño del archivo ha excedido el tamaño indicado en php.ini .',
        UPLOAD_ERR_FORM_SIZE => 'El tamaño del archivo ha excedido el tamaño máximo para este formulario.',
        UPLOAD_ERR_PARTIAL => 'El archivo ha sido subido parcialmente.',
        UPLOAD_ERR_NO_FILE => 'El archivo no existe.',
        UPLOAD_ERR_NO_TMP_DIR => 'El directorio temporal no existe.',
        UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en el disco.',
        UPLOAD_ERR_EXTENSION => 'Error de extensión PHP.'
    ];

    /**
     *
     * @var String ruta temporal del archivo a subir 
     */
    private $file;

    /**
     * En el constructor se setea como Constants::$ROOT . 'model' . Constants::$DS . 'uploads';
     * @var String Directorio destino 
     */
    private $dir_uploads; //directorio destino
    /**
     *
     * @var String nombre con el que se guardará el archivo
     */
    private $new_name;

    /**
     * 
     * @var array Arreglo que contiene constantes definidas anteriormente 
     */
    private $mime_allowed = array();

    public function __construct() {
        $this->dir_uploads = Constants::$ROOT . 'model' . Constants::$DS . 'uploads';
    }

    /**
     * Método para subir un archivo
     * @param String $file_name_input Atributo HTML "name=" del input 'type="file"'
     * @param String $dir Ruta <b>RELATIVA</b> (a partir de: 'siame/api/model/uploads') donde se alamacenará el archivo
     * @param array $mime_allowed arreglo que <b>DEBE</b> contener al menos una de las constantes definidas en esta clase
     * @throws \Exception
     * @return String Nombre del archivo
     */
    public function upload_file($file_name_input, $dir, $mime_allowed = array()) {
        if ($_FILES[$file_name_input]['error'] == $this->err_msg[UPLOAD_ERR_OK]) {
            foreach ($mime_allowed as $key => $mime) {
                if (is_array($mime)) {
                    foreach ($mime as $m) {
                        $this->mime_allowed[] = $m;
                    }
                } else {
                    $this->mime_allowed[] = $mime;
                }
            }
            $this->file = $_FILES[$file_name_input]['tmp_name'];
            Sanitizer::sanitize_string($_FILES[$file_name_input]['name']);
            $this->new_name = rand(100, 1000) . '_' . $_FILES[$file_name_input]['name'];
            $this->dir_uploads .= Constants::$DS . $dir;
            $this->check_allowed();
            $this->check_dir();
            $this->dir_uploads .= Constants::$DS . $this->new_name;
            if (!move_uploaded_file($this->file, $this->dir_uploads)) {
                throw new \Exception('ERROR: No se ha podido mover el archivo de la carpeta temporal hacia el destino.');
            }
            return $this->new_name;
        } else {
            throw new \Exception('ERROR: Ha ocurrido el siguiente error al subir el archivo: ' . $this->err_msg[$_FILES[$file_name_input]['error']]);
        }
    }

    /**
     * Comprueba si el <code>tipo MIME</code> se encuentra dentro de los tipos permitidos
     * @throws \Exception
     */
    private function check_allowed() {
        if (!empty($this->mime_allowed)) {
            $finfo = new \finfo(FILEINFO_MIME);
            $mime_type = $finfo->file($this->file);
            if (!in_array($mime_type, $this->mime_allowed)) {
                throw new \Exception('ERROR: Tipo de archivo no permitido.');
            }
        }
    }

    /**
     * Comprueba si existe el directorio donde se almacenará el archivo, si no existe lo crea
     * @throws \Exception
     */
    private function check_dir() {
        if (!file_exists($this->dir_uploads)) {
            if (!mkdir($this->dir_uploads, 0777, true)) {
                throw new \Exception('ERROR: No se ha podido crear el directorio en el servidor.');
            }
        }
    }

    /**
     * Descarga un archivo 
     * @param string $dir_file <b>RELATIVA</b> (a partir de: 'siame/api/model/uploads') donde se alamacenará el archivo
     * @throws \Exception
     */
    public function download_file($dir_file) {
        $dir_file = $this->dir_uploads . Constants::$DS . $dir_file;
        if (is_file($dir_file)) {
            $finfo = new \finfo(FILEINFO_MIME);
            $mime_type = $finfo->file($dir_file);
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename=' . $dir_file);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($dir_file));
            readfile($dir_file);
        } else {
            throw new \Exception("ERROR: No se reconoce como archivo a '{$dir_file}'");
        }
    }

}
