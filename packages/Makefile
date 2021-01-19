# local path from current dir
RPM_SPEC_DIR = rpm/SPECS
RPM_SOURCES_DIR = rpm/SOURCES

RPMBUILD = $(shell if [ -e /usr/bin/rpmbuild ]; then echo /usr/bin/rpmbuild; else bin/rpm; fi)
HAVE_RPMBUILD = "no"$(RPMBUILD)
RPM = rpm
PACKAGE = inforex
GITHASH=`git log --oneline |head -1|cut -d" " -f 1`
VERSION = 0.1
RELNO = 1
RELEASE = $(shell if [ ! -z '$(GITHASH)' ]; then echo -n "$(RELNO)v.$(GITHASH)"; else echo -n "$(RELNO)";fi)

RELSPECTAG = RELEASE_TAG

# glob cannot work here
TARITIFEXISTS = tests phpunit.xml data
FILES2TAR = composer.json apps LICENCE.md local sonar-project.properties config/README.md documentations phpunit engine gfx public_html database INSTALL.md README.md $(foreach wrd,$(TARITIFEXISTS),$(wrd))

$(RPM_SOURCES_DIR)/$(PACKAGE).tgz: ../.git/index 
	@(tar -C .. -cvzf $(RPM_SOURCES_DIR)/$(PACKAGE).tgz $(FILES2TAR) > /dev/null )

.PHONY: rpm-local srpm-common rpm-common srpm rpm all clean 

all: rpm

# to wymaga podania w wywołaniu parametrów w zmiennych środowiska
#  rpmbuild="katalog_do_budowania" i rpmspec="nazwa_pliku_spec"
rpm-local: $(RPM_SOURCES_DIR)/$(PACKAGE).tgz
	@(if test "${HAVE_RPMBUILD}" = "no"; then \
                echo -e "\n" \
        "*** Required util ${RPMBUILD} missing.  Please install the\n" \
        "*** package for your distribution which provides ${RPMBUILD},\n" \
        "*** re-run configure, and try again.\n"; \
                exit 1; \
        fi; \
        mkdir -p $(rpmbuild)/TMP && \
        mkdir -p $(rpmbuild)/BUILD && \
        mkdir -p $(rpmbuild)/RPMS && \
        mkdir -p $(rpmbuild)/SRPMS && \
        mkdir -p $(rpmbuild)/SPECS && \
        cat ${RPM_SPEC_DIR}/$(rpmspec) | sed -e 's/${RELSPECTAG}/${RELEASE}/' > $(rpmbuild)/SPECS/$(rpmspec) && \
        mkdir -p $(rpmbuild)/SOURCES && \
	if ls -A ${RPM_SOURCES_DIR} ; then \
		cp ${RPM_SOURCES_DIR}/* $(rpmbuild)/SOURCES; \
	fi)

srpm-common:
	@(rpmspec=$(PACKAGE).spec; \
	rpmbuild=`mktemp -t -d $(PACKAGE)-build-$$USER-XXXXXXXX`; \
	$(MAKE) $(AM_MAKEFLAGS) \
		rpmbuild="$$rpmbuild" \
		rpmspec="$$rpmspec" \
		rpm-local || exit 1; \
	LANG=C $(RPMBUILD) \
                --define "_tmppath $$rpmbuild/TMP" \
                --define "_topdir $$rpmbuild" \
                $(def) -bs $$rpmbuild/SPECS/$$rpmspec || exit 1; \
	cp $$rpmbuild/SRPMS/*.src.rpm . || exit 1; \
        rm -R $$rpmbuild)

 
rpm-common: 
	@(rpmpkg=$(PACKAGE)-$(VERSION)-$(RELEASE)*src.rpm; \
        rpmspec=$(PACKAGE).spec; \
        rpmbuild=`mktemp -t -d $(PACKAGE)-build-$$USER-XXXXXXXX`; \
	$(MAKE) $(AM_MAKEFLAGS) \
                rpmbuild="$$rpmbuild" \
                rpmspec="$$rpmspec" \
                rpm-local || exit 1; \
	LANG=C ${RPMBUILD} \
                --define "_tmppath $$rpmbuild/TMP" \
                --define "_topdir $$rpmbuild" \
                $(def) --rebuild $$rpmpkg || exit 1; \
	cp $$rpmbuild/RPMS/*/* . || exit 1; \
        rm -R $$rpmbuild)

rpm: srpm-common rpm-common
srpm: srpm-common

clean:
	rm -f $(PACKAGE)*.rpm
