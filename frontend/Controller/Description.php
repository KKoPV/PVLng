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

        $this->view->SubTitle = \I18N::_('Description');

        $fileMD = ROOT_DIR . DS . 'description.md';
        if (!file_exists($fileMD)) $fileMD .= '.dist';

        $fileHTML = TEMP_DIR . DS . 'Frontend.Description.html';
        $fileTOC  = TEMP_DIR . DS . 'Frontend.Description.TOC.html';

        // Is there an actual content file?
        if (!file_exists($fileHTML) OR filemtime($fileMD) > filemtime($fileHTML)) {

            $content = file_get_contents($fileMD);

            // Move all headers 2 levels deeper
            $content = preg_replace('~^#+~m', '##$0', $content);

            // Prepare a simple TOC
            $links = $toc = array();

            if (preg_match_all('~^#+ +(.*?) *#*$~m', $content, $headers, PREG_SET_ORDER)) {
                foreach ($headers as $header) {
                    $hash = substr(md5($header[0]), -7) . '-' . \Slug::encode($header[0]);
                    $links[] = '<a href="#'.$hash.'">' . $header[1] . '</a>';
                    $toc[$hash] = '<a name="' . $hash . '"></a>';
                    // Prepend the hash before the header
                    $content = str_replace($header[0], $hash."\n\n".$header[0], $content);
                }
            }

            // Transform MarkDown
            $md = new \Markdown;
            $content = $md->transform($content);

            // Prepend TOC
            $TOC = '<p class="toc">' . implode(' | ', $links) . '</p>';

            // Replace inserted hashes aginst the named link tags
            $content = str_replace(array_keys($toc), array_values($toc), $content);

            // Buffer
            file_put_contents($fileTOC,  $TOC);
            file_put_contents($fileHTML, $content);
        } else {
            $TOC     = file_get_contents($fileTOC);
            $content = file_get_contents($fileHTML);
        }

        $this->view->TOC     = $TOC;
        $this->view->Content = $content;
    }

}
