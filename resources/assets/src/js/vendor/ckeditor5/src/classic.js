import ClassicEditorBase from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import plugins from './plugins';

class ClassicEditor extends ClassicEditorBase {};

ClassicEditor.builtinPlugins = plugins;

ClassicEditor.defaultConfig = {
    toolbar: {
        items: [
            'removeFormat',
            'undo',
            'redo',
            '|',
            'outdent',
            'indent',
            '|',
            'heading',
            'alignment',
            '|',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'horizontalLine',
            '|',
            'code',
            '|',
            'link',
            'bulletedList',
            'numberedList',
            'todoList',
            '|',
            'blockQuote',
            'insertTable',
            'imageUpload',
            'mediaEmbed'
        ],
        viewportTopOffset: 50
    },
    blockToolbar: [
        'link', 'paragraph', 'heading1', 'heading2', 'heading3',
        '|',
        'outdent', 'indent',
        '|',
        'bulletedList', 'numberedList'
    ],
    alignment: {
        options: [
            'left',
            'right',
            'center'
        ]
    },
    image: {
        toolbar: [
            'imageStyle:full',
            'imageStyle:side',
            '|',
            'imageTextAlternative'
        ]
    },
    indentBlock: {
        offset: 1,
        unit: 'em'
    },
    table: {
        contentToolbar: [
            'tableColumn',
            'tableRow',
            '|',
            'mergeTableCells'
        ]
    },
    language: 'uk',
    additionalLanguages: 'all'
};

export default ClassicEditor;
