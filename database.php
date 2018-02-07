<?php
require_once "query.php";
/**
 * Created by PhpStorm.
 * User: ihab
 * Date: 7/24/2017
 * Time: 6:11 PM
 */
class database extends query
{
    /**
     * @var sth PDO query var
     */
    protected $sth;
    //abstract so we can clean from here once done updating or inserting
    public $form;

    /**
     * database constructor.
     * @param $DBTYPE
     * @param $DBHOST
     * @param $DBNAME
     * @param $DBUSER
     * @param $DBPASS
     */
    public function __construct($DBTYPE, $DBHOST, $DBNAME, $DBUSER, $DBPASS)
    {
        try {
            parent::__construct('' . $DBTYPE . ':host=' . $DBHOST . ';dbname=' . $DBNAME . '', $DBUSER, $DBPASS);
        } catch (PDOException $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }

    /*
    *
    *selectRow
    *@param string $table
    *@param string $data
    *@param array $where
    *
    * return PDO object
    */
    public function sql($sql, $data = null)
    {
        try {
            $this->sth = $this->prepare("$sql");
            //echo "$sql<br>";
            if (!empty($data)) {
                foreach ($data as $key => $values) {
                    if (is_numeric($key)) {
                        $key += 1;
                    }
                    $this->sth->bindvalue($key, $values);
                }
            }
            if ($this->sth->execute()) {
                return $this->sth;
            } else {
                die(print_r($this->sth->errorInfo(), true));
            }
        } catch (\PDOException $e) {
            print_r("<div class='alert alert-danger'>DataBase Error: record could not be fetched.<br>" . $e->getMessage() . '</div>');
            die();
        } catch (\Exception $e) {
            print_r("<div class='alert alert-danger'>General Error: record could not be fetched.<br>" . $e->getMessage() . '</div>');
        }
    }
    /*
    *
    *selectRow
    *@param string $table
    *@param string $data
    *@param array $where
    *
    * return PDO object
    */
    public function selectRow($table, $data, $display = false)
    {
        try {
            $condition = $this->condition();
            //	echo "SELECT $data from $table $condition<br>";
            $this->sth = $this->prepare("SELECT $data from $table $condition");
            if ($display) {
                print_r("SELECT $data from $table $condition");
            }
            foreach ($this->where as $key => $values) {
                if (is_numeric($key)) {
                    $key += 1;
                }
                if ($display) {
                    print_r($key);
                    print_r($values);
                }
                $this->sth->bindvalue($key, $values);
            }
            foreach ($this->like as $key => $values) {
                $this->sth->bindParam($key, $values);
            }
            if ($this->sth->execute()) {
                $this->CleanVariable();
                return $this->sth;
            }
        } catch (\PDOException $e) {
            print_r("<div class='alert alert-danger'>DataBase Error: record could not be fetched from $table.<br>" . $e->getMessage() . '</div>');
            die();
        } catch (\Exception $e) {
            print_r("<div class='alert alert-danger'>General Error: record could not be fetched .<br>" . $e->getMessage() . '</div>');
            //die();
        }
    }
    /**
     * @param $table
     * @param $where
     * @return $this
     */
    public function delete($table, $where)
    {
        try {
            ksort($where);
            $fieldNames = implode('`,`', array_keys($where));
            $fieldvalues = ':' . implode(', :', array_keys($where));
            $fieldDetails = null;
            foreach ($where as $key => $value) {
                $fieldDetails .= "`$key` =:$key AND ";
            }
            $fieldDetails = rtrim($fieldDetails, 'AND ');
            $this->sth = $this->prepare("DELETE from $table where $fieldDetails");
            foreach ($where as $key => $values) {
                $this->sth->bindvalue($key, $values);
            }
            if ($this->sth->execute()) {
                $this->result = true;
                return $this;
            } else {
                die(print_r($this->sth->errorInfo(), true));
            }
        } catch (\PDOException $e) {
            print_r("<div class='alert alert-danger'>DataBase Error: record could not be deleted.<br>" . $e->getMessage() . '</div>');
            die();
        } catch (\Exception $e) {
            print_r("<div class='alert alert-danger'>General Error: record could not be deleted.<br>" . $e->getMessage() . '</div>');
            //die();
        }
    }

    /**
     * get last inserted id
     * @return string
     */
    public function getLastrecordinserted()
    {
        return $this->lastInsertId();
    }

    /**
     * clean database object once query is done
     */
    public function CleanVariable()
    {
        $this->where = array();
        $this->whereText = null;
        $this->like = array();
        $this->likeText = null;
        $this->order = '';
        $this->in = array();
        $this->inText = null;
        $this->limitText = null;
        $this->group = array();
        $this->groupText = '';
        $this->join = array();
        $this->joinText = '';
    }
}