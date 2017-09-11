<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package             Joomla
 * @subpackage          CoalaWeb Gears
 * @author              Steven Palmer
 * @author url          http://coalaweb.com
 * @author email        support@coalaweb.com
 * @license             GNU/GPL, see /assets/en-GB.license.txt
 * @copyright           Copyright (c) 2015 Steven Palmer All rights reserved.
 *
 * CoalaWeb Gears is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class PlgSystemCwgearsInstallerScript { 

     /** @var string The extension's name */
    protected $_coalaweb_extension = 'cwgears';
    
    /** @var array Plugin that should activated automatically */
    private $cwActivatePlugins = array(
        'plugins' => array(
            'system' => array(
                'cwgears' => 1,
            )
        )
    );
    
    /**
     * Joomla! pre-flight event
     * 
     * @param string $type Installation type (install, update, discover_install)
     * @param JInstaller $parent Parent object
     */
    public function preflight($type, $parent) {

        // Only allow to install on Joomla! 3.2 or later with PHP 5.4 or later
        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } elseif (function_exists('phpversion')) {
            $version = phpversion();
        } else {
            $version = '5.0.0'; // all bets are off!
        }

        if (!version_compare(JVERSION, '3.2', 'ge')) {
            $msg = "<p>Sorry, you need Joomla! 3.2 or later to install this extension!</p>";

            JError::raiseWarning(100, $msg);

            return false;
        }

        if (!version_compare($version, '5.4', 'ge')) {
            $msg = "<p>Sorry, you need PHP 5.4 or later to install this extension!</p>";

            JError::raiseWarning(100, $msg);

            return false;
        }
        
        // Workarounds for JInstaller bugs
        if ($type != 'discover_install') {
            $this->_fixBrokenSQLUpdates($parent);
        }

        return true;
    }

      /**
     * Runs after install, update or discover_update
     * @param string $type install, update or discover_update
     * @param JInstaller $parent 
     */
    function postflight($type, $parent) {
        
        //Activate main plugin only on install
        if ($type == 'install') {       
            $this->_activatePlugin($parent);
        }

    }

    /**
     * Fixed failed install/update of database
     * 
     */
    private function _fixBrokenSQLUpdates($parent) {
        // Get the extension ID
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $query
                ->select($db->qn('extension_id'))
                ->from($db->qn('#__extensions'))
                ->where($db->qn('element') . ' = ' . $db->q($this->_coalaweb_extension));

        $db->setQuery($query);
        $eid = $db->loadResult();

        if (!$eid) {
            return;
        }

        // Get the schema version
        $query = $db->getQuery(true);

        $query
                ->select($db->qn('version_id'))
                ->from($db->qn('#__schemas'))
                ->where($db->qn('extension_id') . ' = ' . $db->q($eid));

        $db->setQuery($query);
        $version = $db->loadResult();

        // If there is a schema version it's not a false update
        if ($version) {
            return;
        }

        // Execute the installation SQL file.
        $dbDriver = strtolower($db->name);

        if ($dbDriver == 'mysqli') {
            $dbDriver = 'mysql';
        }


        // Get the name of the sql file to process
        $sqlfile = $parent->getParent()->getPath('source') . '/sql/install/' . $dbDriver . '/install.mysql.utf8.sql';

        if (file_exists($sqlfile)) {
            $buffer = file_get_contents($sqlfile);
            if ($buffer === false) {
                return;
            }

            $queries = JInstallerHelper::splitSql($buffer);

            if (count($queries) == 0) {
                // No queries to process
                return;
            }

            // Process each query in the $queries array (split out of sql file).
            foreach ($queries as $query) {
                $query = trim($query);

                if ($query != '' && $query{0} != '#') {
                    $db->setQuery($query);

                    if (!$db->execute()) {
                        JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

                        return false;
                    }
                }
            }
        }

        // Update #__schemas to the latest version.
        $path = $parent->getParent()->getPath('source') . '/sql/updates/' . $dbDriver;
        $files = str_replace('.sql', '', JFolder::files($path, '\.sql$'));

        if (count($files) > 0) {
            usort($files, 'version_compare');
            $version = array_pop($files);
        } else {
            $version = '0.0.1';
        }

        $query = $db->getQuery(true);

        $query->insert($db->qn('#__schemas'));
        $query->columns(array($db->qn('extension_id'), $db->qn('version_id')));
        $query->values($eid . ', ' . $db->q($version));

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Activate if main extension is a plugin on install
     *
     * @param JInstaller $parent
     */
    private function _activatePlugin($parent) {
        $db = JFactory::getDbo();

        if (count($this->cwActivatePlugins['plugins'])) {
            foreach ($this->cwActivatePlugins['plugins'] as $folder => $plugins) {
                if (count($plugins)) {
                    foreach ($plugins as $plugin => $published) {

                        if ($published) {
                            $query = $db->getQuery(true)
                                    ->update($db->qn('#__extensions'))
                                    ->set($db->qn('enabled') . ' = ' . $db->q('1'))
                                    ->where($db->qn('element') . ' = ' . $db->q($plugin))
                                    ->where($db->qn('folder') . ' = ' . $db->q($folder));
                            $db->setQuery($query);

                            try {
                                $db->execute();
                            } catch (Exception $exc) {
                                // Nothing
                            }
                        }
                    }
                }
            }
        }
    }
    
}
