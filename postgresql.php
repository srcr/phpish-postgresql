<?php

	namespace phpish\postgresql;


	function _link($link=NULL)
	{
		static $_link;
		if (!is_null($link)) $_link = $link;
		return $_link;
	}


	function connect($host, $username, $password, $database)
	{

		$link = pg_connect("host=" . $host . " port=5432 dbname=" . $database . " user=" . $username . " password=".$password);
		if (!$link)
		{
			error_log('pg_connect error: (' .  pg_last_error($link) . ') ' . pg_last_error($link));
			return false;
		}

		_link($link);
		return $link;
	}


	function query($query, $params=array(), $link=NULL)
	{
		$link = $link ?: _link();
		$params = array_map(function($val) use($link) {
			return pg_escape_string($val);
		}, $params);
		$query = vsprintf($query, $params);
		return pg_query($link, $query);
	}


	function rows($query, $params=array(), $link=NULL)
	{
		$rows = array();
		if ($result = query($query, $params, $link))
		{
			while ($row = pg_fetch_assoc($result)) $rows[] = $row;
			pg_free_result($result);
		}

		return $rows;

	}


	function row($query, $params=array(), $link=NULL)
	{
		$row = array();
		if ($result = query($query, $params, $link))
		{
			$row = pg_fetch_assoc($result);
			pg_free_result($result);
		}

		return $row;

	}


	function num_rows($result)
	{
		return pg_num_rows($result);
	}


	function insert()
	{
		# TODO: insert('table', array('field1'=>array('%s'=>$value1)))
	}


	function insert_id($link=NULL)
	{
		$link = $link ?: _link();
                $insert_query = pg_query("SELECT lastval();");
                $insert_row = pg_fetch_row($insert_query);
		return $insert_row[0];
	}


	function update()
	{
		# TODO: update('table', array('field1'=>array('%s'=>$value1)), array('id'=>array('%d'=>1)))
	}


	function affected_rows($link=NULL)
	{
		$link = $link ?: _link();
		return  pg_affected_rows($link);
	}


	function close($link=NULL)
	{
		$link = $link ?: _link();
		return pg_close($link);
	}


	function error($link=NUll)
	{
		$link = $link ?: _link();
		return pg_last_error($link);
	}

?>
