/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
//THIS IS SPARTA
CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    config.extraAllowedContent = 'table td tr a div img span p ul li[*]{*}(*);';
    config.filebrowserBrowseUrl = '/admin/file/public-manager';
    config.filebrowserImageBrowseUrl = '/admin/file/public-manager';
    config.filebrowserFlashBrowseUrl = '/admin/file/public-manager' ;
//    config.extraPlugins = 'field_placeholder'

};