<?php

/**
 * PDO Abstract
 * 
 * A database layer based on PDO
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model.db.driver
 * 
 */
Application::load('Panda.model.db.driver.DriverInterface');

abstract class PDOAbstract implements DriverInterface {

   protected $_pdo;
   protected $_dsn;
   protected $_username;
   protected $_password;
   protected $_prefix;
   
   //Date formats
   protected $_dateFormat;
   protected $_timeFormat;
   protected $_dateTimeFormat;
   
   //Operators
   protected $_operators = array(
       'gt' => '>',
       'lt' => '<',
       'eq' => '=',
       'lte' => '<=',
       'gte' => '>=',
       'neq' => '<>'
   );

   public function __construct(array $credentials) {
      $this->_setDsn($this->_buildDsn($credentials));
      $this->_setUsername($credentials['username']);
      $this->_setPassword($credentials['password']);
      $this->_setPrefix($credentials['prefix']);
      $this->_pdo();
   }

   public function select(Query $query, $outputFormat = Query::OUTPUT_ARRAY) {
      if ($query->type() !== Query::SELECT_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: select query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $tokensValues = $conditionsBuilder['tokensValues'];
      $limits = $query->limits();
      $sql = '
         SELECT ' . $this->_selectFields($query->fields()) . '
         FROM ' . implode(', ', $query->datasources()) . '
         ' . (!empty($conditions) ? $conditions : '' ) . '
         ' . (($query->limits() !== array()) ? 'LIMIT ' . implode(',', $limits) : '');
      switch ($outputFormat) {
         case Query::OUTPUT_ARRAY:
            $outputFormat = PDO::FETCH_ASSOC;
            break;
         case Query::OUTPUT_OBJECT:
            $outputFormat = PDO::FETCH_OBJ;
            break;
      }
      $result = $this->fetchAll($this->query($sql, $tokensValues), $outputFormat);

      /**
       * By default, the fields given by the database are all string-typed. We try to recognize
       * the native type (included serialized types) using some elementary comparaisons.
       */
      foreach ($result as &$row) {
         foreach ($row as &$field) {
            if (ctype_digit($field)) {
               settype($field, 'int');
            } else if (is_numeric($field)) {
               settype($field, 'float');
            } else if (@unserialize($field) !== false) {
               $field = unserialize($field);
            } else if ($field === null) {
               continue;
            } else {
               try {
                  $datetimeField = new DateTime($field);
               } catch (Exception $e) {
                  //This isn't a dateTime field
                  continue;
               }
               $field = $datetimeField;
            }
         }
      }

      if (count($query->fields()) === 1) {
         if ($limits !== array() && $limits[0] === 1) {
            $result = $result[0][implode('', $query->fields())];
         } else {
            $rawResult = $result;
            $result = array();
            foreach ($rawResult as $resultRow) {
               $result[] = $resultRow[implode('', $query->fields())];
            }
         }
      } else if ($limits !== array() && $limits[0] === 1) {
         $result = $result[0];
      }
      return $result;
   }

   public function count(Query $query) {
      if ($query->type() !== Query::COUNT_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: count query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $tokensValues = $conditionsBuilder['tokensValues'];
      $sql = '
         SELECT COUNT(1) AS count
         FROM ' . implode(', ', $query->datasources()) . '
         ' . (!empty($conditions) ? $conditions : '' );
      $result = $this->fetchAll($this->query($sql, $tokensValues));
      return (int) $result[0]['count'];
   }

   public function sum(Query $query) {
      if ($query->type() !== Query::SUM_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: sum query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $tokensValues = $conditionsBuilder['tokensValues'];
      $sql = '
         SELECT SUM(' . $this->_selectFields($query->fields()) . ') AS sum
         FROM ' . implode(', ', $query->datasources()) . '
         ' . (!empty($conditions) ? $conditions : '' );
      $result = $this->fetchAll($this->query($sql, $tokensValues));
      if (ctype_digit($result[0]['sum'])) {
         return (int) $result[0]['sum'];
      } else {
         return (float) $result[0]['sum'];
      }
   }
   
   public function avg(Query $query) {
      if ($query->type() !== Query::AVG_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: sum query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $tokensValues = $conditionsBuilder['tokensValues'];
      $sql = '
         SELECT AVG(' . $this->_selectFields($query->fields()) . ') AS average
         FROM ' . implode(', ', $query->datasources()) . '
         ' . (!empty($conditions) ? $conditions : '' );
      $result = $this->fetchAll($this->query($sql, $tokensValues));
      if (ctype_digit($result[0]['average'])) {
         return (int) $result[0]['average'];
      } else {
         return (float) $result[0]['average'];
      }
   }

   public function insert(Query $query) {
      if ($query->type() !== Query::INSERT_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: insert query required.'));
      }
      $datasources = $query->datasources();
      $insertParts = $this->_insertParts($query->setValues());
      $tokensValues = $this->_tokensValues($query->tokensValues());
      $sql = '
         INSERT INTO ' . $datasources[0] . '(' . $insertParts['fields'] . ')
         VALUES(' . $insertParts['tokens'] . ')
         ';
      return $this->query($sql, $tokensValues);
   }

   public function update(Query $query) {
      if ($query->type() !== Query::UPDATE_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: update query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $updateStatement = $this->_updateStatement($query->setValues());
      $tokensValues = $this->_tokensValues($conditionsBuilder['tokensValues']);
      $sql = '
         UPDATE ' . implode(', ', $query->datasources()) . '
         SET ' . $updateStatement . '
         ' . (!empty($conditions) ? $conditions : '' );
      return $this->query($sql, $tokensValues);
   }

   public function delete(Query $query) {
      if ($query->type() !== Query::DELETE_QUERY) {
         throw new InvalidArgumentException(__('Invalid query: delete query required.'));
      }
      $conditionsBuilder = $this->_buildConditions($query->conditions(), $query->tokensValues());
      $conditions = $conditionsBuilder['whereStatement'];
      $tokensValues = $conditionsBuilder['tokensValues'];
      $sql = '
         DELETE FROM ' . implode(', ', $query->datasources()) . '
         ' . (!empty($conditions) ? $conditions : '' );
      return $this->query($sql, $tokensValues);
   }

   public function createDatasource(Query $query) {
      
   }

   public function dropDatasource(Query $query) {
      
   }

   public function query($sql, array $tokens = null) {
      if (empty($sql)) {
         throw new InvalidArgumentException(__('Unable to execute the query: $sql is empty.'));
      }
      try {
         $preparedQuery = $this->prepare($sql);
         if (!empty($tokens)) {
            $preparedQuery->execute($tokens);
         } else {
            $preparedQuery->execute();
         }
      } catch (PDOException $e) {
         throw new RuntimeException(__('Unable to execute the query: %s', $e->getMessage()));
      }
      return $preparedQuery;
   }

   public function prepare($sqlQuery) {
      return $this->_pdo()->prepare($sqlQuery);
   }

   public function fetch(PDOStatement $preparedQuery, $mode = PDO::FETCH_ASSOC) {
      return $preparedQuery->fetch($mode);
   }

   public function fetchAll(PDOStatement $preparedQuery, $mode = PDO::FETCH_ASSOC) {
      return $preparedQuery->fetchAll($mode);
   }

   abstract public function primaryKeysOf($datasource);

   public function lastInsertId() {
      return $this->_pdo()->lastInsertId();
   }

   public function dateFormat() {
      return $this->_dateFormat;
   }

   public function timeFormat() {
      return $this->_timeFormat;
   }

   public function dateTimeFormat() {
      return $this->_dateTimeFormat;
   }

   protected function _pdo() {
      if (!$this->_pdo) {
         try {
            $this->_pdo = new PDO($this->_dsn(), $this->_username(), $this->_password());
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         } catch (PDOException $e) {
            throw new RuntimeException(__('Error while trying to connect to the database: %s', $e->getMessage()));
         }
      }
      return $this->_pdo;
   }

   abstract protected function _buildDsn(array $credentials);

   protected function _dsn() {
      return $this->_dsn;
   }

   protected function _username() {
      return $this->_username;
   }

   protected function _password() {
      return $this->_password;
   }

   public function prefix() {
      return $this->_prefix;
   }

   protected function _setDsn($dsn) {
      if (is_string($dsn) && !empty($dsn)) {
         $this->_dsn = $dsn;
      }
   }

   protected function _setUsername($username) {
      if (is_string($username) && !empty($username)) {
         $this->_username = $username;
      }
   }

   protected function _setPassword($password) {
      if (is_string($password) && !empty($password)) {
         $this->_password = $password;
      }
   }

   protected function _setPrefix($prefix) {
      if (is_string($prefix) && !empty($prefix)) {
         $this->_prefix = $prefix;
      }
   }

   protected function _escapeField($field) {
      return $field;
   }

   protected function _selectFields($fields) {
      return is_array($fields) ? implode(', ', array_map(array($this, '_escapeField'), $fields)) : '*';
   }

   protected function _insertParts(array $setValues) {
      if (empty($setValues)) {
         throw new InvalidArgumentException(__('Please use at least one field to insert.'));
      }
      $fields = array();
      $tokens = array();
      foreach ($setValues as $value) {
         foreach ($value as $key => $token) {
            $fields[] = $key;
            $tokens[] = ':' . $token;
         }
      }
      return array('fields' => $this->_selectFields($fields), 'tokens' => implode(', ', $tokens));
   }

   protected function _updateStatement(array $setValues) {
      if (empty($setValues)) {
         throw new InvalidArgumentException(__('Please use at least one field to set.'));
      }
      $placeHolders = array();
      foreach ($setValues as $value) {
         foreach ($value as $key => $token) {
            $placeHolders[] = $this->_escapeField($key) . ' = :' . $token;
         }
      }
      return implode(', ', $placeHolders);
   }

   protected function _tokensValues(array $tokensValues) {
      foreach ($tokensValues as &$value) {
         if (is_object($value)) {
            if ($value instanceof DateTime) {
               $value = (string) $value->format($this->dateTimeFormat());
            } else {
               $value = serialize($value);
            }
         } else if (is_array($value)) {
            $value = serialize($value);
         } else if (is_bool($value)) {
            $value = (int) $value;
         } else {
            continue;
         }
      }
      return $tokensValues;
   }

   protected function _buildConditions(array $conditions, array $tokensValues) {
      if ($conditions !== array()) {
         $whereStatement = 'WHERE ';
         $firstCondition = true;
         foreach ($conditions as $conditionGroup) {
            if (!empty($conditionGroup)) {
               $currentType = $conditionGroup['type'];
               $currentGroupType = $conditionGroup['groupType'];
               if ($firstCondition) {
                  $whereStatement .= '(';
                  $firstCondition = false;
               } else {
                  $whereStatement .= ' ' . $currentGroupType . ' (';
               }
               $subWhereStatement = '';
               foreach ($conditionGroup['conditions'] as $subCondition) {
                  $subWhereStatement .= $currentType . ' (' . $this->_escapeField($subCondition['field']) . ' ';
                  if (!array_key_exists($subCondition['operator'], $this->_operators)) {
                     throw new ErrorException(__('Unable to build the WHERE statement: invalid operator "%s"', $subCondition['operator']));
                  }
                  if (is_array($tokensValues[$subCondition['token']]) && !empty($tokensValues[$subCondition['token']])) {
                     //IN and NOT IN conditions
                     if ($subCondition['operator'] === 'eq' || $subCondition['operator'] === 'neq') {
                        $subWhereStatement .= $subCondition['operator'] === 'eq' ? 'IN (' : 'NOT IN (';
                        for ($i = 0; $i < count($tokensValues[$subCondition['token']]); ++$i) {
                           $subWhereStatement .= $this->_escapeValue($tokensValues[$subCondition['token']][$i]) . ', ';
                        }
                        $subWhereStatement = rtrim($subWhereStatement, ', ') . ') ) ';
                        unset($tokensValues[$subCondition['token']]);
                     } else {
                        throw new ErrorException(__('Unable to build the WHERE statement: "eq" and "neq" are the only supported operators for array tokens.'));
                     }
                  } else if ($tokensValues[$subCondition['token']] === null) {
                     //IS NULL and IS NOT NULL
                     if ($subCondition['operator'] === 'eq' || $subCondition['operator'] === 'neq') {
                        $subWhereStatement .= ($this->_operators[$subCondition['operator']] === 'eq') ? 'IS NULL) ' : 'IS NOT NULL) ';
                        unset($tokensValues[$subCondition['token']]);
                     } else {
                        throw new ErrorException(__('Unable to build the WHERE statement: "eq" and "neq" are the only supported operators for null tokens.'));
                     }
                  } else {
                     $subWhereStatement .= $this->_operators[$subCondition['operator']] . ' :' . $subCondition['token'] . ') ';
                  }
               }
               $whereStatement .= ltrim($subWhereStatement, $currentType) . ')';
            }
         }
         return array('whereStatement' => $whereStatement, 'tokensValues' => $tokensValues);
      } else {
         array('whereStatement' => '', 'tokensValues' => $tokensValues);
      }
   }

   protected function _escapeValue($field) {
      return $this->_pdo()->quote($field);
   }

}