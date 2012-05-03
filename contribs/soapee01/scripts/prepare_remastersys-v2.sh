#!/bin/bash
#prepare_remastersys

#This is ugly ugly ugly.  I never wanted to commit this really.
#it's here so that in case I get hit by a truck, the project can
#continue to build iso's.  Use at your own risk!

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

#---------
#  NOTES
#---------
#Prepares the system for remastersys.
#	skel.tar.gz -> Sets up the background image.  Important is the 
#     custom .profile which finishes the install, and cleans up loose ends
#   remastersys_conf.tar.gz -> configuration files (and boot splash)
#     for remastersys.  The boot splash is also our default desktop background
#     in skel.


#---------
#CHANGELOG
#---------
VERSION="Version 2 - 2011 January 27. WAF License"
DISTRO="precise"
PROFILERUN="/etc/skel/.profile"
URLGETSCRIPT="http://fusionpbx.googlecode.com/svn/contribs/soapee01/scripts/getfs.sh"

#---------
#VARIABLES
#---------

case $1 in
	start)
		/bin/echo "Here we go!"
	;;

	version)
		/bin/echo "  "$VERSION
		/bin/echo
		exit 0
	;;
	
	-v)
		/bin/echo "  "$VERSION
		/bin/echo
		exit 0
	;;
	
	--version)
		/bin/echo "  "$VERSION
		/bin/echo
		exit 0
	;;
	
	*)
		/bin/echo
		/bin/echo "This script should be called as:"
		/bin/echo "  prepare_remastersys start|-v|--version|version"
		/bin/echo
		/bin/echo "This script prepares an the install for remastersys"
		/bin/echo "  It should configure everything needed to create"
		/bin/echo "  an iso image for Ubuntu 10.04/FreeSWITCH/FusionPBX"
		/bin/echo "  If you have not installed FreeSWITCH or FusionPBX"
		/bin/echo "  please do so now.  See install_fusionpbx project"
		/bin/echo "  on sourceforge."
		/bin/echo 
		/bin/echo "      EXAMPLE"
		/bin/echo "         prepare_remastersys start"
		/bin/echo 
		exit 0
	;;
esac



# /usr/bin/make sure only root can run our script
if [ $EUID -ne 0 ]; then
   /bin/echo "This script must be run as root" 1>&2
   exit 1
fi
echo "Good, you are root."

#/bin/grep -i lucid /etc/lsb-release > /dev/null
lsb_release -c |grep -i lucid > /dev/null
if [ $? -eq 0 ]; then
	/bin/echo "Good, you're running Ubuntu 10.04 LTS codename Lucid"
	/bin/echo
else
	lsb_release -c |grep -i squeeze > /dev/null
	if [ $? -eq 0 ]; then
		DISTRO=squeeze
		/bin/echo "OK you're running Debian Squeeze.  This script is known to work"
		/bin/echo "   with apache/nginx and mysql|sqlite|postgres8 options"
		/bin/echo "   Please consider providing feedback on repositories for nginx"
		/bin/echo "   and php-fpm."
		/bin/echo 
		CONTINUE=YES
	else
		/bin/echo 
		/bin/echo "This script was written for Ubuntu 10.04 LTS codename Lucid"
		/bin/echo
		/bin/echo "Your OS appears to be:"
		/bin/cat /etc/lsb-release
		read -p "Do you want to continue [y|n]? " CONTINUE
		case "$CONTINUE" in
		  [yY]*)
		      /bin/echo "This is completely untested. Good Luck!"
		      /bin/echo "Please let us know if it works."
		  ;;
		  *)
		      /bin/echo "OK. Quitting"
		      exit 1
		  ;;
		esac
	fi
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


if [ $DISTRO = "squeeze" ]; then
	echo "add remastersys for deb to sources.list.d"
	echo "deb http://www.geekconnection.org/remastersys/repository squeeze/" > /etc/apt/sources.list.d/remastersys.list
	PROFILERUN="/etc/skel/.profile"
	
elif [ $DISTRO = "precise" ]; then
	echo "add remasterys repo for precise"
	wget -O - http://www.remastersys.com/ubuntu/remastersys.gpg.key | apt-key add -
	echo "deb http://www.remastersys.com/ubuntu precise main" > /etc/apt/sources.list.d/remastersys.list
	echo "we will use .bash_profile instead of .profile"
	PROFILERUN="/etc/skel/.bash_profile"
	
else
#	/bin/grep remastersys /etc/apt/sources.list > /dev/null
#	if [ $? -ne 0 ]; then
#		#add the following 
		/bin/echo "add remastersys to sources"
