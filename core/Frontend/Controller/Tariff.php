<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use Core\Messages;
use ORM\Tariff as ORMTariff;
use ORM\TariffDate as ORMTariffDate;
use ORM\TariffTime as ORMTariffTime;
use ORM\TariffView as ORMTariffView;
use I18N;

/**
 *
 */
class Tariff extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('Tariffs');

        $tariff = array();
        $tblTariffView = new ORMTariffView;

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
    public function showAction()
    {
        $this->view->SubTitle = I18N::translate('Tariff');

        $id = $this->app->params->get('id');

        $tblTariffView = new ORMTariffView;
        $tblTariffView->filterById($id)->order('date')->find();
        $tariff = array();
        $fmtDate = $this->config->get('Locale.Date');
        foreach ($tblTariffView as $key => $row) {
            if ($key === 0) {
                $this->view->Name    = $row->getName();
                $this->view->Comment = $this->linkify($row->getTariffComment());
            }
            // If no date defined yet...
            if (!$row->getDate()) {
                continue;
            }

            $tariff[] = array(
                'id'      => $row->getId(),
                'date'    => date($fmtDate, $row->getDateTS()),
                'dateraw' => date('Y-m-d', $row->getDateTS()),
                'time'    => $row->getTime(),
                'days'    => implode(', ', array_map(
                                    function ($a) {
                                        return I18N::translate('day2::'.($a==7?0:$a));
                                    },
                                 explode(',', $row->getDays())
                             )),
                'cost'    => $row->getCost(),
                'tariff'  => $row->getTariff(),
                'comment' => $row->getTimeComment(),
            );
        }

        $this->view->Tariff = $tariff;
        $tblTariff = new ORMTariff($id);
        $tariff = array();
        $ts = time() - (date('N')-1) * 86400;
        for ($i=1; $i<=7; $i++) {
            $tariff[$i]['day'] = date($fmtDate.' - ', $ts) . I18N::translate('day::'.($i==7?0:$i));
            foreach ($tblTariff->getTariffDay($ts, ORMTariff::STRING) as $row) {
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
    public function addPostAction()
    {
        $tblTariff = new ORMTariff;
        $tblTariff->setName($this->app->request->post('name'))
                  ->setComment($this->app->request->post('comment'))
                  ->insert();

        if ($tblTariff->isError()) {
            Messages::Error($tblTariff->Error());
            $this->app->redirect('/tariff');
        }

        Messages::Success(I18N::translate('TariffCreated'));

        if ($this->app->request->post('clone')) {
            $tblTariff->cloneDatesTimes($this->app->request->post('id'));
            Messages::Success(I18N::translate('TariffDatesCopied'));
            $this->app->redirect('/tariff');
        }

        // Go to edit date for new created tariff
        $this->app->redirect('/tariff/date/add/'.$tblTariff->id);
    }

    /**
     *
     */
    public function addAction()
    {
        $this->view->SubTitle = I18N::translate('CreateTariff');

        if ($id = $this->app->params->get('id')) {
            $this->view->Clone = true;

            $tblTariff = new ORMTariff($id);

            if ($tblTariff->getId()) {
                $this->view->Id      = $id;
                $this->view->Name    = I18N::translate('CopyOf') . ' ' . $tblTariff->getName();
                $this->view->Comment = $tblTariff->getComment();
                // Remove given clone Id
                $this->app->params->set('id', null);
            } else {
                Messages::Error('Unknown clone Id: '.$id);
                $this->app->redirect('/tariff');
            }
        }

        $this->app->foreward('Edit');
    }

    /**
     *
     */
    public function editPostAction()
    {
        $tblTariff = new ORMTariff($this->app->request->post('id'));
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
    public function editAction()
    {
        $this->view->SubTitle = I18N::translate('EditTariff');

        if ($id = $this->app->params->get('id')) {
            $tblTariff = new ORMTariff($id);
            if ($tblTariff->getId()) {
                $this->view->Edit    = true;
                $this->view->Id      = $tblTariff->getId();
                $this->view->Name    = $tblTariff->getName();
                $this->view->Comment = $tblTariff->getComment();
            }
        }
    }

    /**
     *
     */
    public function deletePostAction()
    {
        $tblTariff = new ORMTariff($this->app->request->post('id'));
        if ($tblTariff->getId()) {
            $tblTariff->delete();
        }
        $this->app->redirect('/tariff');
    }

    /**
     *
     */
    public function addDateAction()
    {
        $this->view->Clone = !!$this->app->params->get('date');
        $this->app->foreward('EditDate');
    }

    /**
     *
     */
    public function editDatePostAction()
    {
        $id      = $this->app->request->post('id');
        $dateold = $this->app->request->post('dateold');
        $date    = $this->app->request->post('date');
        $cost    = $this->app->request->post('cost');
        $data    = $this->app->request->post('d');

        // Find valid rows and transform weekdays into database SET
        foreach ($data as $key => $d) {
            if (empty($d['t']) || empty($d['p']) || empty($d['w'])) {
                unset($data[$key]);
            } else {
                $data[$key]['w'] = implode(',', $data[$key]['w']);
            }
        }

        if ($id && $date && !empty($data)) {
            $tblDate = new ORMTariffDate(array($id, $dateold));
            // Deletes also the time records via foreign key
            if ($tblDate->getId()) {
                $tblDate->delete();
            }

            $tblDate->setId($id)->setDate($date)->setCost($cost)->insert();

            $tblTime = new ORMTariffTime;
            $tblTime->setId($id)->setDate($date);
            foreach ($data as $row) {
                $tblTime->setTime($row['t'])->setDays($row['w'])
                        ->setTariff($row['p'])->setComment($row['c'])
                        ->insert();
            }
        }

        if (!$this->app->request->get('returnto')) {
            $this->app->redirect('/tariff');
        }
    }

    /**
     *
     */
    public function editDateAction()
    {
        $this->view->SubTitle = I18N::translate('EditTariffDate');

        $id   = $this->app->params->get('id');
        $date = $this->app->params->get('date');

        $tblTariff = new ORMTariff($id);
        $this->view->Id      = $tblTariff->getId();
        $this->view->Name    = $tblTariff->getName();
        $this->view->Comment = $this->linkify($tblTariff->getComment());

        $tblDate = new ORMTariffDate(array($id, $date));
        $this->view->Cost = $tblDate->getCost();

        // Set given date only for real edit
        $this->view->Date = ($this->view->Clone || !$date)  ? date('Y-m-d') : $date;

        $tblTime = new ORMTariffTime;
        $data = $tblTime->filterById($id)->filterByDate($date)->find()->asAssoc();
        foreach ($data as &$row) {
            foreach (explode(',', $row['days']) as $day) {
                $row['d'.$day] = true;
            }
        }

        // Extend time table to 10? rows
        $add = $this->config->get('Controller.Tariff.TimesLineCount') - count($data);
        while ($add-- > 0) {
            $data[] = array();
        }

        $this->view->Data = $data;
    }

    /**
     *
     */
    public function deleteDatePostAction()
    {
        $tblDate = new ORMTariffDate(array(
            $this->app->request->post('id'),
            $this->app->request->post('date')
        ));
        if ($tblDate->getId()) {
            $tblDate->delete();
        }
        $this->app->redirect('/tariff');
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function linkify($text)
    {
        return preg_replace('~\w+://[^\s]+~', '<a href="$0" target="_blank">$0</a>', $text);
    }
}
