<?php
if (version_compare(PHP_VERSION, '5.6') < 0) {
    throw new \Exception('scssphp requires PHP 5.6 or above');
}

if (! class_exists('ScssPhp\ScssPhp\Version', false)) {
    include_once DIR_DOMPDF . '/src/Base/Range.php';
    include_once DIR_DOMPDF . '/src/Block.php';
    include_once DIR_DOMPDF . '/src/Cache.php';
    include_once DIR_DOMPDF . '/src/Colors.php';
    include_once DIR_DOMPDF . '/src/Compiler.php';
    include_once DIR_DOMPDF . '/src/Compiler/Environment.php';
    include_once DIR_DOMPDF . '/src/Exception/CompilerException.php';
    include_once DIR_DOMPDF . '/src/Exception/ParserException.php';
    include_once DIR_DOMPDF . '/src/Exception/RangeException.php';
    include_once DIR_DOMPDF . '/src/Exception/ServerException.php';
    include_once DIR_DOMPDF . '/src/Formatter.php';
    include_once DIR_DOMPDF . '/src/Formatter/Compact.php';
    include_once DIR_DOMPDF . '/src/Formatter/Compressed.php';
    include_once DIR_DOMPDF . '/src/Formatter/Crunched.php';
    include_once DIR_DOMPDF . '/src/Formatter/Debug.php';
    include_once DIR_DOMPDF . '/src/Formatter/Expanded.php';
    include_once DIR_DOMPDF . '/src/Formatter/Nested.php';
    include_once DIR_DOMPDF . '/src/Formatter/OutputBlock.php';
    include_once DIR_DOMPDF . '/src/Node.php';
    include_once DIR_DOMPDF . '/src/Node/Number.php';
    include_once DIR_DOMPDF . '/src/Parser.php';
    include_once DIR_DOMPDF . '/src/SourceMap/Base64.php';
    include_once DIR_DOMPDF . '/src/SourceMap/Base64VLQ.php';
    include_once DIR_DOMPDF . '/src/SourceMap/SourceMapGenerator.php';
    include_once DIR_DOMPDF . '/src/Type.php';
    include_once DIR_DOMPDF . '/src/Util.php';
    include_once DIR_DOMPDF . '/src/Version.php';
}
