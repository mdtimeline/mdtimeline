<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 8/4/13
 * Time: 4:06 PM
 * To change this template use File | Settings | File Templates.
 */
include_once (dirname(__FILE__).'/Segments.php');

class TXA extends Segments{


    function __destruct(){
        parent::__destruct();
    }

    function __construct($hl7){
        parent::__construct($hl7, 'NTE');

        $this->setField(1, 'SI', 4, true);
        $this->setField(2, 'IS', 30, true);
        $this->setField(3, 'ID', 2, false, true);
        $this->setField(4, 'TS', 26);
        $this->setField(5, 'XCN', 250, false, true);
        $this->setField(6, 'TS', 26);
        $this->setField(7, 'TS', 26);
        $this->setField(8, 'TS', 26, false, true);
        $this->setField(9, 'XCN', 250, false, true);
        $this->setField(10, 'XCN', 250, false, true);
        $this->setField(11, 'XCN', 250, false, true);
        $this->setField(12, 'EI', 30, true);
        $this->setField(13, 'EI', 30);
        $this->setField(14, 'EI', 22, false, true);
        $this->setField(15, 'EI', 22);
        $this->setField(16, 'ST', 30);
        $this->setField(17, 'ID', 2, true);
        $this->setField(18, 'ID', 2);
        $this->setField(19, 'ID', 2);
        $this->setField(20, 'ID', 2);
        $this->setField(21, 'ST', 30);
        $this->setField(22, 'PPN', 30, false, true);
        $this->setField(23, 'XCN', 30, false, true);

    }


}