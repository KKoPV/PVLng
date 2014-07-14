<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Controller;

/**
 *
 */
class Tariff extends \Controller {

    /**
     *
     */
    public function Index_Action() {

        $this->view->SubTitle = __('Tariffs');

        $tariff = array();
        $tblTariffView = new \ORM\TariffView;

        foreach ($tblTariffView->find() as $row) {
            $name = $row->getName();
            if (!isset($tariff[$name])) {
                $tariff[$name] = array(
                    'id'      => $row->getId(),
                    'name'    => $name,
                    'comment' => $this->linkify($row->getTariffComment()),
                    'date'    => array()
                );
            }
            if ($date = $row->getDate()) {
                $tariff[$name]['date'][$date] = $date;
            }
        }

        ksort($tariff, SORT_LOCALE_STRING);
        $this->view->Tariff = $tariff;
    }

    /**
     *
     */
    public function Show_Action() {

        $this->view->SubTitle = __('Tariff');

        $id = $this->app->params->get('id');

        $tblTariffView = new \ORM\TariffView;
        $tblTariffView->filterById($id)->order('date')->find();
        $tariff = array();
        $fmtDate = $this->config->get('Locale.Date');
        foreach ($tblTariffView as $key=>$row) {
          if ($key === 0) {
                $this->view->Name    = $row->getName();
                $this->view->Comment = $this->linkify($row->getTariffComment());
            }
            // If no date defined yet...
            if (!$row->getDate()) continue;

            $tariff[] = array(
                'id'      => $row->getId(),
                'date'    => date($fmtDate, $row->getDateTS()),
                'time'    => $row->getTime(),
                'days'    => implode(', ', array_map(
                                 function($a) { return __('day2::'.($a==7?0:$a)); },
                                 explode(',', $row->getDays())
                             )),
                'cost'    => $row->getCost(),
                'tariff'  => $row->getTariff(),
                'comment' => $row->getTimeComment(),
            );
        }

        $this->view->Tariff = $tariff;
        $tblTariff = new \ORM\Tariff($id);
        $tariff = array();
        $ts = time() - (date('N')-1) * 86400;
        for ($i=1; $i<=7; $i++) {
            $tariff[$i]['day'] = date($fmtDate.' - ', $ts) . __('day::'.($i==7?0:$i));
            foreach ($tblTariff->getTariffDay($ts, \ORM\Tariff::STRING) as $row) {
                $tariff[$i]['data'][] = array(
                    'start'  => $row['start'],
                    'end'    => $row['end'],
                    'tariff' => $row['cost']
                );
            }
            $ts += 86400;
        }

        $this->view->TariffWeek = $tariff;
    }

    /**
     *
     */
    public function AddPOST_Action() {
        $tblTariff = new \ORM\Tariff;
        $tblTariff->setName($this->app->request->post('name'))
                  ->setComment($this->app->request->post('comment'))
                  ->insert();

        if ($tblTariff->isError()) {
            \Messages::Error($tblTariff->Error());
            $this->app->redirect('/tariff');
        }

        \Messages::Success(__('TariffCreated'));

        if ($this->app->request->post('clone')) {
            $tblTariff->cloneDatesTimes($this->app->request->post('id'));
            \Messages::Success(__('TariffDatesCopied'));
            $this->app->redirect('/tariff');
        }

        // Go to edit date for new created tariff
        $this->app->redirect('/tariff/date/add/'.$tblTariff->id);
    }