#		/bin/echo "#for remastersys" >> /etc/apt/sources.list
		/bin/echo "deb http://www.geekconnection.org/remastersys/repository lucid/" >> /etc/apt/sources.list.d/remastersys.list
		PROFILERUN="/etc/skel/.profile"
#	else
#		/bin/echo "Remastersys already added to sources.list"
#	fi
fi
/usr/bin/apt-get update
#remastersys unauthenticated --force-yes

if [ $DISTRO = "precise" ]; then
	echo "PRECISE: getting remastersys remastersys-gui xinit lxde-core midori"
	/usr/bin/apt-get -y install remastersys remastersys-gui xinit lxde-core midori
	#for gtk-3 themes (can't read ubiquity install without)
	/usr/bin/apt-get -y install lxappearance shimmer-themes

else
	/usr/bin/apt-get -y --force-yes install remastersys xinit
fi


/bin/rm /usr/local/bin/motd_fusionpbx
#else
	/bin/echo "create motd"
if [ $DISTRO = "squeeze" ]; then
	MOTDFILE="/etc/motd.tail"
	/bin/cat >> /etc/motd.tail <<'DELIM'
Thank you for trying FusionPBX and FreeSWITCH
Help: IRC #fusionpbx on FreeNode
      www.fusionpbx.com

The FreeSWITCH src was left off the iso to save space.
Get the compiled Source code by running:
	/usr/local/bin/getfs
	
You can then easily upgrade FreeSWITCH by running
  sudo install_fusionpbx upgrade-freeswitch user

Upgrade FusionPBX
  sudo install_fusionpbx upgrade-fusionpbx user

DELIM
else
	/bin/cat > /usr/local/bin/motd_fusionpbx <<'DELIM'
#!/bin/bash
# motd_fusionpbx

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

/bin/echo
/bin/echo "Thank you for trying FusionPBX and FreeSWITCH"
/bin/echo "Help: IRC #fusionpbx on FreeNode"
/bin/echo "      www.fusionpbx.com"
/bin/echo "The FreeSWITCH src was left off to save space. "
/bin/echo "Git/build the latest by running"
/bin/echo "  /usr/local/bin/getfs"
/bin/echo
/bin/echo "Then you can upgrade freeswitch as such:"
/bin/echo "  sudo install_fusionpbx upgrade-freeswitch user"
/bin/echo
/bin/echo "Upgrade FusionPBX"
/bin/echo "  sudo install_fusionpbx upgrade-fusionpbx user"
/bin/echo
#/bin/echo "flattr FusionPBX: http://flattr.com/thing/89152/FusionPBX"
#/bin/echo "Donate to FreeSWITCH: http://bit.ly/donate_freeswitch"
#/bin/echo
DELIM
fi
if [ $DISTRO != "squeeze" ]; then
	/bin/chmod 755 /usr/local/bin/motd_fusionpbx
	/bin/rm /etc/update-motd.d/99-z-motd-fusionpbx
	/bin/ln -s /usr/local/bin/motd_fusionpbx /etc/update-motd.d/99-z-motd-fusionpbx
	/bin/echo "motd linked"
fi
#midori 26.5MB with javascript...
/usr/bin/apt-get -y install midori

#------------------
# instructions.html
#------------------
/bin/cat > /var/www/fusionpbx/instructions.html <<'DELIM'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=windows-1252">
	<TITLE></TITLE>
	<STYLE TYPE="text/css">
	<!--
		@page { margin: 0.79in }
		P { margin-bottom: 0.08in }
		H3 { margin-bottom: 0.08in }
		A:link { so-language: zxx }
	-->
	</STYLE>
