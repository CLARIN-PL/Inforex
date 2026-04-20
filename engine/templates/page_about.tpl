{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<!--
<div class="panel panel-primary" style="margin: 5px">
	<div class="panel-heading">About</div>
	<div class="panel-body">
-->
<div class="container-fluid admin_tables about-page">
			<div class="panel panel-default about-panel">
				<div class="panel-heading about-panel-heading">
					<span class="about-panel-heading-icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
					<span>About</span>
				</div>
				<div class="panel-body">
					<div class="row about-section">
						<div class="col-md-6">
							<div class="about-card about-card-primary">
								<div class="about-card-heading">
									<span class="about-card-icon"><i class="fa fa-database" aria-hidden="true"></i></span>
									<div>
										<h3>Corpus annotation platform</h3>
										<p>Construction, sharing and semantic annotation of text corpora.</p>
									</div>
								</div>
								<p class="about-lead">
									<b>Inforex</b> is a web system for text corpora construction. Inforex allows parallel access and sharing resources among many users. The system assists semantic annotation of texts on several levels, such as marking text references, creating new references, or marking word senses.
								</p>
								<div class="about-feature-title">Main features</div>
								<ul class="about-feature-list">
									<li>does not require installation &mdash; access through a web browser supporting JavaScript,</li>
									<li>remote access to the data,</li>
									<li>data sharing between users,</li>
									<li>control of work progress,</li>
									<li>advanced system of access control &mdash; by users and tasks,</li>
									<li>supports metadata, content cleanup, phrase annotation, phrase lemmatisation and annotation linking,</li>
									<li>inter-annotator agreement on the level of phrase annotation,</li>
									<li>export documents to a ccl format.</li>
								</ul>
								<div class="about-system-info">
									{if $rev!="no_git_rev"}<span>Code revision: <b>{$rev}</b></span>{/if}
									<span>Suggested browser: <a href="http://www.mozilla.com/pl/firefox/">Chrome</a> <img src="gfx/Google-Chrome-icon.png" title="Chrome" alt="Chrome"/></span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<ul class="about-resource-list">
								<li class="about-resource-card">
									<span class="about-resource-icon"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></span>
									<div>
										<h4>Instruction</h4>
										<span class="about-resource-date">17 lipca 2025</span>
										<a href="https://files.clarin-pl.eu/api/public/dl/hZI5B8Dm/pdfs/Inforex%20-%20instrukcja%201.1.pdf">Inforex user instruction (PL)</a>
									</div>
								</li>
								<li class="about-resource-card">
									<span class="about-resource-icon"><i class="fa fa-youtube-play" aria-hidden="true"></i></span>
									<div>
										<h4>Movies</h4>
										<span class="about-resource-date">2020-04</span>
										<a href="https://www.youtube.com/@MichaMarcinczuk">Progress monitoring, users, user roles, subcorpora, metadata, frequency lists</a>
									</div>
								</li>
								<li class="about-resource-card">
									<span class="about-resource-icon"><i class="fa fa-slideshare" aria-hidden="true"></i></span>
									<div>
										<h4>Presentations</h4>
										<span class="about-resource-date">20 października 2025</span>
										<a href="https://files.clarin-pl.eu/api/public/dl/Pn5Zti76?inline=true">Gromadzenie, anotowanie i udostępnianie korpusów</a>, Marcin Oleksy, Jan Kocoń
									</div>
								</li>
							</ul>
						</div>
					</div>
			</div>
		</div>

        <!--
		<div class="panel panel-default">
			<div class="panel-heading">Gallery</div>
			<div class="panel-body">
				<div style="text-align: center">
					<a class="fancybox" rel="group1" href="screens/inforex_agreement_analysis.png" title="Agreement module"><img src="screens/inforex_agreement_analysis_small.png" alt=""/></a>
					<a class="fancybox" rel="group1" href="screens/inforex_agreement_annotator.png" title="Agreement module"><img src="screens/inforex_agreement_analysis_small.png" alt=""/></a>
					<a class="fancybox" rel="group1" href="screens/inforex_agreement_annotator.png" title="Agreement module"><img src="screens/inforex_agreement_analysis_small.png" alt=""/></a>
					</div>
			</div>
		</div>
        -->

			<div class="panel panel-default contribution-panel">
				<div class="panel-heading contribution-panel-heading">
					<span class="contribution-panel-heading-icon"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
					<span>Contribution</span>
				</div>
				<div class="panel-body">
					<div class="row contributor-section">
						<div class="col-md-6">
							<div class="contributor-card contributor-card-active">
								<div class="contributor-heading">
									<span class="contributor-icon"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
									<div>
										<h3>Currently involved in the development</h3>
										<p>People actively shaping the project.</p>
									</div>
								</div>
								<ul class="contributor-list">
									<li><a href="http://marcinoleksy.pl/" target="_blank" title="Logo design and substantive consultation">Marcin Oleksy</a></li>
									<li><span title="Developer">Tomasz Naskręt</span></li>
								</ul>
							</div>
						</div>

						<div class="col-md-6">
							<div class="contributor-card">
								<div class="contributor-heading">
									<span class="contributor-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
									<div>
										<h3>Involved in the past</h3>
										<p>People who contributed to earlier stages.</p>
									</div>
								</div>
								<ul class="contributor-list contributor-list-muted">
									<li><span title="Developer">Adam Kaczmarek</span></li>
									<li><span title="Developer">Jan Kocoń</span></li>
									<li><a href="http://czuk.eu" title="Coordinator and developer">Michał Marcińczuk</a></li>
									<li><span title="Developer">Marcin Ptak</span></li>
									<li><span title="Developer">Mikołaj Szewczyk</span></li>
									<li><span title="Developer">Wojciech Rauk</span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default publications-panel">
				<div class="panel-heading publications-heading">
					<span class="publications-heading-icon"><i class="fa fa-book" aria-hidden="true"></i></span>
					<span>Publications</span>
				</div>
				<div class="panel-body">
					<div class="row publication-card publication-card-featured">
						<div class="col-md-12">
							<div class="publication-meta">Latest publication · RANLP 2019 · Varna, Bulgaria</div>
							<div class="publication-title">
								Marcińczuk, M. &amp; Oleksy, M. (2019).
								<a href="https://www.researchgate.net/publication/335402187_Inforex_-_a_Collaborative_System_for_Text_Corpora_Annotation_and_Analysis_Goes_Open">
									Inforex — a Collaborative System for Text Corpora Annotation and Analysis Goes Open</a>.
							</div>
							<div class="publication-description">
								Proceedings of the International Conference on Recent Advances in Natural Language Processing, pages 711―719. INCOMA Ltd.
							</div>
							<div class="publication-actions">
								<button type="button" class="publication-copy-bibtex" title="Copy BibTeX">
									<i class="fa fa-clipboard" aria-hidden="true"></i>
									Copy BibTeX
								</button>
								<span class="publication-copy-status" aria-live="polite"></span>
							</div>
							<textarea class="publication-bibtex-source" readonly>
@inproceedings{ldelim}marcinczuk-oleksy-2019-inforex,
    title     = "{ldelim}I}nforex {ldelim}---} a Collaborative System for Text Corpora Annotation and Analysis Goes Open",
    author    = "Marci{ldelim}\'n}czuk, Micha{ldelim}\l}  and
                Oleksy, Marcin",
    booktitle = "Proceedings of the International Conference on Recent Advances in Natural Language Processing (RANLP 2019)",
    month     = sep,
    year      = "2019",
    address   = "Varna, Bulgaria",
    publisher = "INCOMA Ltd.",
    url       = "https://www.aclweb.org/anthology/R19-1083",
    doi       = "10.26615/978-954-452-056-4_083",
    pages     = "711--719",
}
							</textarea>

					</div>
						<div class="col-md-12 publication-embed">
						<!--
						<p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View LREC 2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/452559285/content?start_page=1&view_mode=scroll&access_key=key-KMJfdnjdgbeQD4jWmcYs" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
					</div>
				</div>

	                <div class="row publication-card">
		                    <div class="col-md-12">

							<div class="publication-meta">Previous publication · RANLP 2017 · Varna, Bulgaria</div>
							<div class="publication-title">
								Marcińczuk, M., Oleksy, M. &amp; Kocoń, J. (2017).
								<a href="https://www.researchgate.net/publication/321580606_Inforex-a_Collaborative_System_for_Text_Corpora_Annotation_and_Analysis">
									Inforex—a Collaborative System for Text Corpora Annotation and Analysis</a>.
							</div>
							<div class="publication-description">
								In Mitkov, Ruslan, Angelova, Galia (editors), Proceedings of the International Conference Recent Advances in Natural Language Processing, pages 473-482. INCOMA Ltd.
							</div>
							<div class="publication-actions">
								<button type="button" class="publication-copy-bibtex" title="Copy BibTeX">
									<i class="fa fa-clipboard" aria-hidden="true"></i>
									Copy BibTeX
								</button>
								<span class="publication-copy-status" aria-live="polite"></span>
							</div>

	                <textarea class="publication-bibtex-source" readonly>
