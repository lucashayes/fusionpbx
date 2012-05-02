#!/bin/bash

#------------------------------------------------------------------------------
#
# "THE WAF LICENSE" (version 1)
# This is the Wife Acceptance Factor (WAF) License.  
# jamesdotfsatstubbornrosesd0tcom  wrote this file.  As long as you retain this 
# notice you can do whatever you want with it. If you appreciate the work, 
# please consider purchasing something from my wife's wishlist. That pays 
# bigger dividends to this coder than anything else I can think of ;).  It also
# keeps her happy while she's being ignored; so I can work on this stuff. 
#   James Rose
#
# latest wishlist: http://www.stubbornroses.com/waf.html
#
# Credit: Based off of the BEER-WARE LICENSE (REVISION 42) by Poul-Henning Kamp
#
#------------------------------------------------------------------------------

#this script sets up the version of FreeSWITCH installed on the iso
#this makes it possible for make current to work as designed, and leaves
#the system in a more mantainable state.  It will only work on iso's created
#after 2012-05-01

#from here after, make current works as you would expect.

#CONFIG FILE
REPO="git://git.freeswitch.org/freeswitch.git"
CONFFILE="/etc/fusion_iso.conf"

GITVER=$(grep FSGITVER $CONFFILE|sed -e "s/FSGITVER=//")
MODCONF="/etc/freeswitch_iso_modules.conf"
URLGETSCRIPT="http://fusionpbx.googlecode.com/svn/contribs/soapee01/scripts/getfs.sh"

#---------------------
#   ENVIRONMENT CHECKS
#---------------------



#check for root
if [ $EUID -ne 0 ]; then
   /bin/echo "This script must be run as root" 1>&2
   exit 1
fi
echo "Good, you are root."

if [ ! -s $CONFFILE ]; then
	/bin/echo "This is either not an iso install, or an old one. exiting"
	/bin/echo
	exit 1
fi

grep "FreeSWITCH_SRC_INSTALLED=TRUE" $CONFFILE > /dev/null
if [ $? -eq 0 ]; then
	/bin/echo "This looks to be done already. exiting"
	/bin/echo
	exit 1
fi
	
#check for internet connection
/usr/bin/wget -q --tries=10 --timeout=5 http://www.google.com -O /tmp/index.google &> /dev/null
if [ ! -s /tmp/index.google ];then
	echo "No Internet connection. Exiting."
	/bin/rm /tmp/index.google
	exit 1
else
	echo "Internet connection is working, continuing!"
	/bin/rm /tmp/index.google
fi

#Check for new version
WHEREAMI=$(echo "`pwd`/`basename $0`")
wget $URLGETSCRIPT -O /tmp/getfs.sh
CURMD5=$(md5sum "$WHEREAMI" | sed -e "s/\ .*//")
echo "The md5sum of the current script is: $CURMD5"
NEWMD5=$(md5sum /tmp/getfs.sh | sed -e "s/\ .*//")
echo "The md5sum of the latest script is: $CURMD5"

if [[ "$CURMD5" == "$NEWMD5" ]]; then
	echo "files are the same, continuing"
else
	echo "There is a new version of this script."
	echo "  It is PROBABLY a good idea use the new version"
	echo "  the new file is saved in /tmp/getfs.sh"
	echo "  to see the difference, run:"
	echo "  diff -y /tmp/getfs.sh $WHEREAMI"
	read -p "Continue [y/N]? " YESNO
	case $YESNO in
		[Yy]*)
			echo "OK, Continuing"
		;;

		*)
			echo "OK, Stopping."
			exit 0
		;;
	esac
fi


INSFREESWITCH=1

