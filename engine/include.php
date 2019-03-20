<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
ob_start();

$include_paths = array();
$include_paths[] = get_include_path();
set_include_path( implode(PATH_SEPARATOR, $include_paths) );

require_once("PEAR.php");
require_once('HTTP/Session2.php');
require_once('MDB2.php');
require_once('Auth/Auth.php');

require_once($config->path_engine . '/external/Smarty-2.6.22/libs/Smarty.class.php');
require_once($config->path_engine . '/external/pear/HTML/Select.php'); // PEAR module with local changes
require_once($config->path_engine . '/external/pear/FirePHPCore/fb.php');
require_once($config->path_engine . '/external/ChromePhp.php');

/*Abstract and generic classes go first */
require_once($config->path_engine . '/include/utils/GroupedLogger.php');

/* Remaining */
require_once($config->path_engine . '/include/database/Database.php');
require_once($config->path_engine . '/include/database/DatabaseException.php');
require_once($config->path_engine . '/include/database/database_deprecated.php');

require_once($config->path_engine . '/include/diagnostics/CPageAccessValidator.php');
require_once($config->path_engine . '/include/diagnostics/CPageAccessValidatorItem.php');
require_once($config->path_engine . '/include/diagnostics/CPageAjaxDiagnostic.php');

require_once($config->path_engine . '/include/anntakipi/ixtTakipiReader.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiDocument.php');
require_once($config->path_engine . '/include/anntakipi/ixtTakipiStruct.php');

require_once($config->path_engine . '/include/CAction.php');
require_once($config->path_engine . '/include/CAccessError.php');
require_once($config->path_engine . '/include/CInforexWeb.php');
require_once($config->path_engine . '/include/CPage.php');
require_once($config->path_engine . '/include/CPageAdministration.php');
require_once($config->path_engine . '/include/CPageCorpus.php');
require_once($config->path_engine . '/include/CPagePublic.php');
require_once($config->path_engine . '/include/CPerspective.php');
require_once($config->path_engine . '/include/CCorpusPerspective.php');
require_once($config->path_engine . '/include/CRequestLoader.php');
require_once($config->path_engine . '/include/CTextAligner.php');
require_once($config->path_engine . '/include/CTeiFormater.php');
require_once($config->path_engine . '/include/CUserAuthorize.php');
require_once($config->path_engine . '/include/HtmlParser.php');
require_once($config->path_engine . '/include/HtmlStr.php');
require_once($config->path_engine . '/include/HtmlStr2.php');
require_once($config->path_engine . '/include/ReportSearcher.php');

require_once($config->path_engine . '/include/api/Liner.php');
require_once($config->path_engine . '/include/api/Semql.php');
require_once($config->path_engine . '/include/api/Wccl.php');
require_once($config->path_engine . '/include/api/Wcrft.php');
require_once($config->path_engine . '/include/api/WSLiner2.php');
require_once($config->path_engine . '/include/api/WSTagger.php');

require_once($config->path_engine . '/include/class/a_table.php');
require_once($config->path_engine . '/include/class/c_image.php');
require_once($config->path_engine . '/include/class/c_report.php');
require_once($config->path_engine . '/include/class/TableReportAnnotation.php');
require_once($config->path_engine . '/include/class/TableReportAnnotationLemma.php');
require_once($config->path_engine . '/include/class/TableReportAnnotationSharedAttribute.php');
require_once($config->path_engine . '/include/class/c_corpus.php');
require_once($config->path_engine . '/include/class/c_task.php');
require_once($config->path_engine . '/include/class/c_import_annotations_ccl.php');

require_once($config->path_engine . '/include/database/sqlbuilder/SqlBuilderSelect.php');
require_once($config->path_engine . '/include/database/sqlbuilder/SqlBuilderWhere.php');
require_once($config->path_engine . '/include/database/sqlbuilder/SqlBuilderJoin.php');
require_once($config->path_engine . '/include/database/sqlbuilder/SqlBuilder.php');