</HEAD>
<BODY LANG="en-US" DIR="LTR">
<P STYLE="margin-bottom: 0in">Thank you for trying FreeSWITCH and
FusionPBX</P>
<P STYLE="margin-bottom: 0in">This is a 3rd party iso. It was not created by the fine folks at FusionBPX or FreeSWITCH (they are not responsible).</P>
<P STYLE="margin-bottom: 0in">No warranty of any kind is expressed or implied. Use at your own risk.</P>
<H3><A NAME="Instructions"></A>Instructions</H3>
<OL>
	<LI><P STYLE="margin-bottom: 0in">CONFIGURE YOUR NETWORK!</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">Edit /etc/network/interfaces</P>
		<UL>
			<LI><P STYLE="margin-bottom: 0in">Applications &rarr; System Tools
			will get you to a Root Terminal.</P>
		</UL>
		<LI><P STYLE="margin-bottom: 0in">It should currently be set to
		dhcp.</P>
	</UL>
	<LI><P STYLE="margin-bottom: 0in">Read the <A HREF="http://wiki.fusionpbx.com/index.php/Easy_FusionPBX#Errata">Errata
	List</A> 
	</P>
	<LI><P STYLE="margin-bottom: 0in">Double Click the install icon 
	</P>
	<LI><P STYLE="margin-bottom: 0in">Post-install: decide whether or
	not you want to upgrade FusionBPX. 
	</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">The system will not boot with a
		GUI. If you need one, run startx 
		</P>
	</UL>
	<LI><P STYLE="margin-bottom: 0in">Default Username/Password for
	FusionPBX on LiveCD 
	</P>
	<UL>
		<LI><P STYLE="margin-bottom: 0in">username: superadmin 
		</P>
		<LI><P>password: fusionpbx 
		</P>
	</UL>
	<LI><P><A HREF="http://localhost/">Check out FusionPBX on this LiveCD right now!</A></P>
</OL>
<P STYLE="margin-bottom: 0in"><BR>
</P>
</BODY>
</HTML>
DELIM

#------------------
# livecd check
#------------------

mkdir /pub
chmod 777 /pub
#set a cookie...
/bin/echo "FIRST TIME RUN. .profile should delete this." > /pub/fresh_fusion_install
/bin/echo "cookie set in /pub/fresh_fusion_install"
chmod 666 /pub/fresh_fusion_install


#------------------
# .profile
#------------------
#set up /etc/skel/.profile
#/bin/grep fresh_fusion_install /etc/skel/.profile > /dev/null
#if [ $? -ne 0 ]; then
/bin/rm $PROFILERUN
	#Put DELIM in ' ' to prevent `tty..` command from substituting "here document"
/bin/cat > $PROFILERUN <<'DELIM'
# ~/.profile: executed by the command interpreter for login shells.
# This file is not read by bash(1), if ~/.bash_profile or ~/.bash_login
# exists.
# see /usr/share/doc/bash/examples/startup-files for examples.
# the files are located in the bash-doc package.

# the default umask is set in /etc/profile; for setting the umask
# for ssh logins, install and configure the libpam-umask package.
#umask 022

# if running bash
if [ -n "$BASH_VERSION" ]; then
    # include .bashrc if it exists
    if [ -f "$HOME/.bashrc" ]; then
        . "$HOME/.bashrc"
    fi
fi

# set PATH so it includes user's private bin if it exists
if [ -d "$HOME/bin" ] ; then
    PATH="$HOME/bin:$PATH"
fi
	
#added to finish livecd install
if [ -a /rofs ]; then
	/bin/echo "running livecd"
	#remastersys runs on tty[1-6]
	TTYNUM=`/usr/bin/tty | /bin/sed -e "s:/dev/tty::"`
	if [ $TTYNUM -eq 1 ]; then
		#get rid of gnome error
		#gconftool-2 --recursive-unset /apps/panel
		#/usr/bin/gconftool-2 --direct --config-source xml:readwrite:/etc/gconf/gconf.xml.defaults --set --type list --list-type string /apps/panel/global/disabled_applets "[OAFIID:GNOME_FastUserSwitchApplet]"
		#/usr/bin/gconftool-2 -t string -s /desktop/gnome/background/picture_filename /etc/remastersys/isolinux/splash.png
		/usr/bin/startx -- :0
	fi

