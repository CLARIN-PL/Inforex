<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

define("ROLE_SYSTEM_USER_ADMIN", "admin");
define("ROLE_SYSTEM_USER_LOGGEDIN", "loggedin");
define("ROLE_SYSTEM_EDITOR_SCHEMA_RELATIONS", "editor_schema_relations");
define("ROLE_SYSTEM_EDITOR_SCHEMA_EVENTS", "editor_schema_events");
define("ROLE_SYSTEM_USER_PUBLIC", "public_user");

/** Definicja ról na poziomie systemu. */
// ToDo: replace with the above variables
define("USER_ROLE_ADMIN", "admin");
define("USER_ROLE_LOGGEDIN", "loggedin");

/** Definicja ról na poziomie korpusu. */
define("CORPUS_ROLE_ADD_DOCUMENTS", "add_documents");
define("CORPUS_ROLE_AGREEMENT_CHECK", "agreement_check");
define("CORPUS_ROLE_AGREEMENT_MORPHOLOGY", "agreement_morpho");
define("CORPUS_ROLE_FLAG_HISTORY", "flag_history");
define("CORPUS_ROLE_ANNOTATE", "annotate");
define("CORPUS_ROLE_ANNOTATE_AGREEMENT", "annotate_agreement");
define("CORPUS_ROLE_BROWSE_ANNOTATIONS", "browse_annotations");
define("CORPUS_ROLE_BROWSE_RELATIONS", "browse_relations");
define("CORPUS_ROLE_DELETE_ANNOTATIONS", "delete_annotations");
define("CORPUS_ROLE_DELETE_DOCUMENTS", "delete_documents");
define("CORPUS_ROLE_EDIT_DOCUMENTS", "edit_documents");
define("CORPUS_ROLE_EXPORT", "export");
define("CORPUS_ROLE_MANAGER", "manager");
define("CORPUS_ROLE_OWNER", "corpus_owner");
define("CORPUS_ROLE_READ", "read");
define("CORPUS_ROLE_READ_LIMITED", "read_limited");
define("CORPUS_ROLE_RUN_TESTS", "run_tests");
define("CORPUS_ROLE_TASKS", "tasks");
define("CORPUS_ROLE_WCCL_MATCH", "wccl_match");

define("CORPUS_ROLE_IS_PUBLIC", "corpus_role_is_public");