<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 6/18/18
 * Time: 9:25 PM
 */

mb_internal_encoding('UTF-8');

// The connection must be closed after each response. Allowing the client to correctly estimate the network latency.
header('Connection: close');
header('Cache-Control: no-cache, no-store, no-transform');
header('Pragma: no-cache'); // Support for HTTP 1.0