else

	#if [ -a /etc/fresh_fusion_install ]; then 
	if [ -a /pub/fresh_fusion_install ]; then
		#check for internet connection
		/usr/bin/wget -q --tries=10 --timeout=5 http://www.google.com -O /tmp/index.google &> /dev/null
		if [ ! -s /tmp/index.google ];then
			echo "No Internet connection. Exiting. Please fix that, log out, and log back in"
			/bin/rm /tmp/index.google
		#exit 1 #this causes auto-logout. full loop with no internet. not good.
		else
			/bin/rm /pub/fresh_fusion_install
			echo "Internet connection is working, continuing!"
			/bin/rm /tmp/index.google
			/bin/echo "This appears to be the first login after install."
			/bin/echo "The fine folks at FusionPBX would prefer you to"
			/bin/echo "  use the latest version. I can upgrade you "
			/bin/echo "  automatically.  THIS WILL COMPLETELY REMOVE"
			/bin/echo "  FUSIONPBX AND ANY CUSTOMIZATION YOU MAY HAVE"
			/bin/echo "  DONE.  This is not a big deal if you just "
			/bin/echo "  installed from the ubuntu ISO :)"
			/bin/echo
			read -p "  Can I upgrade now (y/n)? " YESNO
		
			case "$YESNO" in
				[Yy]*)
					/bin/echo "first let's get latest install script. We'll need sudo password"
					/usr/bin/sudo /bin/rm /usr/local/bin/install_fusionpbx*
					/usr/bin/sudo svn checkout https://fusionpbx.googlecode.com/svn/trunk/scripts/install/ubuntu/ /usr/src/install_fusionpbx
					/usr/bin/sudo chmod 755 /usr/src/install_fusionpbx/install_fusionpbx.sh
					/usr/bin/sudo ln -s /usr/src/install_fusionpbx/install_fusionpbx.sh /usr/local/bin/
					/bin/echo "removing old directory."
					/usr/bin/sudo /bin/rm -R /var/www/fusionpbx
					/bin/echo "running: sudo /usr/local/bin/install_fusionpbx install-fusionpbx user"
					/usr/bin/sudo cd /usr/src/ubuntu/
					/usr/bin/sudo /usr/bin/svn update
					/usr/bin/sudo chmod 755 /usr/src/ubuntu/install_fusionpbx.sh
					/usr/bin/sudo /usr/local/bin/install_fusionpbx.sh install-fusionpbx user
			
			
					/usr/bin/sudo /bin/rm /etc/fresh_fusion_install
				;;
			
				*)
					/bin/echo "OK. run install_fusionpbx.sh for upgrade options..."
					/bin/echo "  You should also upgrade that script from svn (see wiki)"
					/usr/bin/sudo /bin/rm /etc/fresh_fusion_install
					/bin/echo "Thank you for Choosing FusionPBX"
				;;
			esac
			
			/bin/echo "FreeSWITCH source was left off of this iso to save space!"
			/bin/echo "  IT takes some time to download and compile it, but it will"
			/bin/echo "  leave you with an upgradable system if we do it."
			/bin/echo "  If you say yes (recommended),"
			/bin/echo "    THEN I WILL COMPILE THE VERSION"
			/bin/echo "    CURRENTLY INSTALLED, and SWITCH BACK to LATEST."
			/bin/echo "    Then you can run the script to upgrade freeswitch"
			/bin/echo "    as normal. You can also run this later by calling:"
			/bin/echo "      /usr/local/bin/getfs"
			read -p "CONTINUE [Y/n]? " YESNO
			
			case "$YESNO" in
				[Yy]*)
						/bin/echo "Great! Getting FreeSWITCH source now."
						/usr/local/bin/getfs
				;;
				
				*)
						/bin/echo "OK, get FreeSWITCH later with fsget."
				;;
			esac
	
			if [ -a /etc/skel/.sudo_as_admin_successful ]; then
				#don't want everyone with sudo at account creation...
				/bin/echo "Removing /etc/skel/.sudo_as_admin_successful"
				/usr/bin/sudo /bin/rm /etc/skel/.sudo_as_admin_successful
			fi
		
			if [ ! -e /etc/ssh/ssh_host_dsa_key.pub ]; then
				/bin/echo "OpenSSH Keys not saved during ISO build (for good reason)"
				/bin/echo "Regenerating..."
				/usr/bin/sudo /usr/sbin/dpkg-reconfigure openssh-server
			fi
		fi
	else
		/bin/echo "Thank you for Choosing FusionPBX"
	fi
fi
DELIM
#fi


#overwrite remastersys with our settings...

#set up boot splash
if [ -a /etc/remastersys/isolinux/isolinux.cfg.vesamenu.orig ]; then
	/bin/rm /etc/remastersys/isolinux/isolinux.cfg.vesamenu
else
	/bin/mv /etc/remastersys/isolinux/isolinux.cfg.vesamenu /etc/remastersys/isolinux/isolinux.cfg.vesamenu.orig
fi
/bin/cat > /etc/remastersys/isolinux/isolinux.cfg.vesamenu <<DELIM
default vesamenu.c32
prompt 0
timeout 300

menu title __LIVECDLABEL__
menu background splash.png
menu vshift 5
menu color title 1;37;44 #c0000000 #00ffffff std

label live
  menu label live - boot the Live System
  kernel /casper/vmlinuz
  append  file=/cdrom/preseed/custom.seed boot=casper initrd=/casper/initrd.gz quiet splash --

label xforcevesa
  menu label xforcevesa - boot Live in safe graphics mode
  kernel /casper/vmlinuz
  append  file=/cdrom/preseed/custom.seed boot=casper xforcevesa initrd=/casper/initrd.gz quiet splash --

