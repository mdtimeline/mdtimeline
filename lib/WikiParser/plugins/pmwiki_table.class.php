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
require_once(dirname(__FILE__) . '/../interface/endOfFile.interface.php');

class pmwiki_table implements startOfLine, endOfFile
{
    const regular_expression_table = '/^\|\|(.*)/i';
    const regular_expression_th = '/^!(.*)/i';
    const delimiter_cel = '||';
    
    private $open_table = false;
    
    public function __construct()
    {
        $this->open_table = false;
    }
    
    public function startOfLine($line)
    {
        //Check if it's an open tag
        if(preg_match(pmwiki_table::regular_expression_table, $line,$matches))
        {

	        $cells = explode(pmwiki_table::delimiter_cel, $matches[1]);
	        if(count($cells) > 0){
		        $line = '';
	        }

        	if(!$this->open_table){
		        $line = '<table cellpadding="5">';
		        $this->open_table = true;
	        }

	        $line .= '<tr>';
	        foreach ($cells as $cell){
		        $line .= self::cellParser($cell);
	        }
	        $line .= '</tr>';

         }else if($this->open_table){
	        $line = '</table>' . PHP_EOL . $line;
	        $this->open_table = false;
        }
        
        return $line;
    }

    public function endOfFile($file_content){
    	if($this->open_table){
		    $file_content .= '</table>';
		    $this->open_table = false;
	    }

    	return $file_content;
    }

    private function cellParser($cell){
    	if($cell === '') return $cell;

	    $attr = '';

    	if(preg_match(pmwiki_table::regular_expression_th, $cell,$matches)){

		    if (preg_match('/^\\s{2,}.*\\s{2,}$/',$matches[1])) { $attr .= ' align="center"'; }
		    elseif (preg_match('/^\\s{2,}/',$matches[1])) { $attr .= ' align="right"'; }
		    else { $attr .= ' align="left"'; }

    		return '<th '. $attr .' style="border-bottom-color: #1a9bfc">' . $matches[1] . '</th>';
	    }else{

		    if (preg_match('/^\\s{2,}.*\\s{2,}$/',$cell)) { $attr .= ' align="center"'; }
		    elseif (preg_match('/^\\s{2,}/',$cell)) { $attr .= ' align="right"'; }
		    else { $attr .= ' align="left"'; }

		    return '<td '. $attr .' >' . $cell . '</td>';
	    }

    }


            
}

?>