%define _unpackaged_files_terminate_build 1
%define __brp_mangle_shebangs  /usr/bin/true

%define        httpduser         apache
%define        httpdgroup        apache
%define        httpconfdir       /etc/httpd
#% define        phplibdir         %{_datadir}/php
%define        phplibdir         /var/www/html
%define        inforexdir        %{phplibdir}/%{name}

Name:       inforex
Version:    0.1
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

# remove .htaccess files
#find . -name \.htaccess -print | xargs rm -f

# remove several scripts not used in the package
#rm -rf  vendor/ezyang/htmlpurifier/maintenance/

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
        semanage fcontext -a -t httpd_var_lib_t %{_localstatedir}/lib/%{name}
fi

%files
%defattr(644,root,root,755)

%dir %{inforexdir}
%{inforexdir}/*

%{_localstatedir}/lib/%{name}/config.ini.php

%{httpconfdir}/conf.d/%{name}.conf

%changelog
* Thu Jan 14 2021 Seweryn Walentynowicz <seweryn@walor.torun.pl>
- Initial rpm spec file
