<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
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
            $this->db->query(
                'UPDATE `pvlng_config` SET `value` = UUID() WHERE `key` = "APIKey"'
            );
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