label install
  menu label install - start the installer directly
  kernel /casper/vmlinuz
  append  file=/cdrom/preseed/custom.seed boot=casper only-ubiquity initrd=/casper/initrd.gz quiet splash --

label textonly
  menu label textonly - boot Live in textonly mode
  kernel /casper/vmlinuz
  append  file=/cdrom/preseed/custom.seed boot=casper textonly initrd=/casper/initrd.gz quiet --

label debug
  menu label debug - boot the Live System without splash and show boot info
  kernel /casper/vmlinuz
  append  file=/cdrom/preseed/custom.seed boot=casper initrd=/casper/initrd.gz nosplash --

label memtest
  menu label memtest - Run memtest
  kernel /isolinux/memtest
  append -

label hd
  menu label hd - boot the first hard disk
  localboot 0x80
  append -
DELIM

#get the boot splash
cd /etc/remastersys/isolinux/
if [ -a /etc/remastersys/isolinux/splash.png.orig ]; then
	/bin/rm /etc/remastersys/isolinux/splash.png
else
	/bin/mv /etc/remastersys/isolinux/splash.png /etc/remastersys/isolinux/splash.png.orig
fi
if [ $DISTRO = "squeeze" ]; then
	wget http://sourceforge.net/projects/fusionpbxinstal/files/img/splash-screen-debian.png -O splash.png
elif [ $DISTRO = "precise" ]; then
	wget http://stubbornroses.com/splash-screen-ubuntu12.png -O splash.png	
else
	wget http://sourceforge.net/projects/fusionpbxinstal/files/img/splash-screen-ubuntu.png -O splash.png
fi
#set up gconf skel now...
#this sets the background image for the desktop
/bin/mkdir -p /etc/skel/.gconf/desktop/gnome/background
/bin/chmod -R 700 /etc/skel/.gconf
/bin/rm /etc/skel/.gconf/desktop/gnome/background/%gconf.xml
/bin/cat > /etc/skel/.gconf/desktop/gnome/background/%gconf.xml <<DELIM
<?xml version="1.0"?>
<gconf>
	<entry name="primary_color" mtime="1292532310" type="string">
		<stringvalue>#0b4f3f81f3e4</stringvalue>
	</entry>
	<entry name="secondary_color" mtime="1292532310" type="string">
		<stringvalue>#7b0531ba2a1f</stringvalue>
	</entry>
	<entry name="picture_filename" mtime="1292532259" type="string">
		<stringvalue>/etc/remastersys/isolinux/splash.png</stringvalue>
	</entry>
	<entry name="color_shading_type" mtime="1292532319" type="string">
		<stringvalue>vertical-gradient</stringvalue>
	</entry>
	<entry name="picture_options" mtime="1292532259" type="string">
		<stringvalue>centered</stringvalue>
	</entry>
</gconf>
DELIM

#start midori browser in gnome
#/bin/mkdir -p /etc/skel/.config/autostart
#/bin/chmod -R 755 /etc/skel/.config
#/bin/rm /etc/skel/.config/autostart/midori.desktop
#/bin/cat > /etc/skel/.config/autostart/midori.desktop <<'DELIM'
#[Desktop Entry]
#Type=Application
#Exec=/usr/bin/midori http://localhost
#Hidden=false
#NoDisplay=false
#X-GNOME-Autostart-enabled=true
#Name[en_US]=midori
#Name=midori
#Comment[en_US]=For FusionLiveCD
#Comment=For FusionLiveCD
#DELIM

#set up /etc/remastersys.conf
#LIVECDLABEL is max 32 characters.
if [ $DISTRO = "squeeze" ]; then
        #32 bit or 64 bit
        /bin/uname -a | /bin/grep x86_64 > /dev/null
        if [ $? -eq 0 ]; then
			ISONAME="fusionpbx_deb_x86_64-beta-`date +%F`"
                /bin/echo "64 bit machine"
                /bin/sed -i /etc/remastersys.conf \
                        -e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
                        -e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX Debian Squeeze x86_64"':g \
                        -e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
                        -e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g
        else
			ISONAME="fusionpbx_deb_i386-beta-`date +%F`"
                /bin/echo "32 bit machine"
                /bin/sed -i /etc/remastersys.conf \
                        -e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
                        -e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX Debian SQUEEZE i386"':g \
                        -e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
                        -e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g
        fi
