<?php
namespace Gallery\View\Helper;

use System\View\Helper\BaseHelper;

class PhotoGallery extends BaseHelper
{
    public function __invoke($galleryId, $type = 0, $slider = false, $zoom = false) //$type=0 merge gallery & $type=1 Separate gallery
    {
        $selectGallery = null;
        if (count($galleryId)) {
            if ($type) {
                //separate gallery
            } else {
                $selectGallery = getSM('gallery_item_table')->getAll(array('groupId' => $galleryId));
            }
        }
        return $this->view->render('gallery/gallery/photo-gallery', array(
            'select' => $selectGallery,
            'slider' => $slider,
            'zoom' => $zoom,
        ));
    }
}