<?php
/**
 * Created by PhpStorm.
 * User: ihab
 * Date: 7/24/2017
 * Time: 6:36 PM
 */
abstract class query extends \PDO {

    /**
     * @var array where statement values
     */
    public $where = array();

    /**
     * @var where statement text
     */
    public $whereText = null;
    /**
     * @var array Like var
     */
    public $like = array();
    /**
     * @var Like Statement Text
     */
    public $likeText = null ;
    /**
     * @var order statement Text
     */
    public $order = '' ;
    /**
     * @var array In var
     */
    public $in = array();

    /**
     * @var In Statement Text
     */
    public $inText = null ;
    /**
     * @var Limit Statement Text
     */
    public $limitText = null ;
    /**
     * @var array group var
     */
    public $group = array();

    /**
     * @var group statement text
     */
    public $groupText ='';
    /**
     * @var Join var
     */
    public $join = array();
    /**
     * @var Join Statement String
     */
    public $joinText  =  '' ;


    /**
     * Database Object
     * @param $sql
     * @param $data
     * @return mixed
     */
    abstract protected function sql($sql, $data );

    /**
     * Database Object
     * @param $table
     * @param $data
     * @param $display
     * @return mixed
     */
    abstract protected function selectRow($table, $data, $display );

    /**
     * Database Object
     * @param $table
     * @param $where
     * @return mixed
     */
    abstract protected function delete($table, $where);

    /**
     * add Where to SQL statement
     * @param $where
     * @param string $sign
     * @param string $table
     * @return $this
     */
    public function where( $where , $sign = '=' , $table = '')
    {
        $this->where = array_merge( $where , $this->where ) ;
        ksort($where) ;
        $fieldNames = implode('`,`' , array_keys($where));
        $fieldValues = ':' . implode(', :' , array_keys($where)) ;
        $fieldDetails= null;
        foreach( $where as $key => $value )
        {
            if( $table != '' )
            {
                $fieldDetails .= " $table.`$key`  $sign:$key AND ";
            }
            else
            {
                $fieldDetails .= " `$key`  $sign:$key AND ";
            }
        }
        $fieldDetails = rtrim($fieldDetails , 'AND ');
        if( $this->whereText != '' )
        {
            $this->whereText .= ' AND ';
        }
        $this->whereText .= $fieldDetails;
        return $this;
    }

    /**
     * Join Two Tables
     * @param $firstTable
     * @param $firstTableColumnName
     * @param $secondTable
     * @param $secondTableColumnName
     * @param string $type
     * @return $this
     */
    public function joinTables( $firstTable , $firstTableColumnName , $secondTable , $secondTableColumnName , $type = ' INNER ')
    {
        $this->joinText .= " $type JOIN $secondTable ON $firstTable.$firstTableColumnName = $secondTable.$secondTableColumnName ";
        return $this;
    }

    /**
     * Join Two table with Like Statement
     * @param $firstTable
     * @param $firstTableColumnName
     * @param $secondTable
     * @param $secondTableColumnName
     * @param string $type
     * @return $this
     */
    public function joinTablesLike( $firstTable , $firstTableColumnName , $secondTable , $secondTableColumnName , $type = ' INNER ')
    {
        $this->joinText .= " $type JOIN $secondTable ON $firstTable.$firstTableColumnName LIKE $secondTableColumnName ";
        return $this;
    }

    /**
     * add Between and to SQL statement
     * @param $field
     * @param $start
     * @param $end
     * @return $this
     */
    public function betweenAnd( $field ,  $start , $end )
    {
        $this->where['start_value'] =  $start;
        $this->where['end_value'] =  $end;
        if( $this->whereText != '' )
        {
            $this->whereText .= ' AND ';
        }
        $this->whereText .= "$field BETWEEN :start_value AND :end_value";
        return $this;
    }

    /**
     * add Like to SQL statement
     * @param $where
     * @param string $table
     * @return $this
     */
    public function like( $where , $table = '' )
    {
        $this->like =  $where;
        ksort($where) ;
        $fieldNames = implode('`,`' , array_keys($where));
        $fieldValues = ':' . implode(', :' , array_keys($where)) ;
        $fieldDetails= null;
        foreach( $where as $key => $value )
        {
            if( $table != '' )
            {
                $fieldDetails .= "$table.`$key`  Like :$key AND ";
            }
            else
            {
                $fieldDetails .= "`$key`  Like :$key AND ";
            }
        }
        $fieldDetails = rtrim($fieldDetails , 'AND ');
        $this->likeText = $fieldDetails;
        return $this;
    }

