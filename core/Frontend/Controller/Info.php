<?php
/**
 * PVLng - PhotoVoltaic Logger new generation
 *
 * @link       https://github.com/KKoPV/PVLng
 * @link       https://pvlng.com/
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use Core\Messages;
use ORM\Config as ORMConfig;
use ORM\ChannelView as ORMChannelView;
use I18N;

/**
 *
 */
class Info extends Controller
{
    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     *
     */
    public function indexPostAction()
    {
        if ($this->request->post('regenerate')) {
            (new ORMConfig)->resetAPIkey();
            // Delete key from cache!
            $this->cache->delete('APIkey');
            Messages::success(I18N::translate('APIkeyRegenerated'));
        }
        $this->app->redirect('info');
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle   = I18N::translate('Information');
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

        $channels = new ORMChannelView;
        $this->view->Stats = $channels->filterByChilds(0)->filterByWrite(1)->find()->asAssoc();

        $this->view->TableSize = $this->db->queryRowsArray('
            SELECT `TABLE_NAME`
                 , `TABLE_COMMENT`
                 , `TABLE_ROWS`
                 , ROUND((`DATA_LENGTH` + `INDEX_LENGTH`)/1024/1024, 2) AS `size_mb`
              FROM `information_schema`.`TABLES`
             WHERE `TABLE_TYPE` = "BASE TABLE"
               AND `TABLE_SCHEMA` = "'.$dbName.'" AND `TABLE_NAME` LIKE "pvlng_%"
        ');

        $cacheInfo = [];
        foreach ($this->app->cache->info() as $key => $value) {
            $cacheInfo[] = ['key' => $key, 'value' => $value];
        }

        $this->view->CacheInfo   = $cacheInfo;
        $this->view->CacheHits   = $this->app->cache->getHits();
        $this->view->CacheMisses = $this->app->cache->getMisses();
    }
}
