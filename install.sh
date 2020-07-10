#!/bin/bash

# Install the dependencies
rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Install the dependencies ? y/n"
read rep
done

if [ $rep = "y" ];
then
  composer install
fi



read -p "Press any key to continue ..."
clear


# Drop and create the database, and execute migrations
rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Drop if exist and create a new database ? y/n"
read rep
done

if [ $rep = "y" ];
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

read -p "Press any key to continue ..."
clear

# Execute the migrations
rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Execute migrations script ? y/n"
read rep
done


if [ $rep = "y" ];
then
  php bin/console doctrine:migrations:migrate
fi

# Executer the fixture
rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Executer the fixture ? y/n"
read rep
done


if [ $rep = "y" ];
then
  php bin/console doctrine:fixtures:load --no-interaction
fi

read -p "Press any key to continue ..."
clear







rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Launch the project in PhpStorm ? y/n"
read rep
done

if [ $rep = "y" ];
then
 phpstorm . &
fi

read -p "Press any key to continue ..."
clear

rep=""
until [[ ${rep} =~ ^[y,n]+$ ]]; do
echo "Launch the server ? y/n"
read rep

done
if [ $rep = "y" ];
then
 php -S 127.0.0.1:3906 -t public
fi

# Exit
read -p "Press any key to exit ..."
