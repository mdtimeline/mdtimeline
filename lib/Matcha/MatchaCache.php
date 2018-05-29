<?php
/**
 * Matcha::connect
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class MatchaCache extends Matcha
{

	/**
	 * @var string memory or file
	 */
	private static $cache_type = 'file';
	private static $cache = [];

    /**
     * function __createMemoryModel():
     * Method to create the HEAP(memory) table this will hold the Sencha Model
     * metadata. This will speed up the model read times and, also will
     * hold a already parsed version of the sencha model.
     * @return bool
     */
    public static function __createMemoryModel()
    {
        try
        {

        	if(self::$cache_type == 'file') return true;

            // set the heap to 16MB
            $setHeap = 'SET max_heap_table_size = 1024*1024*16;';

            // create the table in memory
            $sql = 'CREATE TABLE IF NOT EXISTS `_sencha_model` (
                        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `model` VARCHAR(150),
                        `modelData` VARCHAR(65535),
                        `modelLastChange` TIMESTAMP NULL
                    ) ENGINE = MEMORY;';
            self::$__conn->query($setHeap);
            self::$__conn->query($sql);
            self::$__conn->query("ALTER TABLE `_sencha_model` ADD INDEX `model` (`model`)");
            return true;
        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__createMemoryModel', []);
        }
    }

    /**
     * function __checkMemoryModel():
     * Method to check if the Sencha Memory Table exists if not create the
     * memory table.
     */
    public static function __checkMemoryModel()
    {
        try
        {
	        if(self::$cache_type == 'file') return true;

	        $sql = "SHOW TABLES LIKE '_sencha_model';";
	        $sth = @self::$__conn->prepare($sql);
	        $sth->execute();
	        $recordSet = $sth->fetch(PDO::FETCH_ASSOC);
            if(!is_array($recordSet)) self::__createMemoryModel();
        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__checkMemoryModel', []);
        }
    }

    /**
     * function __storeSenchaModel($senchaModelName = NULL, $senchaModelArray = array()):
     * Method to store the already parsed Sencha Model into server side memory using
     * MySQL HEAP(Memory) table
     * @param null $senchaModelName
     * @param array $senchaModelArray
     * @param null $instance
     * @return bool
     */
    public static function __storeSenchaModel($senchaModelName = NULL, $senchaModelArray = array(), $instance = null)
    {
        try
        {
	        $senchaMemoryModelName = isset($instance) ? $senchaModelName .'_' . $instance : $senchaModelName;

	        if(self::$cache_type == 'file'){

		        $file = MATCHA_ROOT . 'cache/' . $senchaMemoryModelName . '.php';
		        self::$cache[$senchaMemoryModelName] = [
		        	'time' => MatchaModel::__getFileModifyDate($senchaModelName),
			        'data' => $senchaModelArray
		        ];

		        $model_data = var_export(self::$cache[$senchaMemoryModelName], true);
		        $model_data = str_replace('stdClass::__set_state', '(object)', $model_data);
		        file_put_contents($file, '<?php $model_data = ' . $model_data . ';', LOCK_EX);

		        return true;

	        }else{
		        self::__checkMemoryModel();
		        self::__destroySenchaMemoryModel($senchaMemoryModelName);
		        $sql = "INSERT INTO `_sencha_model` (model, modelData, modelLastChange) VALUES (?,?,?)";
		        $params = [$senchaMemoryModelName, serialize($senchaModelArray), date('Y-m-d H:i:s', MatchaModel::__getFileModifyDate($senchaModelName))];
		        $sth = @self::$__conn->prepare($sql);
		        $sth->execute($params);
		        return true;
	        }
        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__storeSenchaModel', [$senchaModelName, $instance]);
        }
    }

    /**
     * function __getModelFromMemory($senchaModel = NULL):
     * Method to get the sencha model from the server side memory(HEAP)
     * and return a Array.
     * @param null $senchaModelName
     * @param null $instance
     * @return bool|mixed
     */
    public static function __getModelFromMemory($senchaModelName = null, $instance = null)
    {
        try
        {
	        $senchaMemoryModelName = isset($instance) ? $senchaModelName .'_' . $instance : $senchaModelName;

	        if(self::$cache_type == 'file'){

	        	if(!array_key_exists($senchaMemoryModelName, self::$cache)){
			        $file = MATCHA_ROOT . 'cache/' . $senchaMemoryModelName . '.php';
			        if(!file_exists($file)) return false;

			        @include "$file";
			        $model_data = isset($model_data) ? $model_data : false;
			        self::$cache[$senchaMemoryModelName] = $model_data;
		        }

		        return self::$cache[$senchaMemoryModelName]['data'];

	        }else{

		        self::__checkMemoryModel();
		        $sql = "SELECT * FROM _sencha_model WHERE model = ?;";
		        $params = [$senchaMemoryModelName];
		        $sth = @self::$__conn->prepare($sql);
		        $sth->execute($params);
		        $model = $sth->fetch(PDO::FETCH_ASSOC);

		        if(is_array($model)) return unserialize($model['modelData']); else return false;
	        }

        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__getModelFromMemory', [$senchaModelName, $instance]);
        }
    }

    /**
     * function __getModelLastChange($senchaModel = NULL):
     * Method to get the last modified date in Unix TIMESTAMP format
     * @param null $senchaModelName
     * @param null $instance
     * @return bool
     */
    public static function __getSenchaModelLastChange($senchaModelName = null, $instance = null)
    {
        try
        {
	        $senchaMemoryModelName = isset($instance) ? $senchaModelName .'_' . $instance : $senchaModelName;

	        if(self::$cache_type == 'file'){

		        $file = MATCHA_ROOT . 'cache/' . $senchaMemoryModelName . '.php';
		        if(!file_exists($file)) return false;

		        @include "$file";
		        $model_data = isset($model_data) ? $model_data : false;
		        return $model_data['time'];

	        }else{
		        self::__checkMemoryModel();
		        $sql = "SELECT * FROM _sencha_model WHERE model = ?;";
		        $params = [$senchaMemoryModelName];
		        $sth = @self::$__conn->prepare($sql);
		        $sth->execute($params);
		        $model = $sth->fetch(PDO::FETCH_ASSOC);
		        if(is_array($model)) return strtotime($model['modelLastChange']); else return false;
	        }

        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__getSenchaModelLastChange', [$senchaModelName, $instance]);
        }
    }

    /**
     * function __isModelInMemory($senchaModel = NULL):
     * Method to check if a sencha model is loaded into memory.
     * @param null $senchaModelName
     * @param null $instance
     * @return bool
     */
    public static function __isModelInMemory($senchaModelName = null, $instance = null)
    {
        try
        {
	        $senchaMemoryModelName = isset($instance) ? $senchaModelName .'_' . $instance : $senchaModelName;

	        if(self::$cache_type == 'file'){

	        	$file = MATCHA_ROOT . 'cache/' . $senchaMemoryModelName . '.php';
		        return file_exists($file);

	        }else{
		        self::__checkMemoryModel();
		        $sql = "SELECT * FROM _sencha_model WHERE model = ?;";
		        $params = [$senchaMemoryModelName];
		        $sth = @self::$__conn->prepare($sql);
		        $sth->execute($params);
		        $model = $sth->fetch(PDO::FETCH_ASSOC);

		        if(is_array($model)) return true; else return false;
	        }

        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__isModelInMemory', [$senchaModelName, $instance]);
        }
    }

    /**
     * function __destroySenchaMemoryModel($senchaModel = NULL):
     * Method to delete a sencha model stored in memory.
     * @param null $senchaModelName
     * @param null $instance
     * @return bool
     */
    public static function __destroySenchaMemoryModel($senchaModelName = null, $instance = null)
    {
        try
        {
	        $senchaMemoryModelName = isset($instance) ? $senchaModelName .'_' . $instance : $senchaModelName;

	        if(self::$cache_type == 'file'){

		        $file = MATCHA_ROOT . 'cache/' . $senchaMemoryModelName;
		        unlink($file);
		        return true;

	        }else{
		        self::__checkMemoryModel();
		        $sql = "DELETE FROM _sencha_model WHERE model = ?;";
		        $params = [$senchaMemoryModelName];
		        $sth = @self::$__conn->prepare($sql);
		        $sth->execute($params);

		        return true;
	        }

        }
        catch(PDOException $e)
        {
	        return self::PDOExceptionHandler($e, 'self::__destroySenchaMemoryModel', [$senchaModelName, $instance]);
        }
    }

	/**
	 * @param $e         PDOException
	 * @param $method    string
	 * @param $arguments array
	 *
	 * @return mixed
	 */
	private static function PDOExceptionHandler($e, $method, $arguments){
		if (strpos($e->getMessage(), 'server has gone away') === false || !Matcha::reconnect()) {
			MatchaErrorHandler::__errorProcess($e);
			return false;
		}
		MatchaErrorHandler::__errorProcess($e);
		return call_user_func_array($method, $arguments);
	}

}
