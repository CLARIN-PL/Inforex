%define _unpackaged_files_terminate_build 1
%define __brp_mangle_shebangs  /usr/bin/true

%define        httpduser         apache
%define        httpdgroup        apache
%define        httpconfdir       /etc/httpd
%define        phplibdir         /var/www/html
%define        inforexdir        %{phplibdir}/%{name}

Name:       inforex
Version:    VERSION_TAG
Release:    RELEASE_TAG
Summary:    dummy package to testing rpm builds
License:    GLGPL v3
Group:      Productivity/Networking/Web/Frontends
URL:        https://github.com/CLARIN-PL/Inforex

Source0:	%{name}.tgz
Source1:        %{name}.httpd.conf

BuildRequires:  httpd-devel
BuildRequires:  httpd
BuildRequires:  php-devel

Requires:       httpd
Requires:       mysql

Buildarch: noarch

%global debug_package %{nil}

%description
This skeleton is intentionaly developed as absolute minimal specification
to test all phases of building rpm process

%prep
%setup -q -c

# remove precompiled templtates from devel dir
rm -rf engine/templates_c/*

%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir $RPM_BUILD_ROOT
install -m 755 -d %{buildroot}%{inforexdir}
# data dir may not exists
mkdir %{buildroot}%{inforexdir}/data
# copy all files
cp -r . %{buildroot}%{inforexdir}

# move data directory to /var/lib/ and set symlink
install -m 755 -d %{buildroot}%{_localstatedir}/lib/
mv %{buildroot}%{inforexdir}/data %{buildroot}%{_localstatedir}/lib/%{name}
ln -s %{_localstatedir}/lib/%{name} %{buildroot}%{inforexdir}/data

# install templates_c empty workdir for Smarty cache
install -m 775 -d %{buildroot}%{inforexdir}/engine/templates_c

# create one initial sql dump from all patches
rm -rf %{buildroot}/%{inforexdir}/database
mkdir -p %{buildroot}/%{inforexdir}/database/init
SQL_DUMP_FILE=%{buildroot}%{inforexdir}/database/init/initDB.sql
cat ./database/init/inforex-v1.0.sql |grep -v "50013 DEFINER=" > $SQL_DUMP_FILE
cat ./database/inforex-v1.0-changelog.sql |sed -e 's/endDelimiter:#/\nDELIMITER #/' | sed -e 's/^#/#\nDELIMITER ;/' >> $SQL_DUMP_FILE

# install (empty) configuration-file
touch %{buildroot}%{_localstatedir}/lib/%{name}/config.ini.php

# install apache config-file and fix paths
install -m 755 -d %{buildroot}%{httpconfdir}/conf.d
install -m 644 %{SOURCE1} %{buildroot}%{httpconfdir}/conf.d/%{name}.conf
sed -i -e 's|INFOREXDIR|%{inforexdir}|g ;
        s|INFOREXDATAROOT|%{_localstatedir}/lib/%{name}|g ;
        s|# Alias|Alias| ;
        s|PHPLIBDIR|%{phplibdir}|g' %{buildroot}%{httpconfdir}/conf.d/%{name}.conf

%post
if [ -x %{_sbindir}/semanage ] ; then
        # check is SELinux is installed and update settings
	if ( %{_sbindir}/semanage fcontext -l |grep %{_localstatedir}/lib/%{name} > /dev/null ) ; then 
		%{_sbindir}/semanage fcontext -d %{_localstatedir}/lib/%{name}
	fi
        %{_sbindir}/semanage fcontext -a -t httpd_var_lib_t %{_localstatedir}/lib/%{name}
	%{_sbindir}/restorecon %{_localstatedir}/lib/%{name}
	# %{inforexdir}/engine/templates_c for rw
	if ( %{_sbindir}/semanage fcontext -l |grep %{inforexdir}/engine/templates_c  > /dev/null ) ; then
		%{_sbindir}/semanage fcontext -d %{inforexdir}/engine/templates_c 
	fi
	%{_sbindir}/semanage fcontext -a -t httpd_sys_rw_content_t %{inforexdir}/engine/templates_c
	%{_sbindir}/restorecon %{inforexdir}/engine/templates_c
fi

%files
%defattr(644,root,root,755)

%dir %{inforexdir}
%{inforexdir}/*
%exclude %{inforexdir}/engine/templates_c
%attr(775,-,%{httpdgroup}) %{inforexdir}/engine/templates_c

%dir %{_localstatedir}/lib/%{name}
%{_localstatedir}/lib/%{name}/config.ini.php

%{httpconfdir}/conf.d/%{name}.conf

%changelog
* Thu Jun 24 2021 Seweryn Walentynowicz <seweryn@walor.torun.pl>
- templates_c directory must exists before packaging rpm
* Wed Feb 17 2021 Seweryn Walentynowicz <seweryn@walor.torun.pl>
- solved selinux context installation problems
* Mon Feb 15 2021 Seweryn Walentynowicz <seweryn@walor.torun.pl>
- Initial complete sql dump added
* Thu Jan 14 2021 Seweryn Walentynowicz <seweryn@walor.torun.pl>
- Initial rpm spec file
