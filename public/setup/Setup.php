<?php
/**
 *
 */
namespace Setup;

/**
 *
 */
abstract class Setup
{
    /**
     *
     */
    public static function run(array $config)
    {
        if ($config['title']) {
            echo '<h1>', $config['title'], '</h1>';
        }

        if ($config['description']) {
            echo '<h3><em>', $config['description'], '</em></h3>';
        }

        $i = 0;
        $isError = false;

        foreach ($config['tasks'] as $class => $params) {
            $class = 'Setup\\' . $class;
            $class = new $class;

            printf('<h2>%d. %s</h2>', ++$i, $class->title);

            $class->process($params);

            $isError = $isError || $class->isError();

            echo '<ul>';
            foreach ($class->getMessages() as $msg) {
                if (strpos($msg, '@') === 0) {
                    echo '<li style="list-style-type:none"><h3>', substr($msg, 1), '</h3></li>';
                } else {
                    echo '<li>', $msg, '</li>';
                }
            }
            echo '</ul>';
        }

        return !$isError;
    }
}
