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
				<script src="https://ctj.clarin-pl.eu/clarin_bar/script.js"></script>
				<script>
					$(document).ready(function() {ldelim}
                        new ClarinModule({ldelim}
                            offset: {ldelim}
                                'top': 0,
                                'right': 0,
                                'bottom': null,
                                'left': null
                            {rdelim},
                            arrow: {ldelim}
                                'initial-orientation': "left",// up || down || right || left
                                'rotation-hover': 180
                            {rdelim},
                            themeColor: '#337ab7',
                            compactMode: true
                        {rdelim});
                    {rdelim});
				</script>
        {/if}
			<div id="footer">
				<div class="footer-card footer-funding">
					<span class="footer-label">Finansowanie</span>
					<span>
						Praca finansowana w ramach wkładu krajowego na rzecz udziału we wspólnym międzynarodowym przedsięwzięciu
						<a target="_blank" href="https://clarin-pl.eu">CLARIN ERIC: Wspólne zasoby językowe i infrastruktura technologiczna</a>
					</span>
				</div>
				<div class="footer-card footer-credit">
					<span class="footer-label">Inforex</span>
					<span>
						<a target="_blank" href="https://pwr.edu.pl">Politechnika Wrocławska</a>
						<span class="footer-separator">/</span>
						<a target="_blank" href="https://clarin-pl.eu">Zespół CLARIN-PL</a>
						<span class="footer-years">© 2009&ndash;2026</span>
					</span>
				</div>
			</div> <!-- id="footer" -->

			<span>
		    {if $Config.log_sql}
				<div style="text-align: left; background: red; color: white; padding: 3px;"><b>Warning:</b> SQL logging is ON. To disable it set <em style="color: yellow">Config::Cfg()->put_log_sql(false);</em> in <em style="color: yellow">config.local.php</em>.</div>
			{/if}
			</span>
     </div>  <!-- id="page" -->
   </body>
</html>