    /**
     *
     */
    public function Add_Action() {

        $this->view->SubTitle = __('CreateTariff');

        if ($id = $this->app->params->get('id')) {
            $this->view->Clone = TRUE;

            $tblTariff = new \ORM\Tariff($id);

            if ($tblTariff->getId()) {
                $this->view->Id      = $id;
                $this->view->Name    = __('CopyOf') . ' ' . $tblTariff->getName();
                $this->view->Comment = $tblTariff->getComment();
                // Remove given clone Id
                $this->app->params->set('id', NULL);
            } else {
                \Messages::Error('Unknown clone Id: '.$id);
                $this->app->redirect('/tariff');
            }
        }

        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function EditPOST_Action() {

        $tblTariff = new \ORM\Tariff($this->app->request->post('id'));
        if ($tblTariff->getId()) {
            $tblTariff->setName($this->app->request->post('name'))
                      ->setComment($this->app->request->post('comment'))
                      ->update();
        }

        $this->app->redirect('/tariff');
    }

    /**
     *
     */
    public function Edit_Action() {

        $this->view->SubTitle = __('EditTariff');

        if ($id = $this->app->params->get('id')) {
            $tblTariff = new \ORM\Tariff($id);
            if ($tblTariff->getId()) {
                $this->view->Edit    = TRUE;
                $this->view->Id      = $tblTariff->getId();
                $this->view->Name    = $tblTariff->getName();
                $this->view->Comment = $tblTariff->getComment();
            }
        }
    }

    /**
     *
     */
    public function DeletePost_Action() {
        $tblTariff = new \ORM\Tariff($this->app->request->post('id'));
        if ($tblTariff->getId()) $tblTariff->delete();
        $this->app->redirect('/tariff');
    }

    /**
     *
     */
    public function AddDate_Action() {
        $this->view->Clone = !!$this->app->params->get('date');
        $this->app->foreward('EditDate');
    }

    /**
     *
     */
    public function EditDatePOST_Action() {
        $id      = $this->app->request->post('id');
        $dateold = $this->app->request->post('dateold');
        $date    = $this->app->request->post('date');
        $cost    = $this->app->request->post('cost');
        $data    = $this->app->request->post('d');

        // Find valid rows and transform weekdays into database SET
        foreach ($data as $key=>$d) {
            if (empty($d['t']) OR empty($d['p']) OR empty($d['w'])) {
                unset($data[$key]);
            } else {
                $data[$key]['w'] = implode(',', $data[$key]['w']);
            }
        }

        if ($id AND $date AND !empty($data)) {
            $tblDate = new \ORM\TariffDate(array($id, $dateold));
            // Deletes also the time records via foreign key
            if ($tblDate->getId()) $tblDate->delete();

            $tblDate->setId($id)->setDate($date)->setCost($cost)->insert();

            $tblTime = new \ORM\TariffTime;
            $tblTime->setId($id)->setDate($date);
            foreach ($data as $row) {
                $tblTime->setTime($row['t'])->setDays($row['w'])
                        ->setTariff($row['p'])->setComment($row['c'])
                        ->insert();
            }
        }

        if (!$this->app->request->get('returnto')) $this->app->redirect('/tariff');
    }

    /**
     *
     */
    public function EditDate_Action() {

        $this->view->SubTitle = __('EditTariffDate');

        $id   = $this->app->params->get('id');
        $date = $this->app->params->get('date');

        $tblTariff = new \ORM\Tariff($id);
        $this->view->Id      = $tblTariff->getId();
        $this->view->Name    = $tblTariff->getName();
        $this->view->Comment = $this->linkify($tblTariff->getComment());
        $tblDate = new \ORM\TariffDate(array($id, $date));
        $this->view->Cost = $tblDate->getCost();

        // Set given date only for real edit
        $this->view->Date = ($this->view->Clone OR !$date)  ? date('Y-m-d') : $date;

        $tblTime = new \ORM\TariffTime;
        $data = $tblTime->filterById($id)->filterByDate($date)->find()->asAssoc();
        foreach ($data as &$row) {
            foreach (explode(',', $row['days']) as $day) $row['d'.$day] = TRUE;
        }

        // Extend time table to 10? rows
        $add = $this->config->get('Controller.Tariff.TimesLineCount') - count($data);
        while ($add-- > 0) $data[] = array();

        $this->view->Data = $data;
    }

    /**
     *
     */
    public function DeleteDatePOST_Action() {
        $tblDate = new \ORM\TariffDate(array(
            $this->app->request->post('id'),
            $this->app->request->post('date')
        ));
        if ($tblDate->getId()) $tblDate->delete();
        $this->app->redirect('/tariff');
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function linkify( $text ) {
        return preg_replace('~\w+://[^\s]+~', '<a href="$0" target="_blank">$0</a>', $text);
    }

}
