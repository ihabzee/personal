<?php
/**
 * Created by PhpStorm.
 * User: ihab
 * Date: 7/24/2017
 * Time: 6:54 PM
 */

require_once "database.php";

 $sqlhost="localhost";
 $sqluser="root";
 $sqlpass="password";
 $sqldb="opskin";
 $sqltype="mysql";


$db = new database($sqltype, $sqlhost,$sqldb , $sqluser, $sqlpass );
$db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


/**
 * get all information from Products DB Table
 */
echo "<h1>get all information from Products DB Table</h1><br>";
$sth = $db->selectRow( 'products' ,"*" );
$result = $sth->fetchAll();

print_r( $result );

/**
 * get specified columns information from Products DB Table
 */
echo "<h1>get specified columns information from Products DB Table</h1><br>";
$sth = $db->selectRow( 'products' ,"id, name" );
$result = $sth->fetchAll();

print_r( $result );


/**
 * get specified columns information from Products DB Table where id = 57
 * sing is optional by default is "="
 * table is optional is case quering multiple table in same query
 */
echo "<h1>get specified columns information from Products DB Table where id = 57</h1><br>";

$sth = $db->where( array( "id" => 57) , "=" , 'products' );
$sth = $db->selectRow( 'products' ,"id, name" );
$result = $sth->fetchAll();
print_r( $result );


/**
 * get specified columns information from Products DB Table where id  between 57 and 60
 * sing is optional by default is "="
 * table is optional is case quering multiple table in same query
 */
echo "<h1>get specified columns information from Products DB Table where id  between 57 and 60</h1><br>";

$sth = $db->betweenAnd( 'id' , 57  , 60);
$sth = $db->selectRow( 'products' ,"id, name" );
$result = $sth->fetchAll();
print_r( $result );

/**
 * get specified columns information from Products DB Table where name contains 'Text'
 */
echo "<h1>get specified columns information from Products DB Table where name contains 'Text'</h1><br>";

$sth = $db->like( array( 'name' => '%Text%' )  );
$sth = $db->selectRow( 'products' ,"id, name" );
$result = $sth->fetchAll();
print_r( $result );


/**
 * get All information from Products with id = 57 and all of its variants
 */
echo "<h1>get All information from Products with id = 57 and all of its variants</h1><br>";
$sth = $db->where( array( "id" => 57) , "=" , 'products' );
$sth = $db->joinTables( "products" , "id" , "product_variants" , "product_id" , "" );
$sth = $db->selectRow( 'products' ,"*" );
$result = $sth->fetchAll();
print_r( $result );