    /**
     * add Group to SQL statement
     * @param $fields
     * @param string $table
     * @return $this
     */
    public function group( $fields , $table = ''  )
    {
        $fieldDetails= null;
        foreach( $fields as $value )
        {

            if( $table != '' )
            {
                $fieldDetails .= "$table.`$value` , ";
            }
            else
            {
                $fieldDetails .= "$value , ";
            }

        }
        $fieldDetails = rtrim($fieldDetails , ', ');
        $this->group = " GROUP BY $fieldDetails  ";
        return $this;
    }

    /**
     * add In to SQL statement
     * @param $field
     * @param $data
     * @param string $table
     * @return $this
     */
    public function in( $field , $data , $table = '' )
    {
        $fieldDetails = '(';
        $this->in = $data ;
        foreach( $data as $value )
        {
            $fieldDetails .= "'$value' , ";
        }
        $fieldDetails = rtrim($fieldDetails , ', ');
        $fieldDetails .= ')' ;
        if( $this->inText != '' )
        {
            $this->inText .= ' AND ';
        }
        if( $table != '' )
        {
            $this->inText .= $table.'.'.$field . " IN " . $fieldDetails;
        }
        else
        {
            $this->inText .=  $field . " IN " . $fieldDetails;
        }

        return $this;
    }

    /**
     * add "Not In" to SQL statement
     * @param $field
     * @param $data
     * @return $this
     */
    public function notin( $field , $data )
    {
        $fieldDetails = '(';
        $this->in = $data ;
        foreach( $data as $value )
        {
            $fieldDetails .= "'$value' , ";
        }
        $fieldDetails = rtrim($fieldDetails , ', ');
        $fieldDetails .= ')' ;
        if( $this->inText != '' )
        {
            $this->inText .= ' AND ';
        }
        $this->inText .= $field . " NOT IN " . $fieldDetails;
        return $this;
    }

    /**
     * add Order to SQL statement
     * @param $fields
     * @param string $dir
     * @return $this
     */
    public function order( $fields , $dir = 'DESC' )
    {
        $fieldDetails= null;
        foreach( $fields as $value )
        {
            $fieldDetails .= "$value , ";
        }
        $fieldDetails = rtrim($fieldDetails , ', ');
        if( $this->order == '' )
        {
            $this->order = " ORDER BY $fieldDetails $dir ";
        }
        else
        {
            $this->order .=  " ,$fieldDetails $dir ";
        }

        return $this;
    }

    /**
     * Use Switch case statement to order a field
     * @param String $field
     * @param Array $values
     * @return $this
     */
    public function field( $field , $values )
    {
        $order = 1 ;
        $fieldDetails = null;
        foreach( $values as $value )
        {
            $fieldDetails .= " ,  '$value'  ";
            $order++;
        }

        $this->order .= " ORDER BY FIELD( $field  $fieldDetails )  ";
        return $this;
    }


    /**
     * add limit to SQL statement
     * @param $limit
     * @return $this
     */
    public function limit( $limit )
    {
        $start  = $limit['start'];
        $count  = $limit['count'];
        $this->limitText = " LIMIT $start , $count ";
        return $this;
    }


    /**
     * build query conditions
     * @return string
     */
    public function condition()
    {
        $condition = " WHERE " ;
        if( ! empty( $this->where ))
        {
            $condition .=  $this->whereText . " AND ";
        }
        if(  ! empty( $this->like ))
        {
            $condition .=  $this->likeText . " AND ";
        }
        if( ! empty( $this->in ))
        {
            $condition .=  $this->inText . " AND ";
        }
        $condition = rtrim($condition , 'AND ');
        if( empty( $this->where ) && empty( $this->like ) && empty( $this->in )   )
        {
            $condition = '';
        }
        //Add group by
        if( !empty($this->group) )
        {
            $condition .= $this->group ;
        }
        //Add order
        if( !empty($this->order) )
        {
            $condition .= $this->order ;
        }
        //Add Limit
        if( !empty($this->limitText) )
        {
            $condition .= $this->limitText ;
        }
        //Add join
        if( !empty($this->joinText) )
        {
            $condition = $this->joinText . ' ' . $condition ;
        }
        return $condition;
    }
}