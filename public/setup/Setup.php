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
        $i  = 1;
        $ok = true;

        foreach ($config as $class => $params) {
            $class = 'Setup\\' . $class;
            $class = new $class;

            printf('<h3>%d. %s</h3>', $i++, $class->title);

            $class->process($params);

            echo '<ul>';
            foreach ($class->getMessages() as $msg) {
                echo '<li>', $msg, '</li>';
            }
            echo '</ul>';

            if ($class->isError()) {
                $ok = false;
            }
        }

        return $ok;
    }
}