@inproceedings{ldelim}DBLP:conf/ranlp/MarcinczukOK17,
    author    = {ldelim}Michal Marcinczuk and Marcin Oleksy and Jan Kocon},
    editor    = {ldelim}Ruslan Mitkov and Galia Angelova},
    title     = {ldelim}Inforex - a collaborative system for text corpora annotation and analysis},
    booktitle = {ldelim}Proceedings of the International Conference Recent Advances in Natural
    Language Processing, {ldelim}RANLP} 2017, Varna, Bulgaria, September 2-8, 2017},
    pages     = {ldelim}473--482},
    publisher = {ldelim}{ldelim}INCOMA} Ltd.},
    year      = {ldelim}2017},
    url       = {ldelim}https://doi.org/10.26615/978-954-452-049-6_063},
    doi       = {ldelim}10.26615/978-954-452-049-6_063},
    timestamp = {ldelim}Tue, 09 Jan 2018 14:09:59 +0100},
    biburl    = {ldelim}https://dblp.org/rec/bib/conf/ranlp/MarcinczukOK17},
    bibsource = {ldelim}dblp computer science bibliography, https://dblp.org}
}
							</textarea>
					</div>
						<div class="col-md-12 publication-embed">
						<!--
						<p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View LREC 2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/373745808/content?start_page=1&view_mode=scroll&access_key=key-auTc87QYbGNceZ59Wf0X&show_recommendations=true" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
                    </div>
				</div>
					<div class="row publication-card">
		                    <div class="col-md-12">

							<div class="publication-meta">Previous publication · LREC 2012 · Istanbul, Turkey</div>
							<div class="publication-title">
								Marcińczuk, M., Kocoń, J. &amp; Broda, B. (2012).
								<a href="https://www.researchgate.net/publication/308886657_Inforex_-_a_web-based_tool_for_text_corpus_management_and_semantic_annotation">Inforex &mdash; a web-based tool for text corpus management and semantic annotation</a>.
							</div>
							<div class="publication-description">
								In Calzolari, N., Choukri, K., Declerck, T., Do\u{ldelim}g}an, M. U., Maegaard, B., Mariani, J. et al. (editors), Proceedings of the Eighth International Conference on Language Resources and Evaluation, pages 224-230. European Language Resources Association.
							</div>
							<div class="publication-actions">
								<button type="button" class="publication-copy-bibtex" title="Copy BibTeX">
									<i class="fa fa-clipboard" aria-hidden="true"></i>
									Copy BibTeX
								</button>
								<span class="publication-copy-status" aria-live="polite"></span>
							</div>

				<textarea class="publication-bibtex-source" readonly>
