<?php

class App_MimeConverter
{
    private static $_extTypeMap = array(
        '.docm' => "application/vnd.ms-word.document.macroEnabled.12",
        '.docx' => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        '.dotm' => "application/vnd.ms-word.template.macroEnabled.12",
        '.dotx' => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
        '.potm' => "application/vnd.ms-powerpoint.template.macroEnabled.12",
        '.potx' => "application/vnd.openxmlformats-officedocument.presentationml.template",
        '.ppam' => "application/vnd.ms-powerpoint.addin.macroEnabled.12",
        '.ppsm' => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12",
        '.ppsx' => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
        '.pptm' => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
        '.pptx' => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        '.xlam' => "application/vnd.ms-excel.addin.macroEnabled.12",
        '.xlsb' => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
        '.xlsm' => "application vnd.ms-excel.sheet.macroEnabled.12",
        '.xlsx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        '.xltm' => "application/vnd.ms-excel.template.macroEnabled.12",
        '.xltx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
        '.manifest' => "application/manifest",
        '.xaml' =>"application/xaml+xml",
        '.application' => "application/x-ms-application",
        '.deploy' => "application/octet-stream",
        '.xbap'   => "application/x-ms-xbap");
    
    public static function getMimeType($file)
    {
        // Find extension. Return null string if not found
        $ext = (false === $pos = strrpos($file, '.')) ? '' : substr($file, $pos);

        if(array_key_exists($ext,App_MimeConverter::$_extTypeMap))
        {
            return App_MimeConverter::$_extTypeMap[$ext];
        }
        else
        {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo,$file,FILEINFO_MIME_TYPE);
            finfo_close($finfo);
            return $mime;
        }
    }
}
