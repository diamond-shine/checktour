// basic-styles
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
import Strikethrough from '@ckeditor/ckeditor5-basic-styles/src/strikethrough';
import Code from '@ckeditor/ckeditor5-basic-styles/src/code';
import Subscript from '@ckeditor/ckeditor5-basic-styles/src/subscript';
import Superscript from '@ckeditor/ckeditor5-basic-styles/src/superscript';
// alignment
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
// autoformat
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
// upload
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Base64UploadAdapter from '@ckeditor/ckeditor5-upload/src/adapters/base64uploadadapter';
import SimpleUploadAdapter from '@ckeditor/ckeditor5-upload/src/adapters/simpleuploadadapter';
// block-quote
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
// ui
import BlockToolbar from '@ckeditor/ckeditor5-ui/src/toolbar/block/blocktoolbar';
// easy-image
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
// essentials
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
// font
import Font from '@ckeditor/ckeditor5-font/src/font';
// heading
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import HeadingButtonsUI from '@ckeditor/ckeditor5-heading/src/headingbuttonsui';
// highlight
import Highlight from '@ckeditor/ckeditor5-highlight/src/highlight';
// horizontalline
import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline';
// image
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
// indent
import Indent from '@ckeditor/ckeditor5-indent/src/indent';
// link
import Link from '@ckeditor/ckeditor5-link/src/link';
// list
import List from '@ckeditor/ckeditor5-list/src/list';
// media-embed
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
// mention
import Mention from '@ckeditor/ckeditor5-mention/src/mention';
// paragraph
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import ParagraphButtonUI from '@ckeditor/ckeditor5-paragraph/src/paragraphbuttonui';
// paste-from-office
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
// remove-format
import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat';
// table
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
// typing
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation';
// list
import TodoList from '@ckeditor/ckeditor5-list/src/todolist';
// watchdog
import WatchDog from '@ckeditor/ckeditor5-watchdog/src/watchdog';
// word-count
import WordCount from '@ckeditor/ckeditor5-word-count/src/wordcount';

export default [
    Alignment,
    Autoformat,
    Base64UploadAdapter,
    BlockQuote,
    BlockToolbar,
    Bold,
    Code,
    EasyImage,
    Essentials,
    Font,
    Heading,
    HeadingButtonsUI,
    Highlight,
    HorizontalLine,
    Image,
    ImageCaption,
    ImageResize,
    ImageStyle,
    ImageToolbar,
    Indent,
    Italic,
    Link,
    List,
    MediaEmbed,
    Mention,
    Paragraph,
    ParagraphButtonUI,
    PasteFromOffice,
    RemoveFormat,
    SimpleUploadAdapter,
    Strikethrough,
    Subscript,
    Superscript,
    Table,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
    UploadAdapter,
    WordCount
];
