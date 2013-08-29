/**
 *    Modified by Wojciech Gaweł 2011-2013
 *    - add "punbb" style
 *    - add history in url (need "jQuery BBQ: Back Button & Query Library")
 **/
(function($) {
    $.extend({
        tablesorterPager: new function() {

            function updatePageDisplay(c) {
                var s;
                if (c.view === 'punbb') {
                    var text = '';
                    var pageNum = c.page + 1;
                    var totalPages = c.totalPages;
                    var viewPunbbVisiblePageNumberMargin = c.viewPunbbVisiblePageNumberMargin;
                    var viewPunbbVisiblePageNumberMarginAtCorners = c.viewPunbbVisiblePageNumberMarginAtCorners;
                    for (var i = 1; i <= c.totalPages; i++) {
                        var isLastPage = i === c.totalPages;
                        var isFirstPage = i === 1;
                        if ((i > viewPunbbVisiblePageNumberMarginAtCorners) && ((pageNum - i) > viewPunbbVisiblePageNumberMargin) 
                                && !(viewPunbbVisiblePageNumberMargin + viewPunbbVisiblePageNumberMarginAtCorners - pageNum + 2 === 0)) {
                            if ((pageNum - i) === viewPunbbVisiblePageNumberMargin + viewPunbbVisiblePageNumberMarginAtCorners) {
                                text += '...';
                            }
                        } else if ((i <= totalPages - viewPunbbVisiblePageNumberMarginAtCorners) && ((i - pageNum) > viewPunbbVisiblePageNumberMargin)
                                && !(c.totalPages - viewPunbbVisiblePageNumberMargin - viewPunbbVisiblePageNumberMarginAtCorners - pageNum - 1 === 0)) {
                            if ((i - pageNum) === viewPunbbVisiblePageNumberMargin + viewPunbbVisiblePageNumberMarginAtCorners) {
                                text += '...';
                            }
                        } else {
                            if (pageNum === i) {
                                text += '<a href="#' + c.currentPageUrlId + '=' + i + '" class="' + c.currentPageNumber + (isLastPage ? ' last' : (isFirstPage ? ' first' : '')) + '" id="' + i + '">' + i + '</a>';
                            } else {
                                text += '<a href="#' + c.currentPageUrlId + '=' + i + '" id="' + i + '" class="' + (isLastPage ? ' last' : (isFirstPage ? ' first' : '')) + '">' + i + '</a>';
                            }
                        }
                    }
                    s = $(c.cssPageDisplay, c.container).html(text);
                } else {
                    s = $(c.cssPageDisplay, c.container).val((c.page + 1) + c.seperator + c.totalPages);
                }
            }

            function setPageSize(table, size) {
                var c = table.config;
                c.size = size;
                c.totalPages = Math.ceil(c.totalRows / c.size);
                c.pagerPositionSet = false;
                moveToPage(table);
                fixPosition(table);
            }

            function fixPosition(table) {
                var c = table.config;
                if (!c.pagerPositionSet && c.positionFixed) {
                    var c = table.config, o = $(table);
                    if (o.offset) {
                        c.container.css({
                            top: o.offset().top + o.height() + 'px',
                            position: 'absolute'
                        });
                    }
                    c.pagerPositionSet = true;
                }
            }

            function moveToFirstPage(table) {
                var c = table.config;
                c.page = 0;
                moveToPage(table);
            }

            function moveToLastPage(table) {
                var c = table.config;
                c.page = (c.totalPages - 1);
                moveToPage(table);
            }

            function moveToNextPage(table) {
                var c = table.config;
                c.page++;
                if (c.page >= (c.totalPages - 1)) {
                    c.page = (c.totalPages - 1);
                }
                moveToPage(table);
            }

            function moveToPrevPage(table) {
                var c = table.config;
                c.page--;
                if (c.page <= 0) {
                    c.page = 0;
                }
                moveToPage(table);
            }


            function moveToPage(table) {
                var c = table.config;
                if (c.page < 0 || c.page > (c.totalPages - 1)) {
                    c.page = 0;
                }
                var state = {};
                state[table.config.currentPageUrlId] = c.page + 1;
                $.bbq.pushState(state);
                renderTable(table, c.rowsCopy);
            }

            function renderTable(table, rows) {

                var c = table.config;
                var l = rows.length;
                var s = (c.page * c.size);
                var e = (s + c.size);
                if (e > rows.length) {
                    e = rows.length;
                }


                var tableBody = $(table.tBodies[0]);

                // clear the table body

                $.tablesorter.clearTableBody(table);

                for (var i = s; i < e; i++) {

                    //tableBody.append(rows[i]);

                    var o = rows[i];
                    var l = o.length;
                    for (var j = 0; j < l; j++) {

                        tableBody[0].appendChild(o[j]);

                    }
                }

                fixPosition(table, tableBody);

                $(table).trigger("applyWidgets");

                if (c.page >= c.totalPages) {
                    moveToLastPage(table);
                }

                updatePageDisplay(c);
            }

            this.appender = function(table, rows) {

                var c = table.config;

                c.rowsCopy = rows;
                c.totalRows = rows.length;
                c.totalPages = Math.ceil(c.totalRows / c.size);

                renderTable(table, rows);
            };

            this.defaults = {
                size: 10,
                offset: 0,
                page: 0,
                totalRows: 0,
                totalPages: 0,
                container: null,
                cssNext: '.next',
                cssPrev: '.prev',
                cssFirst: '.first',
                cssLast: '.last',
                cssPageDisplay: '.pagedisplay',
                cssPageSize: '.pagesize',
                seperator: "/",
                positionFixed: true,
                appender: this.appender,
                view: 'standard',
                viewPunbbVisiblePageNumberMargin: 2,
                viewPunbbVisiblePageNumberMarginAtCorners: 2,
                currentPageNumber: 'currentPageNumber',
                currentPageUrlId: 'page'
            };

            this.construct = function(settings) {

                return this.each(function() {

                    config = $.extend(this.config, $.tablesorterPager.defaults, settings);

                    var table = this, pager = config.container;

                    $(this).trigger("appendCache");

                    config.size = parseInt($(".pagesize", pager).val());

                    $(config.cssFirst, pager).click(function() {
                        moveToFirstPage(table);
                        return false;
                    });
                    $(config.cssNext, pager).click(function() {
                        moveToNextPage(table);
                        return false;
                    });
                    $(config.cssPrev, pager).click(function() {
                        moveToPrevPage(table);
                        return false;
                    });
                    $(config.cssLast, pager).click(function() {
                        moveToLastPage(table);
                        return false;
                    });
                    $(config.cssPageSize, pager).change(function() {
                        setPageSize(table, parseInt($(this).val()));
                        return false;
                    });
                    $(config.cssPageDisplay, pager).delegate('a', 'click', function() {
                        table.config.page = parseInt($(this).attr("id")) - 1;
                        moveToPage(table);
                        return false;
                    });
                    $(window).bind('hashchange', function(e) {
                        if (table.config !== undefined) {
                            var page_number = $.bbq.getState(table.config.currentPageUrlId, true) || 1;
                            if (table.config.page != page_number - 1) {
                                table.config.page = page_number - 1;
                                moveToPage(table);
                            }
                        }
                    });
                    $(window).trigger('hashchange');
                });

            };

        }
    });
    // extend plugin scope
    $.fn.extend({
        tablesorterPager: $.tablesorterPager.construct
    });

})(jQuery);				