elif [ $DISTRO = "precise" ]; then
        #32 bit or 64 bit
        /bin/uname -a | /bin/grep x86_64 > /dev/null
        if [ $? -eq 0 ]; then
			ISONAME="fusionpbx_ub_1204_x86_64-`date +%F`"
                /bin/echo "64 bit machine"
                /bin/sed -i /etc/remastersys.conf \
                        -e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
                        -e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX-Ubuntu 12.04LTS x86_64"':g \
                        -e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
                        -e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g
				#customiso = fusionpbx_ub_12.04_x86_64-beta-2012-04-26.iso ---- genisoimage volume id string too long.
        else
			ISONAME="fusionpbx_ub_1204_i386-`date +%F`"
                /bin/echo "32 bit machine"
                /bin/sed -i /etc/remastersys.conf \
                        -e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
                        -e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX-Ubuntu 12.04LTS i386"':g \
                        -e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
                        -e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g
        fi
		
else
	#32 bit or 64 bit
	/bin/uname -a | /bin/grep x86_64 > /dev/null
	if [ $? -eq 0 ]; then
		ISONAME="fusionpbx_ub_x86_64-beta-`date +%F`"
		/bin/echo "64 bit machine"
		/bin/sed -i /etc/remastersys.conf \
			-e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
			-e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX-Ubuntu 10.04LTS x86_64"':g \
			-e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
			-e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g 
	else	
		ISONAME="fusionpbx_ub_i386-beta-`date +%F`"
		/bin/echo "32 bit machine"
		/bin/sed -i /etc/remastersys.conf \
			-e s:^LIVEUSER=.*$:'LIVEUSER="fusionpbx"':g \
			-e s:^LIVECDLABEL=.*$:'LIVECDLABEL="FusionPBX-Ubuntu 10.04LTS i386"':g \
			-e s:^CUSTOMISO=.*$:CUSTOMISO=\"$ISONAME.iso\":g \
			-e s,^LIVECDURL=.*$,'LIVECDURL="http://www.fusionpbx.com"',g 
	fi
fi

#FOR getfs
echo "ISONAME=$ISONAME" > /etc/fusion_iso.conf
cd /usr/src/freeswitch
FSGITVER=$(git log --pretty=oneline | head -n 1 | sed -e "s/\ .*//")
echo "FSGITVER=$FSGITVER" >> /etc/fusion_iso.conf
cp /usr/src/freeswitch/modules.conf /etc/freeswitch_iso_modules.conf

wget $URLGETSCRIPT -O /usr/local/bin/getfs
chmod 755 /usr/local/bin/getfs
	
/bin/echo "/usr/src/freeswitch is a very large directory."
/bin/echo "It will fit on an CDROM. if you exclude it"
/bin/echo "If include it, this will be a DVD sized iso."
read -p "Do you want to include /usr/src/freeswitch? (y/N)? " YESNO

case "$YESNO" in
  [yY]*)
	/bin/echo "Done. This will be one honking iso."
	/bin/sed -i /etc/remastersys.conf \
		-e s:^EXCLUDES=.*$:EXCLUDES="":g	
  ;;
  *)
	/bin/echo "excluding /usr/src/freeswitch to save space"
	/bin/sed -i /etc/remastersys.conf \
		-e s:^EXCLUDES=.*$:'EXCLUDES="/usr/src/freeswitch"':g
  ;;
esac

/bin/echo
/bin/echo "Setting up install_fusionpbx.sh as an svn pull"
/bin/echo "  so we can upgrade the script post iso install,"
/bin/echo "  before we pull down the latest fusionpbx."

/bin/rm /usr/local/bin/install_fusionpbx*
svn checkout https://fusionpbx.googlecode.com/svn/trunk/scripts/install/ubuntu/ /usr/src/install_fusionpbx
chmod 755 /usr/src/install_fusionpbx/install_fusionpbx.sh
ln -s /usr/src/install_fusionpbx/install_fusionpbx.sh /usr/local/bin/

/bin/echo
/bin/echo

/bin/echo
/bin/echo
/bin/echo

#get rid of error
#The panel encountered a problem while loading
# "OAFIID:GNOME_FastUserSwitchApplet".
# Do you want to delete the applet from your configuration?
#
# ~/.gconf/apps/panel/applets/fast_user_switch_screen0/%gconf.xml
# looks much more difficult to do. I hate gnome...

# Fix FUSA not loading (defaults): gconftool-2 --direct --config-source xml:readwrite:/etc/gconf/gconf.xml.defaults --set --type list --list-type string /apps/panel/global/disabled_applets "[OAFIID:GNOME_FastUserSwitchApplet]"
# may need to do this in .profile first run and post/install.
#trying there.

