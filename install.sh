#!/bin/bash

# Install the dependencies
rep=""
until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
echo "Install the project ? y/n"
read -n1 rep; echo
done

if [ $rep = "y" ] || [ $rep = "Y" ];
then
	rep=""
	# Install the dependencies
	rep=""
	until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
	echo "Install the dependencies ? y/n"
	read -n1 rep; echo
	done

	if [ $rep = "y" ] || [ $rep = "Y" ];
	then
	  composer install
	fi

	read -n1 -p "Press any key to continue ..."
	clear


	# Drop and create the database, and execute migrations
	rep=""
	until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
	echo "Drop if exist and create a new database ? y/n"
	read -n1 rep; echo
	done

	if [ $rep = "y" ] || [ $rep = "Y" ];
	then
	  echo "Drop database if exist"
	  if (! php bin/console doctrine:database:drop --force)
	  then
		clear
		echo "--------------------------"
		echo "|    database not exist   |"
		echo "--------------------------"
	  else
		echo "The database has been successfully deleted"
	  fi
	  echo ""
	  echo "Create database"
	  php bin/console doctrine:database:create
	fi

	read -n1 -p "Press any key to continue ..."
	clear

	# Execute the migrations
	rep=""
	until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
	echo "Execute migrations script ? y/n"
	read -n1 rep; echo
	done


	if [ $rep = "y" ] || [ $rep = "Y" ];
	then
	  php bin/console doctrine:migrations:migrate
	fi

	read -n1 -p "Press any key to continue ..."
	clear


	# Executer the fixture
	rep=""
	until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
	echo "Executer the fixture ? y/n"
	read -n1 rep; echo
	done


	if [ $rep = "y" ] || [ $rep = "Y" ];
	then
	  php bin/console doctrine:fixtures:load --no-interaction
	fi

	read -n1 -p "Press any key to continue ..."
	clear


fi

rep=""
until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
echo "Launch the project in PhpStorm ? y/n"
read -n1 rep; echo
done

if [ $rep = "y" ] || [ $rep = "Y" ];
then
 phpstorm . &
fi

read -n1 -p "Press any key to continue ..."
clear

rep=""
until [[ ${rep} =~ ^[y,n,Y,N]+$ ]]; do
echo "Launch the server ? y/n"
read -n1 rep; echo

done
if [ $rep = "y" ] || [ $rep = "Y" ];
then
 php -S 127.0.0.1:3906 -t public
fi

# Exit
read -n1 -p "Press any key to exit ..."
