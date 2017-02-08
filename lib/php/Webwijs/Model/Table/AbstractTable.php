<?php

namespace Webwijs\Model\Table;

use Webwijs\Loader\ClassLoader;

class AbstractTable
{
    protected $type;
    protected $config;
    public function __construct($type, $config = null)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->type = $type;
        if (is_array($config)) {
            $this->setConfig($config);
        }
    }
    public function save($model)
    {
        $status = false;
        $values = $this->_getModifiedValues($model);
        $formats = $this->_getFormats($values);
        if ($model->id) {
            $status = $this->db->update($this->tableName, $values, array('id' => $model->id), $formats, array('%d'));
        }
        else {
            if ($this->db->insert($this->tableName, $values, $formats)) {
                $model->id = $this->db->insert_id;
                $status = true;
            }
        }

        if ($status) {
            foreach (array_keys($values) as $field) {
                $model->setModified($field, false);
            }
        }
        return $status;
    }
    public function setConfig(array $config)
    {
        /*
         * make sure all the necessary keys are present that
         * are required by this table.
         */
        $isValid = true;
        foreach (array('table') as $key) {
            if (!array_key_exists($key, $config)) {
                $isValid = false;
            }
        }
        
        // update tables config
        if ($isValid) {
            $this->config = array_merge((array) $this->config, $config);
        }
    }
    
    /**
     * Returns the table name and will also prepend
     * the blog prefix to the table name.
     *
     * @return string the table name.
     */
    public function getTableName()
    {
        global $wpdb;
        if (is_object($wpdb)) {    
            return $wpdb->prefix . $this->config['table'];
        }
    }
    
    public function delete($model)
    {
        return $this->deleteBy(array('id' => $model->id));
    }
    public function deleteBy($where = null)
    {
        $where = $this->_formatWhere($where);
        if (!empty($where)) {
            $sql = 'DELETE FROM ' . $this->tableName . ' WHERE ' . $where;
            return $this->db->query($sql);
        }
    }
    public function findIds()
    {
        $sql = 'SELECT id FROM ' . $this->tableName;
        return $this->db->get_col($sql);
    }
    public function find($id)
    {
        return $this->findOneBy(array('id' => $id));
    }
    public function findAll($order=null)
    {
        return $this->findBy(null,$order);
    }
    public function findBy($where = null, $order = null, $limit = null)
    {
        $sql = $this->_getSelectSql($where, $order, $limit);
        $results = $this->db->get_results($sql);

        if (!empty($results)) {
            return $this->_hydrate($results);
        }
    }
    public function findOneBy($where = null, $order = null)
    {
        $objects = $this->findBy($where, $order, 1);
        if (!empty($objects)) {
            return array_shift($objects);
        }
    }
    public function getConfig()
    {
        return $this->config;
    }

    protected function _getSelectSql($where = null, $order = null, $limit = null)
    {
        $where = $this->_formatWhere($where);
        $order = $this->_formatOrder($order);
        $limit = $this->_formatLimit($limit);

        $sql = 'SELECT ' . implode(', ', array_keys($this->config['fields'])) . ' FROM ' . $this->tableName;
        !empty($where) && $sql .= ' WHERE ' . $where;
        !empty($order) && $sql .= ' ORDER BY ' . $order;
        !empty($limit) && $sql .= ' LIMIT ' . $limit;
        return $sql;
    }
    protected function _hydrate($results, $type = null)
    {
        if (is_null($type)) {
            $type = $this->type;
        }
        
        $objects = array();
        foreach ($results as $row) {
            $class = ClassLoader::loadStatic('model', $type);
            if (!empty($class)) {
                $objects[] = new $class($row);
            }
        }
        return $objects;
    }
    protected function _formatWhere($where)
    {
        $parts = array();
        if (is_array($where)) {
            $formats = $this->_getFormats($where);
            foreach ($where as $name => $value) {
                if (is_array($value) && !empty($value)) {
                    if (isset($value['eq'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['eq'], '=', $formats[$name]);
                    }
                    if (isset($value['neq'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['neq'], '!=', $formats[$name]);
                    }
                    if (isset($value['like'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['like'], 'LIKE', $formats[$name]);
                    }
                    if (isset($value['nlike'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['nlike'], 'NOT LIKE', $formats[$name]);
                    }
                    if (isset($value['in'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['in'], 'IN', $formats[$name]);
                    }
                    if (isset($value['nin'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['nin'], 'NOT IN', $formats[$name]);
                    }
                    if (isset($value['gt'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['gt'], '>', $formats[$name]);
                    }
                    if (isset($value['gteq'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['gteq'], '>=', $formats[$name]);
                    }
                    if (isset($value['lt'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['lt'], '<', $formats[$name]);
                    }
                    if (isset($value['lteq'])) {
                        $parts[] = $this->_formatCustomWhere($name, $value['lteq'], '<=', $formats[$name]);
                    }
                    // for backwards compatibility: array('id' => array(1, 2, 3, 4, 5))
                    if (array_keys($value) === range(0, count($value) - 1)) {
                        $parts[] = $this->_formatCustomWhere($name, $value, 'IN', $formats[$name]);
                    }
                    unset($where[$name]);
                }
                else {
                    $parts[] = '(' . $name . ' = ' . $formats[$name] . ')';
                }
            }
            $where = $this->db->prepare(implode(' AND ', $parts), $where);
        }
        return $where;
    }
    protected function _formatCustomWhere($name, $value, $operator, $format)
    {
        if (in_array($operator, array('IN', 'NOT IN'))) {
            $formatedValues = array();
            foreach ((array) $value as $valueItem) {
                $formatedValues[] = $this->db->prepare($format, $valueItem);
            }
            $value = '(' . implode(', ', $formatedValues) . ')';
        }
        else {
            $value = $this->db->prepare($format, $value);
        }
        return '(' . $name . ' ' . $operator . ' ' . $value . ')';
    }
    protected function _formatOrder($order)
    {
        if (empty($order) && isset($this->config['orderby'])) {
            $order = $this->config['orderby'];
        }
        return $order;
    }
    protected function _formatLimit($limit)
    {
        return $limit;
    }

    protected function _getModifiedValues($model, $skipId = true)
    {
        $values = array();
        foreach (array_keys($this->config['fields']) as $name) {
            if ($skipId && ($name == 'id')) {
                continue;
            }
            if ($model->isModified($name)) {
                $values[$name] = $model->$name;
            }
        }
        return $values;
    }
    protected function _getFormats($values)
    {
        $formats = array();
        foreach (array_keys($values) as $name) {
            $formats[$name] = isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : '%s';
        }
        return $formats;
    }
    public function __get($name)
    {
        $method = sprintf('get%s', ucfirst($name));
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
    }
}
