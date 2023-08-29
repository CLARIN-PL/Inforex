{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{* Zamknięcie szablonu strony. Szablon rozpoczynający: inc_header *}

		</div> <!-- id="page_content" -->
		{if $Config.federationLoginUrl}
		{*inclusion of clarin bar*}
			{literal}
				<script src="https://ctj.clarin-pl.eu/clarin_bar/script.js"></script>
				<script>
					$(document).ready(function() {
                        new ClarinModule({
                            offset: {
                                'top': 0,
                                'right': 0,
                                'bottom': null,
                                'left': null
                            },
                            arrow: {
                                'initial-orientation': "left",// up || down || right || left
                                'rotation-hover': 180
                            },
                            themeColor: '#337ab7',
                            compactMode: true
                        });
                    });
				</script>
			{/literal}
        {/if}
		<div id="footer">
			<span style="float: left">
				Praca finansowana w ramach wkładu krajowego na rzecz udziału we wspólnym międzynarodowym przedsięwzięciu
				<a target="_blank" href="https://clarin-pl.eu">"CLARIN ERIC: Wspólne zasoby językowe i infrastruktura technologiczna"</a>
			</span>
			<span>
				Copyright © <a target="_blank" href="http://pwr.wroc.pl">Politechnika Wrocławska</a>,
				<a target="_blank" href="http://nlp.pwr.wroc.pl">Grupa Technologii Językowych G4.19</a>, 2009&ndash;2020
			</span>
		</div> <!-- id="footer" -->

			<span>
		    {if $Config.log_sql}
				<div style="text-align: left; background: red; color: white; padding: 3px;"><b>Warning:</b> SQL logging is ON. To disable it set <em style="color: yellow">Config::Config()->put_log_sql(false);</em> in <em style="color: yellow">config.local.php</em>.</div>
			{/if}
			</span>
     </div>  <!-- id="page" -->
   </body>
</html>
