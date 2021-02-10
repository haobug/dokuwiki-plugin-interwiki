<?php
/**
 * DokuWiki Plugin interwiki (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  haobug <qingxianhao@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die('interwiki plugin die');

if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');

define('INTERWIKI_ICONS',DOKU_INC.'lib/images/interwiki/');
define('INTERWIKI_PREFIX','../interwiki/');

require_once (DOKU_PLUGIN . 'action.php');

class action_plugin_interwiki extends DokuWiki_Action_Plugin {
    var $extensions = array('.png','.gif');
    /**
     * Register the eventhandlers
     */
    function register(Doku_Event_Handler $contr) {
        $contr->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array ());
    }

    function getInterwikiButtons() {
        $wikis = getInterwiki();
        if(count($wikis) < 1)
            return array();

        $btns = array();
        $excludes = array_unique(
            array_map('trim',
                explode(',',
                    strtolower(
                         $this->getConf('excluded_shortcuts')
                    )
                )
            )
        );
        sort($excludes);

        foreach($wikis as $key => $url) {
            if(in_array($key, $excludes))
                continue;

            $icon ='';
            foreach ($this->extensions as $ext){
                if(file_exists(INTERWIKI_ICONS.$key.$ext)){
                    $icon = INTERWIKI_PREFIX.$key.$ext;
                    break;
                }
            }
            if(''==$icon)
                continue;

            $btn_tmp = array(
                'type'   => 'format',
                'title'  => $key,
                'icon'   => $icon,
                'open'   => '[['.$key.'>',
                'close'  => ']]',
            );
            array_push($btns, $btn_tmp);
        }
        return $btns;
    }
    /**
     * Inserts the toolbar button
     */
    function insert_button(&$event, $param) {
        $event->data[] = array(
            'type'   => 'picker',
            'title'  => $this->getLang('qb_interwiki'),
            'icon'   => '../../plugins/interwiki/ico.gif',
            'list'   => $this->getInterwikiButtons(),
        );

    }
}
