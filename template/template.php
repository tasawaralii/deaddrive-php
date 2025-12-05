<?php

function dd_render($title, $content)
{
    require_once __DIR__ . '/head.php';
    echo "<body>";

    require __DIR__ . '/header.php';


    $mainStyle = (!isset($_SERVER['user'])) ? 'padding-left:0px;' : '';
    echo "<main style=\"{$mainStyle}margin-top: 58px\">";

    echo '<div class="container pt-4">';
    // content can be a string (html) or a callable
    if (is_callable($content)) {
        call_user_func($content);
    } else {
        echo $content;
    }

    require_once __DIR__ . '/footer.php';
    echo '</div>';
    echo "</main>";

    echo "\n</body>\n</html>";
}