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

        // Buffer DB name
        $dbName = $this->config->get('Database.Database');

        list($this->view->DatabaseSize, $this->view->DatabaseFree) =
        $this->db->queryRowArray('
            SELECT SUM(`DATA_LENGTH` + `INDEX_LENGTH`)/1024/1024 AS `0`
                 , SUM(`DATA_FREE`)/1024/1024                    AS `1`
              FROM `information_schema`.`TABLES`
             WHERE `TABLE_SCHEMA` = "'.$dbName.'" AND `TABLE_NAME` LIKE "pvlng_%"
        ');

        $channels = new \ORM\ChannelView;
        $this->view->Stats = $channels->filterByChilds(0)->filterByWrite(1)->find()->asAssoc();

#        $this->view->Stats = (new \ORM\ReadingStatistics)->find()->asAssoc();

        $this->view->TableSize = $this->db->queryRowsArray('
            SELECT `TABLE_NAME`
                 , `TABLE_COMMENT`
                 , `TABLE_ROWS`
                 , ROUND((`DATA_LENGTH` + `INDEX_LENGTH`)/1024/1024, 2) AS `size_mb`
              FROM `information_schema`.`TABLES`
             WHERE `TABLE_TYPE` = "BASE TABLE"
               AND `TABLE_SCHEMA` = "'.$dbName.'" AND `TABLE_NAME` LIKE "pvlng_%"
        ');

        $this->view->CacheInfo   = $this->app->cache->info();
        $this->view->CacheHits   = $this->app->cache->getHits();
        $this->view->CacheMisses = $this->app->cache->getMisses();
    }

}