#that sort of worked. kept losing start panel. got rid of error, and got proper desktop background.
#might be best to just copy the directories to skel.
/bin/echo "I hope you set up gnome like you like it"
/bin/echo "You'll need to autostart midori to"
/bin/echo "/usr/bin/midori file:///var/www/fusionpbx/instructions.html"
/bin/echo "   if this is precise, vim ~/.config/autostart/midori.desktop [you may need mkdir ~/.config/autostart]"
/bin/echo "          Then add the following to the file"
/bin/echo "          [Desktop Entry]"
/bin/echo "          Type=Application"
/bin/echo "          Exec=midori file:///var/www/fusionpbx/instructions.html"
/bin/echo
/bin/echo "set the desktop background"
/bin/echo
/bin/echo "get rid of the fastuserswitching error"
/bin/echo "I will wait while you do those things"
/bin/echo "then I will copy the gnome settings to skel."
/bin/echo "desktop Colors"
/bin/echo "color name"
/bin/echo  "65795D"
/bin/echo  "hue 104"
/bin/echo  "saturation 23"
/bin/echo  "value 47"
/bin/echo  "red 101"
/bin/echo  "Green 121"
/bin/echo  "blue 93"
/bin/echo 
/bin/echo "For precise, run lxappearance, and select the"
/bin/echo "  Greybird theme (/usr/share/themes/Greybird)"
/bin/echo "  I will set the symlink when you come back"
/bin/echo "    IF YOU FORGET TO DO THIS UBIQUITY IS BLACK"
/bin/echo "    AND ILLEGIBLE ON THE INSTALL"
/bin/echo 
#/bin/echo "DO NOT REBOOT, or you will need to recreate /etc/ubiquity_fusion_install"
read

#this worked once, still might consider a hard copy.
if [ $DISTRO = "precise" ]; then
	/bin/echo "setting symlink"
	su fusionpbx -c "ln -s /usr/share/themes/Greybird/gtk-3.0 ~/.config/gtk-3.0"
fi


#copy the latest .gconf .gnome2 directories to skel
#no, let's just build what we need from this script
/bin/cp -R /home/fusionpbx/.gconf /etc/skel/
/bin/cp -R /home/fusionpbx/.gnome2 /etc/skel/
/bin/cp -R /home/fusionpbx/.config /etc/skel/
/bin/cp -R /home/fusionpbx/.local /etc/skel/

#now remove some stuff from skel that we don't want
/bin/rm -R /etc/skel/.local/share/gvfs-metadata
#nothing needed for .gconf
/bin/rm -R /etc/skel/.gnome2/keyrings
/bin/rm /etc/skel/.config/midori/history.db
/bin/rm /etc/skel/.config/midori/session.xbel

if [ -f /etc/skel/.profile ]; then
	echo "/etc/skel/.profile exists"
else
	echo "/etc/skel/.profile MISSING"
fi
	
if [ -f /etc/skel/.bash_profile ]; then
	echo "/etc/skel/.bash_profile exists"
else
	echo "/etc/skel/.bash_profile MISSING"
fi


/bin/echo "clearing apt archives to save space."
/usr/bin/apt-get clean

/bin/echo
/bin/echo "You will now need to run startx"
/bin/echo "open an xterm, and run remastersys clean"
/bin/echo "remastersys dist"
/bin/echo "done."

exit 0

#if all of the iso prep goes well, but the geniso fails, try this to save time.
##!/bin/bash
#CREATEISO="/usr/bin/genisoimage"
#LIVECDLABEL="FusionPBX-Ubuntu 12.04LTS x86_64"
#WORKDIR="/home/remastersys/remastersys"
#CUSTOMISO="fusion.iso"
#$CREATEISO -iso-level 3 -r -V "$LIVECDLABEL" -cache-inodes -J -l -b isolinux/isolinux.bin -c isolinux/boot.cat -no-emul-boot -boot-load-size 4 -boot-info-table -o $WORKDIR/$CUSTOMISO "$WORKDIR/ISOTMP"

#stuff installed by remastersys.
remove
popularity-contest ubuntu-standard

add
fvwm1 gksu gnome-keyring libffi5 libgcr0 libgksu2-0 libgnome-keyring0
  libgp11-0 libgtop2-7 libgtop2-common libpam-gnome-keyring python-cairo
  python-dbus python-gobject python-gtk2
  
