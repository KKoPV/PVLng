<?php
/**
 * AOP
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 * Register middleware to handle localized numbers
 */
class LocaleMiddleware extends Slim\Middleware {

    /**
     *
     */
    public function call() {
        $app = $this->getApplication();

        // Init locale settings
        foreach (BabelKitMySQLi::getInstance()->full_set('locale', $app->Language) as $row) {
            $app->config->set('Locale.'.$row[0], $row[1]);
        }

        // Shortcuts for $this->fromLocale()
        $this->TSep = $app->config->get('Locale.ThousandSeparator');
        $this->DSep = $app->config->get('Locale.DecimalPoint');

        // Transform posted data from local format
        if ($app->request->isPost()) {
            // Force creation of environment['slim.request.form_hash']
            $app->request->post();

            if ($post = $app->environment['slim.request.form_hash']) {
                foreach ($post as &$value) $value = $this->fromLocale($value);
                $app->environment['slim.request.form_hash'] = $post;
            }
        }

        // Transform data for view into local format
        $app->view->setRenderValueCallback(function($value) use ($app) {
            return is_numeric($value)
                 ? number_format(
                       $value,
                       strlen(substr(strrchr($value, '.'), 1)),
                       $app->config->get('Locale.DecimalPoint'),
                       $app->config->get('Locale.ThousandSeparator')
                   )
                 : $value;
        });

        // Run inner middleware and application
        $this->next->call();
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $TSep;

    /**
     *
     */
    protected $DSep;

    /**
     * Recursive helper function
     */
    protected function fromLocale( $value ) {
        if (is_array($value)) {
            foreach ($value as $k=>$v) $value[$k] = $this->fromLocale($v);
        } elseif (preg_match('~^[ 0-9.,-]+$~', $value))  {
            // Remove thousand separators
            $value = str_replace($this->TSep, '', $value);
            // Replace decimal point
            $value = str_replace($this->DSep, '.', $value);
        }
        return $value;
    }

}

$app->add(new LocaleMiddleware());
