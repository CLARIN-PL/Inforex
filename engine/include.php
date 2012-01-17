<?php
/**
 * Wczytanie wszystkich potrzebnych bibliotek. 
 */
require_once($config->path_engine . '/include/Smarty-2.6.22/libs/Smarty.class.php');
require_once($config->path_engine . '/include/pear/HTML/Select.php'); // PEAR module with local changes
require_once($config->path_engine . '/include/pear/FirePHPCore/fb.php');
require_once("PEAR.php");
require_once("MDB2.php");
require_once('HTTP/Session2.php');
require_once('Auth/Auth.php');

require_once($config->path_engine . '/database.php');

require_once($config->path_engine . '/include/anntakipi/ixtTakipiReader.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiDocument.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiStruct.php');

require_once($config->path_engine . '/include/CAction.php');
require_once($config->path_engine . '/include/CInforexWeb.php');
require_once($config->path_engine . '/include/CLiner.php');
require_once($config->path_engine . '/include/CPage.php');
require_once($config->path_engine . '/include/CPerspective.php');
require_once($config->path_engine . '/include/CRequestLoader.php');
require_once($config->path_engine . '/include/CTextAligner.php');
require_once($config->path_engine . '/include/CTeiFormater.php');
require_once($config->path_engine . '/include/CUserAuthorize.php');
require_once($config->path_engine . '/include/CWSTagger.php');

require_once($config->path_engine . '/include/report_reformat.php');
require_once($config->path_engine . '/include/ner_filter.php');
require_once($config->path_engine . '/include/lib_htmlstr.php');
require_once($config->path_engine . '/include/lib_htmlparser.php');
require_once($config->path_engine . '/include/lib_roles.php');

require_once($config->path_engine . '/include/class/a_table.php');
require_once($config->path_engine . '/include/class/c_report.php');
require_once($config->path_engine . '/include/class/c_report_annotation.php');
require_once($config->path_engine . '/include/class/c_corpus.php');

require_once($config->path_engine . '/include/database/CDbAnnotation.php');
require_once($config->path_engine . '/include/database/CDbCorpus.php');
require_once($config->path_engine . '/include/database/CDbCorpusStats.php');
require_once($config->path_engine . '/include/database/CDbCorpusRelation.php');
require_once($config->path_engine . '/include/database/CDbReport.php');
require_once($config->path_engine . '/include/database/CDbToken.php');
require_once($config->path_engine . '/include/database/CPlWordnet.php');
require_once($config->path_engine . '/include/database/DBReportPerspective.php');
require_once($config->path_engine . '/include/database/db_reports.php');

require_once($config->path_engine . '/include/factory/CCclFactory.php');

require_once($config->path_engine . '/include/integrity/CCclIntegrity.php');
require_once($config->path_engine . '/include/integrity/CTokensIntegrity.php');
require_once($config->path_engine . '/include/integrity/CAnnotationsIntegrity.php');

require_once($config->path_engine . '/include/readers/CCclReader.php');
require_once($config->path_engine . '/include/readers/CFolderReader.php');

require_once($config->path_engine . '/include/structs/AnnotatedDocumentStruct.php');
require_once($config->path_engine . '/include/structs/CclStruct.php');

require_once($config->path_engine . '/include/utils/CDocumentConverter.php');
require_once($config->path_engine . '/include/utils/CDiffFormatter.php');
require_once($config->path_engine . '/include/utils/CHelperBootstrap.php');
require_once($config->path_engine . '/include/utils/CHelperTokenize.php');
require_once($config->path_engine . '/include/utils/CLpsTextTransformer.php');
require_once($config->path_engine . '/include/utils/CMyDomDocument.php');
require_once($config->path_engine . '/include/utils/CReportPerspective.php');
require_once($config->path_engine . '/include/utils/CReformat.php');
require_once($config->path_engine . '/include/utils/CUserActivity.php');
require_once($config->path_engine . '/include/utils/CWcclAnnotation.php');
require_once($config->path_engine . '/include/utils/CWcclDocument.php');
require_once($config->path_engine . '/include/utils/CWcclReader.php');
require_once($config->path_engine . '/include/utils/CWcclRelation.php');

require_once($config->path_engine . '/include/writers/CAlephWriter.php');
require_once($config->path_engine . '/include/writers/CHtmlWriter.php');
require_once($config->path_engine . '/include/writers/CCclWriter.php');

require_once($config->path_engine . '/pages/report_perspectives/PerspectiveAnaphora.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveAnnotator_anaphora.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveAnnotator.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveAnnotatorWSD.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveDiffs.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveEdit.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveEdit_raw.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveHtml.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectivePreview.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveTakipi.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveTei.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveTopic.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveTranscription.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveTokenization.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveAutoExtension.php');
require_once($config->path_engine . '/pages/report_perspectives/PerspectiveRelation_statistic.php');

?>