require_once($config->path_engine . '/include/database/def_database_schema.php');
require_once($config->path_engine . '/include/database/CDbAnnotation.php');
require_once($config->path_engine . '/include/database/CDbAnnotationSet.php');
require_once($config->path_engine . '/include/database/CDbAnnotationSharedAttribute.php');
require_once($config->path_engine . '/include/database/CDbAnnotationType.php');
require_once($config->path_engine . '/include/database/CDbCorporaFlag.php');
require_once($config->path_engine . '/include/database/CDbCorporaUsers.php');
require_once($config->path_engine . '/include/database/CDbCorpus.php');
require_once($config->path_engine . '/include/database/CDbCorpusStats.php');
require_once($config->path_engine . '/include/database/CDbCorpusRelation.php');
require_once($config->path_engine . '/include/database/CDbFlag.php');
require_once($config->path_engine . '/include/database/CDbImage.php');
require_once($config->path_engine . '/include/database/CDbRelationSet.php');
require_once($config->path_engine . '/include/database/CDbRelationAgreement.php');
require_once($config->path_engine . '/include/database/CDbReport.php');
require_once($config->path_engine . '/include/database/CDbReportAnnotationLemma.php');
require_once($config->path_engine . '/include/database/CDbReportEvent.php');
require_once($config->path_engine . '/include/database/CDbReportFlag.php');
require_once($config->path_engine . '/include/database/CDbReportPerspective.php');
require_once($config->path_engine . '/include/database/CDbReportRelation.php');
require_once($config->path_engine . '/include/database/CDbStatus.php');
require_once($config->path_engine . '/include/database/CDbSubcorpus.php');
require_once($config->path_engine . '/include/database/CDbToken.php');
require_once($config->path_engine . '/include/database/CDbUserRoles.php');
require_once($config->path_engine . '/include/database/CPlWordnet.php');
require_once($config->path_engine . '/include/database/db_reports.php');
require_once($config->path_engine . '/include/database/CDbSens.php');
require_once($config->path_engine . '/include/database/CDbTag.php');
require_once($config->path_engine . '/include/database/CDbTask.php');
require_once($config->path_engine . '/include/database/CDbUser.php');
require_once($config->path_engine . '/include/database/CDbCtag.php');
require_once($config->path_engine . '/include/database/CDbBase.php');
require_once($config->path_engine . '/include/database/CDbOrth.php');
require_once($config->path_engine . '/include/database/CDbExport.php');
require_once($config->path_engine . '/include/database/CDbReportUserSelection.php');
require_once($config->path_engine . '/include/database/CDbTokensTagsOptimized.php');
require_once($config->path_engine . '/include/database/CDbTagset.php');

require_once($config->path_engine . '/include/define/def_roles.php');
require_once($config->path_engine . '/include/define/def_flags.php');

require_once($config->path_engine . '/include/enums/CTagset.php');

require_once($config->path_engine . '/include/export/CCclFactory.php');
require_once($config->path_engine . '/include/export/ExportManager.php');
require_once($config->path_engine . '/include/export/CorpusExporter.php');

require_once($config->path_engine . '/include/functions/func_aux.php');
require_once($config->path_engine . '/include/functions/func_cli.php');
require_once($config->path_engine . '/include/functions/func_flags.php');
require_once($config->path_engine . '/include/functions/func_ner_filter.php');
require_once($config->path_engine . '/include/functions/func_roles.php');
require_once($config->path_engine . '/include/functions/func_report_reformat.php');
require_once($config->path_engine . '/include/functions/func_shell.php');

require_once($config->path_engine . '/include/import/CorpusDocumentImporter.php');
require_once($config->path_engine . '/include/import/DocumentReaderTxt.php');
require_once($config->path_engine . '/include/import/DocumentAnnotationImporter.php');

require_once($config->path_engine . '/include/integrity/CCclIntegrity.php');
require_once($config->path_engine . '/include/integrity/CTokensIntegrity.php');
require_once($config->path_engine . '/include/integrity/CAnnotationsIntegrity.php');

require_once($config->path_engine . '/include/readers/CCclReader.php');
require_once($config->path_engine . '/include/readers/CFolderReader.php');

