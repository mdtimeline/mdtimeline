<?php
/* 
 * @package     PHP5 Wiki Parser
 * @author      Dan Goldsmith
 * @copyright   Dan Goldsmith 2012
 * @link        http://d2g.org.uk/
 * @version     {SUBVERSION_BUILD_NUMBER}
 * 
 * @licence     MPL 2.0
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. 
 */
require_once(dirname(__FILE__) . '/../interface/startOfLine.interface.php');
require_once(dirname(__FILE__) . '/../interface/endOfLine.interface.php');

class pmwiki_linebreaks implements startOfLine, endOfLine
{
    const regular_expression = '/^[^\!](.*?)(\\{1,3}|)$/i';
    
    private $paragraph_open = false;
    
    public function __construct()
    {
        
    }
    
    public function startOfLine($line) 
    {
	    if($line[0] == '<') return $line;

	    if(preg_match(pmwiki_linebreaks::regular_expression, trim(strip_tags($line)),$matches) && trim($line) != "" && !$this->paragraph_open)
        {
            $this->paragraph_open = true;
            return "<p>" . ltrim($line);
        }
        
        return $line;
    }

    public function endOfLine($line)
    {
	    if(substr($line, -1) == '>') return $line;

        if(preg_match(pmwiki_linebreaks::regular_expression, $line,$matches) && $this->paragraph_open)
        {
            $this->paragraph_open = false;
            return rtrim($line) . "</p>";
        }
        
        return $line;
    }
    
}

?>