#!/bin/bash
pidFile="/src/runtime/master_pid";

function start(){
	php ./src/Core/YcfHttpServer.php $pidFile;
	php ./pool_server.php start;
  
	#printf $?
	if [ $? == 0 ]; then
		printf "qserver start OK\r\n"
		return 0
	else
		printf "qserver start FAIL\r\n"
		return 1
	fi
}

function stop(){
	master_pid=$(php -r "echo file_get_contents(realpath(dirname(__FILE__)) . '/src/runtime/master_pid');");
	if [ -n "$master_pid" ];then
		kill -15 $master_pid;
		if [ $? == 0 ];then
			php ./pool_server.php stop
			php -r "file_put_contents(realpath(dirname(__FILE__)) . '/src/runtime/master_pid','');"
			printf "qserver stop OK \n"
			return 0
		fi
	else
		printf "qserver stop FAIL\r\n"
		return 1	
	fi
	
}





case $1 in 
	
	start )
		start
	;;
	stop)
		stop
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

