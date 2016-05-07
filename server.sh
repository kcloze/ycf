#!/bin/bash
pidFile="src/runtime/master.pid";

function start(){
	php ./src/Core/YcfHttpServer.php $pidFile;
	#php ./src/Core/YcfDBPool.php start;
  
	#printf $?
	if [ $? == 0 ]; then
		printf "ycf_server start OK\r\n"
		return 0
	else
		printf "ycf_server start FAIL\r\n"
		return 1
	fi
}

function stop(){
	master_pid=$(php -r "echo file_get_contents(realpath(dirname(__FILE__)) . '/src/runtime/master.pid');");
	if [ -n "$master_pid" ];then
		kill -15 $master_pid;
		sleep 1;
		kill -9 $master_pid;
		sleep 1;
		if [ $? == 0 ];then
			#php ./src/Core/YcfDBPool.php stop
			php -r "file_put_contents(realpath(dirname(__FILE__)) . '/src/runtime/master.pid','');"
			printf "ycf_server stop OK \n"
			return 0
		fi
	else
		printf "ycf_server stop FAIL\r\n"
		return 1	
	fi
	
}
function startDB(){
	#php ./src/Core/YcfHttpServer.php $pidFile;
	php ./src/Core/YcfDBPool.php start;
  
	#printf $?
	if [ $? == 0 ]; then
		printf "ycf_db_pool start OK\r\n"
		return 0
	else
		printf "ycf_db_pool start FAIL\r\n"
		return 1
	fi
}

function stopDB(){
	#php ./src/Core/YcfHttpServer.php $pidFile;
	php ./src/Core/YcfDBPool.php stop;
  
	#printf $?
	if [ $? == 0 ]; then
		printf "ycf_db_pool stop OK\r\n"
		return 0
	else
		printf "ycf_db_pool stop FAIL\r\n"
		return 1
	fi
}





case $1 in 
	
	start )
		start
		#sleep 1
		#startDB
	;;
	stop)
		stop
		#sleep 1
		#stopDB
	;;
	startDB )
		startDB
	;;
	stopDB)
		stopDB
	;;
	restart)
		stop
		sleep 1
		start
	;;

	*)
		start
	;;
esac

