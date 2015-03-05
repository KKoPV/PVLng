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
class Description extends \Controller {

    /**
     *
     */
    public function Index_Action() {

        $this->view->SubTitle = __('Description');

        $fileMD = ROOT_DIR . DS . 'description.md';
        if (!file_exists($fileMD)) $fileMD .= '.dist';

        $fileTOC  = TEMP_DIR . DS . 'Frontend.Description.TOC.html';
        $fileHTML = TEMP_DIR . DS . 'Frontend.Description.html';

        // Is there an actual content file?
        if (!file_exists($fileHTML) OR filemtime($fileMD) > filemtime($fileHTML)) {
            /// \Yryie::Info('Build description from '.$fileMD);

            // Put a "back to top" icon behind each header
            $top = '<a href="#top" style="margin-left:12px"><img src="/images/ico/arrow-stop-090.png" style="width:12px"></a>';

            $TOC = '';
            $content = file_get_contents($fileMD);

            if (preg_match_all('~^(#+ +)(.*?)( *#*)$~m', $content, $headers, PREG_SET_ORDER)) {
                foreach ($headers as $header) {
                    $hash = urlencode($header[2]);
                    $TOC .= '<a href="#'.$hash.'">' . $header[2] . '</a>';
                    $anchor = '<a name="'.$hash.'"></a>';
                    // Move all headers 2 levels deeper
                    $content = str_replace($header[0], '##'.$header[1].$anchor.$header[2].$header[3].$top, $content);
                }
            }

            file_put_contents($fileTOC, $TOC);
            // Transform MarkDown
            file_put_contents($fileHTML, (new \Markdown)->transform($content));
        }

        $this->view->TOC     = file_get_contents($fileTOC);
        $this->view->Content = file_get_contents($fileHTML);
    }

}
