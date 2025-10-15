/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "placeholder" plugin.
 *
 */
/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "placeholder" plugin.
 *
 */

(function () {
    var placeholderReplaceRegex = /#FIELD_\d+/g;
    CKEDITOR.plugins.add('field_placeholder', {
        requires: 'fakeobjects',
        onLoad: function () {
            CKEDITOR.addCss('.cke_field_placeholder' +
                '{text-align: center;padding:2px;background-color:#19396e;color:white;margin:0 2px;font-family:tahoma;}'
            );
        },
        init: function (editor) {
        },
        afterInit: function (editor) {
            var dataProcessor = editor.dataProcessor,
                dataFilter = dataProcessor && dataProcessor.dataFilter,
                htmlFilter = dataProcessor && dataProcessor.htmlFilter;

            if (dataFilter) {
                dataFilter.addRules({
                    text: function (text) {
                        return text.replace(placeholderReplaceRegex, function (match) {
                            return CKEDITOR.plugins.placeholder.createPlaceholder(editor, match, 1);
                        });
                    }
                });
            }

            if (htmlFilter) {
                htmlFilter.addRules({
                    elements: {
                        'img': function (element) {
                            if (element.attributes && element.attributes[ 'data-cke-field-placeholder' ]) {
                                var text = element.attributes['data-id'];
//                               alert(CKEDITOR.instances['format'].getData());
                                delete element.name;
                            }
                        }
                    }
                });
            }
        }
    });
})();

CKEDITOR.plugins.placeholder = {
    createPlaceholder: function (editor, text, isGet) {
        var element = CKEDITOR.dom.fromHtml();
        var element = new CKEDITOR.dom.element('img', editor.document);
        element.setAttributes({
            contentEditable: 'false',
            'data-cke-field-placeholder': 1,
            'class': 'cke_field_placeholder',
            'data-id': text,
            alt: System.FormsManager.Fields[text],
            title: System.FormsManager.Fields[text]
        });

        text && element.setText(' ' + System.FormsManager.Fields[text] + ' ');

        if (isGet)
            return element.getOuterHtml();

        editor.insertElement(element);
        return null;
    }
};