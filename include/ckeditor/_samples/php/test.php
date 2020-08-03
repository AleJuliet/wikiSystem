<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Setting Configuration Options &mdash; CKEditor Sample</title>
	<meta content="text/html; charset=utf-8" http-equiv="content-type"/>
</head>
<body>

	<form action="../sample_posteddata.php" method="post">
			<label>Editor 1:</label>
<?php
// Include the CKEditor class.
include("../../ckeditor.php");

// Create a class instance.
$CKEditor = new CKEditor();

// Do not print the code directly to the browser, return it instead.
//$CKEditor->returnOutput = true;

// Path to the CKEditor directory, ideally use an absolute path instead of a relative dir.
//   $CKEditor->basePath = '/ckeditor/'
// If not set, CKEditor will try to detect the correct path.
$CKEditor->basePath = '../../';

// Set global configuration (will be used by all instances of CKEditor).
//$CKEditor->config['width'] = 600;
// Change default textarea attributes.
$CKEditor->textareaAttributes = array("cols" => 80, "rows" => 10);

$config['toolbarCanCollapse'] = false;
$config['language']='en';
$config['toolbar'] = array(
	array( 'Source', '-','Undo','Redo','-','Bold', 'Italic', 'Underline', 'Strike' ),
 array('JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Outdent','Indent','-','NumberedList','BulletedList','Blockquote'),
 array('Subscript','Superscript','Table','HorizontalRule','SpecialChar'),
 array('Link', 'Unlink')
);

// The initial value to be displayed in the editor.
$initialValue = '';

// Create the first instance.

$code = $CKEditor->editor("editor1", $initialValue, $config);

//echo $code;
?>
	</form>
</body>
</html>