#---------------------------------------
#       GIT/COMPILE    FREESWITCH
#---------------------------------------
if [ $INSFREESWITCH -eq 1 ]; then

	#------------------------
	# GIT FREESWITCH
	#------------------------
	/bin/grep 'git_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "Git Already Done. Skipping"	
	else
		cd /usr/src
		/usr/bin/time /usr/bin/git clone $REPO
		if [ $? -ne 0 ]; then
			#git had an error
			/bin/echo "GIT ERROR"
			exit 1
		else
			#switch repository
			/bin/echo "Switching to revision $GITVER"
			git checkout $GITVER
			/bin/echo "git_done" >> /tmp/install_fusion_status
		fi
		
	fi
	
	#------------------------
	# BOOTSTRAP FREESWITCH
	#------------------------
	/bin/grep 'bootstrap_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "Bootstrap already done. skipping"
	else
		#might see about -j option to bootstrap.sh
		/etc/init.d/ssh start
		cd /usr/src/freeswitch
		/bin/echo
		/bin/echo "FreeSWITCH Downloaded"
		/bin/echo 
		/bin/echo "Bootstrapping."
		/bin/echo
		#next line failed (couldn't find file) not sure why.
		#it did run fine a second time.  Go figure (really).
		#ldconfig culprit?
		if [ $CORES > "1" ]; then 
			/bin/echo "  multicore processor detected. Starting Bootstrap with -j"
			/usr/bin/time /usr/src/freeswitch/bootstrap.sh -j
		else 
			/bin/echo "  singlecore processor detected. Starting Bootstrap sans -j"
			/usr/bin/time /usr/src/freeswitch/bootstrap.sh
		fi

		if [ $? -ne 0 ]; then
			#bootstrap had an error
			/bin/echo "BOOTSTRAP ERROR"
			exit 1
		else
			/bin/echo "bootstrap_done" >> /tmp/install_fusion_status
		fi
	fi
	

	#------------------------
	# copy modules.conf 
	#------------------------
	/bin/grep 'build_modules' /tmp/install_fusion_status > /dev/null
	echo "Now copy modules.conf for freeswitch we used to build originally"
	cp $CONFFILE /usr/src/freeswitch/modules.conf
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: Failed to enable build modules in modules.conf."
			exit 1
		else
			/bin/echo "build_modules" >> /tmp/install_fusion_status
		fi

	
	#------------------------
	# CONFIGURE FREESWITCH 
	#------------------------
	/bin/grep 'config_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "FreeSWITCH already Configured! Skipping."
	else
		/bin/echo
		/bin/echo -ne "Configuring FreeSWITCH. This will take a while [~15 minutes]"
		/bin/sleep 1
		/bin/echo -ne " ."
		/bin/sleep 1
		/bin/echo -ne " ."
		/bin/sleep 1
		/bin/echo -ne " ."
		/usr/bin/time /usr/src/freeswitch/configure
		
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: FreeSWITCH Configure ERROR."
			exit 1
		else
			/bin/echo "config_done" >> /tmp/install_fusion_status
		fi
	fi
		
	#------------------------
	# COMPILE FREESWITCH 
	#------------------------	
	/bin/grep 'compile_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "FreeSWITCH already Compiled! Skipping."
	else
		#might see about -j cores option to make...
		
		/bin/echo
		/bin/echo -ne "Compiling FreeSWITCH. This might take a LONG while [~30 minutes]"
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo -ne "."
	
		#making sure pwd is correct
		cd /usr/src/freeswitch
		if [ $CORES -gt 1 ]; then 
			/bin/echo "  multicore processor detected. Compiling with -j $CORES"
			#per anthm compile the freeswitch core first, then the modules.
			/usr/bin/time /usr/bin/make -j $CORES core
			/usr/bin/time /usr/bin/make -j $CORES
		else 
			/bin/echo "  singlecore processor detected. Starting compile sans -j"
			/usr/bin/time /usr/bin/make 
		fi
		
	
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: FreeSWITCH Build Failure."
			exit 1
		else
			/bin/echo "compile_done" >> /tmp/install_fusion_status
		fi
	fi
	
	#------------------------
	# GIT HEAD FREESWITCH 
	#------------------------	
	/bin/grep 'head_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "FreeSWITCH already git Head! Skipping."
	else
		echo "change git repo back to head"
		git reset --hard HEAD
	
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: FreeSWITCH INSTALL Failure."
			exit 1
		else
			/bin/echo "head_done" >> /tmp/install_fusion_status
		fi
	fi	
	
	#------------------------
	# FREESWITCH  HD SOUNDS
	#------------------------	
	/bin/grep 'sounds_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "FreeSWITCH HD SOUNDS DONE! Skipping."
	else
		/bin/echo
		/bin/echo -ne "Installing FreeSWITCH HD sounds (16/8khz). This will take a while [~10 minutes]"
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo "."
		cd /usr/src/freeswitch
		if [ $CORES -gt 1 ]; then 
			/bin/echo "  multicore processor detected. Installing with -j $CORES"
			/usr/bin/time /usr/bin/make -j $CORES hd-sounds-install
		else 
			/bin/echo "  singlecore processor detected. Starting install sans -j"
			/usr/bin/time /usr/bin/make hd-sounds-install
		fi
		#/usr/bin/time /usr/bin/make hd-sounds-install
	
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: FreeSWITCH make cdsounds-install ERROR."
			exit 1
		else
			/bin/echo "sounds_done" >> /tmp/install_fusion_status
		fi
	fi
	
	#------------------------
	# FREESWITCH  MOH
	#------------------------	
	/bin/grep 'moh_done' /tmp/install_fusion_status > /dev/null
	if [ $? -eq 0 ]; then
		/bin/echo "FreeSWITCH MOH DONE! Skipping."
	else
		/bin/echo
		/bin/echo -ne "Installing FreeSWITCH HD Music On Hold sounds (16/8kHz). This will take a while [~10 minutes]"
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo -ne "."
		/bin/sleep 1
		/bin/echo "."
		
		cd /usr/src/freeswitch
		if [ $CORES -gt 1 ]; then 
			/bin/echo "  multicore processor detected. Installing with -j $CORES"
			/usr/bin/time /usr/bin/make -j $CORES hd-moh-install
		else 
			/bin/echo "  singlecore processor detected. Starting install sans -j"
			/usr/bin/time /usr/bin/make hd-moh-install
		fi
		#/usr/bin/make hd-moh-install
		
		if [ $? -ne 0 ]; then
			#previous had an error
			/bin/echo "ERROR: FreeSWITCH make cd-moh-install ERROR."
			exit 1
		else
			/bin/echo "moh_done" >> /tmp/install_fusion_status
		fi
	fi
	

	/bin/echo
	/bin/echo
	/bin/echo "FreeSWITCH Git/Compile Completed. Have Fun!"
	/bin/echo

fi

#---------------------------------------
#     DONE GIT/COMPILE FREESWITCH
#---------------------------------------
rm /tmp/install_fusion_status
echo "FreeSWITCH_SRC_INSTALLED=TRUE">>$CONFFILE
exit 0