@InProceedings{ldelim}lMARCICZUK12.446,
    author = {ldelim}Michał Marcińczuk and Jan Kocoń and Bartosz Broda},
    title = {ldelim}Inforex -- a web-based tool for text corpus management and semantic annotation},
    booktitle = {ldelim}Proceedings of the Eight International Conference on Language Resources and Evaluation (LREC'12)},
    year = {ldelim}2012},
    month = {ldelim}may},
    date = {ldelim}23-25},
    address = {ldelim}Istanbul, Turkey},
    editor = {ldelim}Nicoletta Calzolari (Conference Chair) and Khalid Choukri and Thierry Declerck and Mehmet Uğur Doğan and Bente Maegaard and Joseph Mariani and Asuncion Moreno and Jan Odijk and Stelios Piperidis},
    publisher = {ldelim}European Language Resources Association (ELRA)},
    isbn = {ldelim}978-2-9517408-7-7},
    language = {ldelim}english}
 }
					</textarea>
					</div>
					<div class="col-md-12 publication-embed">
						<!--
                        <p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View Lrec2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/125073059/content?start_page=1&view_mode=scroll&access_key=key-tq924rkuphbq1yra45l&show_recommendations=true" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
                    </div>
                </div>
			</div>
		</div>

<!--
	</div>
</div>
-->
</div>

{literal}
<script type="text/javascript">
$(function () {
	function copyText(text) {
		if (navigator.clipboard && navigator.clipboard.writeText) {
			return navigator.clipboard.writeText(text);
		}

		var textarea = document.createElement("textarea");
		textarea.value = text;
		textarea.setAttribute("readonly", "readonly");
		textarea.style.position = "fixed";
		textarea.style.left = "-9999px";
		document.body.appendChild(textarea);
		textarea.select();

		try {
			document.execCommand("copy");
			return $.Deferred().resolve().promise();
		} catch (error) {
			return $.Deferred().reject(error).promise();
		} finally {
			document.body.removeChild(textarea);
		}
	}

	$(".publication-copy-bibtex").on("click", function () {
		var $button = $(this);
		var $card = $button.closest(".publication-card");
		var $status = $card.find(".publication-copy-status");
		var bibtex = $.trim($card.find(".publication-bibtex-source").val());

		copyText(bibtex).then(function () {
			$status.text("Copied");
			$button.addClass("publication-copy-bibtex-copied");
			setTimeout(function () {
				$status.text("");
				$button.removeClass("publication-copy-bibtex-copied");
			}, 1800);
		}, function () {
			$status.text("Copy failed");
		});
	});
});
</script>
{/literal}

{include file="inc_footer.tpl"}
