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

define("DB_TABLE_ANNOTATION_TYPES", "annotation_types");
define("DB_TABLE_ANNOTATION_SETS_CORPORA", "annotation_sets_corpora");
define("DB_TABLE_REPORTS", "reports");
define("DB_TABLE_REPORTS_ANNOTATIONS_LEMMA", "reports_annotations_lemma");
define("DB_TABLE_REPORTS_ANNOTATIONS", "reports_annotations_optimized");
define("DB_TABLE_REPORTS_USERS_SELECTION", "reports_users_selection");
define("DB_TABLE_TASKS_REPORTS", "tasks_reports");

define("DB_COLUMN_CORPORA__CORPUS_ID", "id");

define("DB_COLUMN_REPORTS__REPORT_ID", "id");
define("DB_COLUMN_REPORTS__TITLE", "title");
define("DB_COLUMN_REPORTS__LANG", "lang");
define("DB_COLUMN_REPORTS__CONTENT", "content");
define("DB_COLUMN_REPORTS__FILENAME", "filename");
define("DB_COLUMN_REPORTS__DATE", "date");
define("DB_COLUMN_REPORTS__SOURCE", "source");
define("DB_COLUMN_REPORTS__FORMAT_ID", "format_id");
define("DB_COLUMN_REPORTS__CORPUS_ID", "corpora");

define("DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID", "id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID", "type_id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__FROM", "from");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TO", "to");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TEXT", "text");
define("DB_COLUMN_REPORTS_ANNOTATIONS__STAGE", "stage");
define("DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID", "user_id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ID", "report_id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__LEMMA", "lemma");

define("DB_COLUMN_REPORTS_USERS_SELECTION__USER_ID", "user_id");
define("DB_COLUMN_REPORTS_USERS_SELECTION__REPORT_ID", "report_id");

define("DB_COLUMN_USERS__USER_ID", "user_id");

define("DB_COLUMN_CORPORA_FLAGS__CORPORA_FLAG_ID", "corpora_flag_id");
define("DB_COLUMN_CORPORA_FLAGS__CORPORA_ID", "corpora_id");
define("DB_COLUMN_CORPORA_FLAGS__NAME", "name");
define("DB_COLUMN_CORPORA_FLAGS__SHORT", "short");
define("DB_COLUMN_CORPORA_FLAGS__SORT", "sort");
define("DB_COLUMN_CORPORA_FLAGS__DESCRIPTION", "description");

define("DB_COLUMN_TOKENS__FROM", "from");
define("DB_COLUMN_TOKENS__TO", "to");
define("DB_COLUMN_TOKENS__TOKEN_ID", "token_id");
define("DB_COLUMN_TOKENS__REPORT_ID", "report_id");

define("DB_REPORT_FORMATS_XML", 1);
define("DB_REPORT_FORMATS_PLAIN", 2);
define("DB_REPORT_FORMATS_PREMORPH", 3);

define("DB_COLUMN_TASKS_REPORTS__REPORT_ID", "report_id");
define("DB_COLUMN_TASKS_REPORTS__TASK_ID", "task_id");
define("DB_COLUMN_TASKS_REPORTS__STATUS", "status");
define("DB_COLUMN_TASKS_REPORTS__MESSAGE", "message");

define("DB_SHARED_ATTRIBUTE_TYPES_ENUM", 'enum');
define("DB_SHARED_ATTRIBUTE_TYPES_STRING", 'string');