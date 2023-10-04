/**
 * @license Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.filebrowserBrowseUrl = '/ckeditor/default/image-browse';
    config.filebrowserUploadUrl = '/ckeditor/default/image-upload';
    config.extraPlugins = 'oembed,widget,lineheight';
    config.line_height="0.5em;0.75em;1em;1.25em;1.5em;1.75em;2em" ;
    config.contentsCss = [CKEDITOR.getUrl('contents.css'), CKEDITOR.getUrl('rules/spacing.css')];
    config.allowedContent = true;
    config.toolbar = [
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Scayt' ] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'insert', items: [ 'Image', 'Iframe', 'oembed', 'Table', 'HorizontalRule', 'SpecialChar' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
		{ name: 'others', items: [ '-' ] },
		'/',
		
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'CreateDiv', 'JustifyLeft','JustifyCenter','JustifyRight', 'JustifyBlock' ] },
		{ name: 'styles', items: [ 'Styles', 'Format','Font','FontSize', 'lineheight'  ] },
		// { name: 'about', items: [ 'About' ] },
	    ['TextColor','BGColor'],
	    ['ShowBlocks'],
	    { name: 'tools', items: [ 'Maximize' ] },
	];

};
