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
<br/>
		<div class="panel panel-info">
			<div class="panel-heading">About</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<b>Inforex</b> is a web system for text corpora construction. Inforex allows parallel access and sharing resources among many users. The system assists semantic annotation of texts on several levels, such as marking text references, creating new references, or marking word senses.
						<br/><br/>
						Main features
						<ul>
							<li>does not require installation &mdash; access through a web browser supporting JavaScript,</li>
							<li>remote access to the data,</li>
							<li>data sharing between users,</li>
							<li>control of work progress,</li>
							<li>advanced system of access control &mbash; by users and tasks,</li>
							<li>supports several types of document description:
								<ul>
									<li>metadata,</li>
									<li>content cleanup,</li>
									<li>phrase annotation (a continous sequence of words/tokens),</li>
									<li>phrase lemmatisation,</li>
									<li>annotation linking.</li>
								</ul>
							</li>
							<li>inter-annotator agreement on the level of phrase annotation,</li>
							<li>export documents to a ccl format,</li>
						</ul>
						<br/>
						{if $rev!="no_git_rev"}Code revision : {$rev}<br/>{/if}
						Suggested browser: <a href="http://www.mozilla.com/pl/firefox/">Chrome</a> <img src="gfx/Google-Chrome-icon.png" title="Chrome" style="vertical-align: middle"/>
					</div>
					<div class="col-md-6">
						<ul class="list-group">
							<li class="list-group-item">
								<h4 class="list-group-item-heading">Instruction</h4>
								<ul>
									<li><span class="label label-info" style="font-size: 110%">2017-10</span> <a href="http://clarin-pl.eu/wp-content/uploads/2017/10/Inforex-instrukcja.pdf">Inforex user instruction (PL)</a></li>
								</ul>
							</li>
							<li class="list-group-item">
								<h4 class="list-group-item-heading">Movies</h4>
								<ul>
									<li><span class="label label-info" style="font-size: 110%">2015-04</span> <a href="https://youtu.be/jCF3Mf_BCTw">Progress monitoring, users, user roles, subcorpora, metadata, frequency lists</a></li>
								</ul>
							</li>
							<li class="list-group-item">
								<h4 class="list-group-item-heading">Presentations</h4>
								<ul>
									<li><span class="label label-info" style="font-size: 110%">2015-04</span> <a href="http://clarin-pl.eu/pliki/warsztaty/Wyklad2.pdf">Gromadzenie, anotowanie i udostępnianie korpusów</a>, Marcin Oleksy, Jan Kocoń</a></li>
								</ul>
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

		<div class="panel panel-default">
			<div class="panel-heading">Publications</div>
			<div class="panel-body">
				<h3>Latest publication</h3>
				<div class="row">
					<div class="col-md-6">
						<b>Marcińczuk, M. &amp; Oleksy, M. (2019). <a href="https://www.researchgate.net/publication/335402187_Inforex_-_a_Collaborative_System_for_Text_Corpora_Annotation_and_Analysis_Goes_Open">
								Inforex — a Collaborative Systemfor Text Corpora Annotation and Analysis Goes Open</a>. In
							Proceedings of the International Conference on Recent Advances in Natural Language Processing, RANLP 2019, pages 711―719. Varna, Bulgaria. INCOMA Ltd.</b>
							<br/><br/>
						<pre>
@inproceedings{ldelim}marcinczuk-oleksy-2019-inforex,
    title     = "{ldelim}I}nforex {ldelim}---} a Collaborative Systemfor Text Corpora Annotation and Analysis Goes Open",
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
                        </pre>

					</div>
					<div class="col-md-6">
						<!--
						<p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View LREC 2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/452559285/content?start_page=1&view_mode=scroll&access_key=key-KMJfdnjdgbeQD4jWmcYs" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
					</div>
				</div>

				<h3>Previous publications</h3>
                <div class="row">
                    <div class="col-md-6">

                        <b>Marcińczuk, M., Oleksy, M. &amp; Kocoń, J. (2017). <a href="https://www.researchgate.net/publication/321580606_Inforex-a_Collaborative_System_for_Text_Corpora_Annotation_and_Analysis">
                            Inforex—a Collaborative System for Text Corpora Annotation and Analysis</a>. In Mitkov, Ruslan, Angelova, Galia (editors),
                        Proceedings of the International Conference Recent Advances in Natural Language Processing, RANLP 2017, pages 473-482. Varna, Bulgaria. INCOMA Ltd.</b>

                <pre>
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
                        </pre>
					</div>
					<div class="col-md-6">
						<!--
						<p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View LREC 2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/373745808/content?start_page=1&view_mode=scroll&access_key=key-auTc87QYbGNceZ59Wf0X&show_recommendations=true" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
                    </div>
				</div>
				<div class="row">
                    <div class="col-md-6">

                    Marcińczuk, M., Kocoń, J. &amp&amp; Broda, B (2012). <a href="https://www.researchgate.net/publication/308886657_Inforex_-_a_web-based_tool_for_text_corpus_management_and_semantic_annotation">Inforex &mdash; a web-based tool for text corpus management and semantic annotation</a>. In Calzolari, N., Choukri, K., Declerck, T., Do\u{ldelim}g}an, M. U., Maegaard, B., Mariani, J. et al (editors), Proceedings of the Eighth International Conference on Language Resources and Evaluation (LREC-2012), pages 224-230. Istanbul, Turkey : European Language Resources Association (ELRA).

			<pre>
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
        			</pre>
					</div>
					<div class="col-md-6">
						<!--
                        <p  style=" margin: 12px auto 6px auto; font-family: Helvetica,Arial,Sans-serif; font-style: normal; font-variant: normal; font-weight: normal; font-size: 14px; line-height: normal; font-size-adjust: none; font-stretch: normal; -x-system-font: none; display: block;">   <a title="View Lrec2012 Inforex Poster on Scribd" href="https://www.scribd.com/doc/125073059/Lrec2012-Inforex-Poster#from_embed"  style="text-decoration: underline;" >Lrec2012 Inforex Poster</a> by <a title="View Michał Marcińczuk's profile on Scribd" href="https://www.scribd.com/user/32430161/Micha%C5%82-Marci%C5%84czuk#from_embed"  style="text-decoration: underline;" >Michał Marcińczuk</a> on Scribd</p>
						-->
						<iframe class="scribd_iframe_embed" src="https://www.scribd.com/embeds/125073059/content?start_page=1&view_mode=scroll&access_key=key-tq924rkuphbq1yra45l&show_recommendations=true" data-auto-height="false" data-aspect-ratio="0.7074509803921568" scrolling="no" id="doc_5289" width="100%" height="600" frameborder="0"></iframe>
                    </div>
                </div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Contribution</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-6">
						<h3>Currently involved in the development</h3>
						<ul>
							<li><a href="http://czuk.eu" title="Coordinator and developer">Michał Marcińczuk</a>,</li>
							<li><a href="http://marcinoleksy.pl/" target="_blank" title="Logo design and substantive consultation">Marcin Oleksy</a>,</li>
							<li><a href="http://wojciechrauk.pl/" target="_blank" title="Developer">Wojciech Rauk</a>.</li>
						</ul>
					</div>

					<div class="col-md-6">
						<h3>Involved in the past</h3>
						<ul>
							<li><span title="Developer">Adam Kaczmarek</span>,</li>
							<li><span title="Developer">Jan Kocoń</span>,</li>
							<li><span title="Developer">Marcin Ptak</span>,</li>
							<li><span title="Developer">Mikołaj Szewczyk</span>.</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
<!--
	</div>
</div>
-->

{include file="inc_footer.tpl"}