add2
alacarte app-install-data capplets-data desktop-file-utils docbook-xml
  esound-clients esound-common evolution-data-server
  evolution-data-server-common gamin ghostscript gnome-about gnome-applets
  gnome-applets-data gnome-control-center gnome-desktop-data gnome-doc-utils
  gnome-media gnome-media-common gnome-menus gnome-mime-data gnome-panel
  gnome-panel-data gnome-session gnome-session-bin gnome-settings-daemon
  gnome-system-monitor gnome-user-guide gsfonts gstreamer0.10-plugins-base
  gstreamer0.10-plugins-good gstreamer0.10-pulseaudio gstreamer0.10-x gvfs
  gvfs-backends indicator-applet indicator-application indicator-messages
  indicator-sound launchpad-integration libaa1 libappindicator0 libarchive1
  libart-2.0-2 libasound2 libasound2-plugins libatasmart4 libatspi1.0-0
  libaudiofile0 libavahi-glib1 libavc1394-0 libbluetooth3 libbonobo2-0
  libbonobo2-common libbonoboui2-0 libbonoboui2-common libcaca0
  libcairomm-1.0-1 libcamel1.2-14 libcanberra-gtk-module libcanberra-gtk0
  libcanberra0 libcdio-cdda0 libcdio-paranoia0 libcdio10 libcdparanoia0
  libcupsimage2 libdbusmenu-glib1 libdbusmenu-gtk1 libdevkit-power-gobject1
  libdv4 libebackend1.2-0 libebook1.2-9 libecal1.2-7 libedata-book1.2-2
  libedata-cal1.2-6 libedataserver1.2-11 libedataserverui1.2-8
  libegroupwise1.2-13 libesd0 libexempi3 libexif12 libflac8 libgamin0
  libgdata-google1.2-1 libgdata1.2-1 libgdu0 libglib2.0-data
  libglibmm-2.4-1c2a libglu1-mesa libgnome-desktop-2-17 libgnome-media0
  libgnome-menu2 libgnome-window-settings1 libgnome2-0 libgnome2-common
  libgnomecanvas2-0 libgnomecanvas2-common libgnomekbd-common libgnomekbd4
  libgnomeui-0 libgnomeui-common libgnomevfs2-0 libgnomevfs2-common
  libgphoto2-2 libgphoto2-port0 libgs8 libgtkmm-2.4-1c2a libgucharmap7
  libgudev-1.0-0 libgvfscommon0 libgweather-common libgweather1
  libhal-storage1 libhal1 libical0 libido-0.1-0 libiec61883-0
  libimobiledevice0 libindicate4 libindicator0 libjack0 libjson-glib-1.0-0
  liblaunchpad-integration1 liblcms1 libmagickcore2 libmagickwand2
  libmetacity-private0 libnautilus-extension1 libogg0 liboil0.3 libopenobex1
  libpanel-applet2-0 libpangomm-1.4-1 libpaper-utils libpaper1 libplist1
  libpolkit-agent-1-0 libpolkit-backend-1-0 libproxy0 libpulse-browse0
  libpulse-mainloop-glib0 libpulse0 librarian0 libraw1394-11 libsamplerate0
  libsgutils2-2 libshout3 libsmbclient libsndfile1 libsoup-gnome2.4-1
  libspeex1 libspeexdsp1 libtag1-vanilla libtag1c2a libtalloc2 libtdb1
  libtheora0 libusb-1.0-0 libusbmuxd1 libv4l-0 libvisual-0.4-0
  libvisual-0.4-plugins libvorbis0a libvorbisenc2 libvorbisfile3 libwavpack1
  libwbclient0 libxklavier16 libxml2-utils libxss1 libxxf86misc1
  metacity-common mousetweaks nautilus nautilus-data obex-data-server
  policykit-1 policykit-1-gnome psfontmgr pulseaudio pulseaudio-esound-compat
  pulseaudio-module-x11 pulseaudio-utils python-gconf python-gmenu
  python-gnome2 python-gnomeapplet python-gnomecanvas python-libxml2
  python-pyorbit python-xdg python-xkit rarian-compat rtkit
  screen-resolution-extra scrollkeeper sgml-data ubuntu-system-service udisks
  usbmuxd xsltproc xulrunner-1.9.2 yelp zenity
  
  apt-cache show install ubiquity-frontend-gtk
  Depends: python (>= 2.6), python-central (>= 0.6.11), ubiquity (= 2.2.25), python-dbus, python-gtk2 (>= 2.17.0-0ubuntu2), iso-codes, x-window-manager, gksu
  
  #color name
  #65795D
  #hue 104
  #saturation 23
  #value 47
  #red 101
  #Green 121
  #blue 93
