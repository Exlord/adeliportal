<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/12/13
 * Time: 11:07 AM
 */

namespace File\View\Helper;

use System\View\Helper\BaseHelper;

class FileDisplay extends BaseHelper
{
    public function __invoke($files,$maxImage)
    {
        $html = '';
        $this->view->headScript()->appendFile($this->view->basePath() . '/js/file.js');
        foreach ($files as $file) {
            $file_path = $file;
            if (file_exists(PUBLIC_PATH . $file_path)) {
                if (!empty($file_path)) {
                    $file_name = end(explode('/', $file_path));

                    //check mime type and if mime is image  resize image is shows to user
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $deleteLink = "";
                    if (strstr(finfo_file($finfo, PUBLIC_PATH . $file_path), 'image') != FALSE) {
                        $resizeFilePath = getThumbnail()->thumbnail($file_path, 150,150);
                        $title = t('Delete');
                        $fileSrc = base64_encode(PUBLIC_PATH . $file_path);
                        $url = url('app/delete-file', array('file' => $fileSrc));
                        $file_name = "<img src='$resizeFilePath' >";
                        $deleteLink = "<a href='#' rel='tooltip' title='$title' data-src='$url' class='btn btn-default fileDelete'><span class=' glyphicon glyphicon-remove text-danger'></span></a>";
                    }
                    finfo_close($finfo);
                    $html .= sprintf("<span class='imageWithDelete'>%s<a href='%s'>%s</a></span>", $deleteLink, $file_path, $file_name);
                }
            }

        }


        $html .="<div class='message info'>".sprintf(t('You uploaded %s images of %s image'), "<em>".count($files)."</em>", "<em>".$maxImage."</em>")."</div>";
        return $html;

    }
}