require_once($config->path_engine . '/include/structs/AnnotationEid.php');
require_once($config->path_engine . '/include/structs/AnnotatedDocumentStruct.php');
require_once($config->path_engine . '/include/structs/CclStruct.php');
require_once($config->path_engine . '/include/structs/TeiStruct.php');
//require_once($config->path_engine . '/include/structs/CclStruct2.php');

require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterEnumItem.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilter.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterEnum.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterEnumFlag.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterEnumLanguage.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterEnumSubcorpus.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterSearch.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterBase.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportFilterSelected.php');
require_once($config->path_engine . '/include/utils/reportlist/filters/ReportListFilters.php');

require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumn.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnId.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnFlag.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnTokenization.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnStatus.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnSubcorpus.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportColumnTitle.php');
require_once($config->path_engine . '/include/utils/reportlist/columns/ReportListColumns.php');

require_once($config->path_engine . '/include/utils/ElementCounter.php');
require_once($config->path_engine . '/include/utils/CclAnnotationFlatten.php');
require_once($config->path_engine . '/include/utils/CCookieManager.php');
require_once($config->path_engine . '/include/utils/CDocumentConverter.php');
require_once($config->path_engine . '/include/utils/CDiffFormatter.php');
require_once($config->path_engine . '/include/utils/CHelperBootstrap.php');
require_once($config->path_engine . '/include/utils/CHelperDocumentFilter.php');
require_once($config->path_engine . '/include/utils/CHelperPath.php');
require_once($config->path_engine . '/include/utils/CHelperTokenize.php');
require_once($config->path_engine . '/include/utils/CLpsTextTransformer.php');
require_once($config->path_engine . '/include/utils/CNlpRest2.php');
require_once($config->path_engine . '/include/utils/CMyDomDocument.php');
require_once($config->path_engine . '/include/utils/CReportPerspective.php');
require_once($config->path_engine . '/include/utils/CReportContent.php');
require_once($config->path_engine . '/include/utils/CPremorph.php');
require_once($config->path_engine . '/include/utils/CReformat.php');
require_once($config->path_engine . '/include/utils/CUserActivity.php');
require_once($config->path_engine . '/include/utils/CWcclAnnotation.php');
require_once($config->path_engine . '/include/utils/CWcclDocument.php');
require_once($config->path_engine . '/include/utils/CWcclImport.php');
require_once($config->path_engine . '/include/utils/CWcclReader.php');
require_once($config->path_engine . '/include/utils/CWcclRelation.php');
require_once($config->path_engine . '/include/utils/CMorphoUtil.php');

require_once($config->path_engine . '/include/writers/CAlephWriter.php');
require_once($config->path_engine . '/include/writers/CHtmlWriter.php');
require_once($config->path_engine . '/include/writers/CCclWriter.php');
require_once($config->path_engine . '/include/writers/CIobWriter.php');
require_once($config->path_engine . '/include/writers/CTeiWriter.php');

require_once($config->path_engine . '/page/report_perspectives/PerspectiveAgreement.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveRelationAgreement.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveMorphoDisamb.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveMorphoDisambAgreement.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnaphora.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotation_attributes.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotation_lemma.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotation_table.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveImportAnnotations.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotator_anaphora.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotator.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAnnotatorWSD.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveCleanup.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveDiffs.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveEdit.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveEdit_raw.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveFlag_history.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveMetadata.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveNoaccess.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveHtml.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveImages.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectivePreview.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTags.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTakipi.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTei.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTopic.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTranscription.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveTokenization.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveAutoExtension.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveRelation_statistic.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveViewer.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveUnassigned.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveEditTranslation.php');
require_once($config->path_engine . '/page/report_perspectives/PerspectiveExtendedMetadata.php');

require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveInformation.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveUsers.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveUsers_roles.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveUser_activity.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveSubcorpora.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectivePerspectives.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveFlags.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveAnnotation_sets.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveCustom_annotation_sets.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveRelation_sets.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveEvent_groups.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveCorpus_metadata.php');
require_once($config->path_engine . '/page/corpus_perspectives/PerspectiveCorpus_delete.php');
ob_end_clean();