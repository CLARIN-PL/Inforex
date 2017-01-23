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


define("DB_COLUMN_REPORTS__REPORT_ID", "id");
define("DB_COLUMN_REPORTS__CONTENT", "content");

define("DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID", "id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID", "type_id");
define("DB_COLUMN_REPORTS_ANNOTATIONS__FROM", "from");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TO", "to");
define("DB_COLUMN_REPORTS_ANNOTATIONS__TEXT", "text");
define("DB_COLUMN_REPORTS_ANNOTATIONS__STAGE", "stage");
define("DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID", "user_id");

define("DB_COLUMN_USERS__USER_ID", "user_id");

?>