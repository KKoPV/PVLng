<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use PVLng\PVLng;
use I18N;
use Markdown;
use Yryie;

/**
 *
 */
class Description extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('Description');

        $fileMD = PVLng::path(PVLng::$RootDir, 'description.md');

        if (!file_exists($fileMD)) {
            $fileMD .= '.dist';
        }

        $fileTOC  = PVLng::path(PVLng::$TempDir, 'Frontend.Description.TOC.html');
        $fileHTML = PVLng::path(PVLng::$TempDir, 'Frontend.Description.html');

        // Is there an actual content file?
        if (!file_exists($fileHTML) || filemtime($fileMD) > filemtime($fileHTML)) {
            /// Yryie::Info('Build description from '.$fileMD);

            // Put a "back to top" icon behind each header
            $top = '<a href="#top" class="fa fa-sort-asc btn" style="margin-left:12px" title="Go to top"></a>';

            $TOC = '';
            $content = file_get_contents($fileMD);

            if (preg_match_all('~^(#+ +)(.*?)#*\s*$~m', $content, $headers, PREG_SET_ORDER)) {
                foreach ($headers as $header) {
                    $hash = urlencode($header[2]);
                    $TOC .= '<a href="#'.$hash.'">' . $header[2] . '</a>';
                    $anchor = '<a name="'.$hash.'"></a>';
                    // Move all headers 2 levels deeper
                    $content = str_replace($header[0], '##'.$header[1].$anchor.$header[2].$top.PHP_EOL, $content);
                }
            }

            file_put_contents($fileTOC, $TOC);
            // Transform MarkDown
            file_put_contents($fileHTML, (new Markdown)->transform($content));
        }

        $this->view->TOC     = file_get_contents($fileTOC);
        $this->view->Content = file_get_contents($fileHTML);
    }
}
