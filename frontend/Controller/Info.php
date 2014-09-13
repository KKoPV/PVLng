<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Controller;

/**
 *
 */
class Info extends \Controller {

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     *
     */
    public function IndexPost_Action() {
        if ($this->request->post('regenerate')) {
            (new \ORM\Config)->resetAPIkey();
            // Delete key from cache!
            $this->cache->delete('APIkey');
            \Messages::Success(__('APIkeyRegenerated'));
        }
        $this->app->redirect('info');
    }

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle   = __('Information');
        $this->view->ServerName = $_SERVER['SERVER_NAME'];

        list($this->view->DatabaseSize, $this->view->DatabaseFree) =
        $this->db->queryRowArray('
            SELECT SUM(`data_length`+`index_length`)/1024/1024 AS "0"
                 , SUM(`data_free`)/1024/1024 AS "1"
              FROM `information_schema`.`tables`
             WHERE `table_schema` = "'.$this->config->get('Database.Database').'"
        ');

        $this->view->Stats = (new \ORM\ReadingStatistics)->find()->asAssoc();

        $this->view->CacheInfo   = $this->app->cache->info();
        $this->view->CacheHits   = $this->app->cache->getHits();
        $this->view->CacheMisses = $this->app->cache->getMisses();
    }

}
