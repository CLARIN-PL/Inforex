<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Plik zawiera definicję nazw tabel i pól tabel z bazy danych.
 */

define("DB_TABLE_REPORTS_ANNOTATIONS", "reports_annotations_optimized");
define("DB_TABLE_REPORTS_USERS_SELECTION", "reports_users_selection");

define("DB_COLUMN_CORPORA__CORPUS_ID", "id");

define("DB_COLUMN_REPORTS__REPORT_ID", "id");
define("DB_COLUMN_REPORTS__TITLE", "title");
define("DB_COLUMN_REPORTS__CONTENT", "content");
define("DB_COLUMN_REPORTS__FILENAME", "filename");
define("DB_COLUMN_REPORTS__DATE", "date");
define("DB_COLUMN_REPORTS__SOURCE", "source");
define("DB_COLUMN_REPORTS__FORMAT_ID", "format_id");

define("DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID", "id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID", "type_id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__FROM", "from");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TO", "to");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TEXT", "text");
define("DB_COLUMN_REPORTS_ANNOTATIONS__STAGE", "stage");
define("DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID", "user_id");

define("DB_COLUMN_REPORTS_USERS_SELECTION__USER_ID", "user_id");
define("DB_COLUMN_REPORTS_USERS_SELECTION__REPORT_ID", "report_id");

define("DB_COLUMN_USERS__USER_ID", "user_id");

define("DB_COLUMN_CORPORA_FLAGS__CORPORA_FLAG_ID", "corpora_flag_id");
define("DB_COLUMN_CORPORA_FLAGS__CORPORA_ID", "corpora_id");
define("DB_COLUMN_CORPORA_FLAGS__NAME", "name");
define("DB_COLUMN_CORPORA_FLAGS__SHORT", "short");
define("DB_COLUMN_CORPORA_FLAGS__SORT", "sort");
define("DB_COLUMN_CORPORA_FLAGS__DESCRIPTION", "description");

define("DB_REPORT_FORMATS_XML", 1);
define("DB_REPORT_FORMATS_PLAIN", 2);
define("DB_REPORT_FORMATS_PREMORPH", 3);