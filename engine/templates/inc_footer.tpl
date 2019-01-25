{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{* Zamknięcie szablonu strony. Szablon rozpoczynający: inc_header *}

		</div>
		{if $config->federationLoginUrl}
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
		    {if $config->log_sql}
		        <div style="text-align: left; background: red; color: white; padding: 3px;"><b>Warning:</b> SQL logging is ON. To disable it set <em style="color: yellow">$config->log_sql=false</em> in <em style="color: yellow">config.local.php</em>.</div>
		    {/if}
			<span style="float: left">This page was tested in <a href="http://www.mozilla.com/pl/firefox/">FireFox</a> <img src="gfx/firefox.png" title="FireFox" style="vertical-align: middle"/>. 
			  <span style="font-size: 0.8em; color: #555;">Page generated in {$page_generation_time} sec(s).</span> 
			</span>
			Involved in development:
			<span>
			<a href="http://czuk.eu" title="Coordinator and developer">Michał Marcińczuk</a>,
			<em title="Developer">Adam Kaczmarek</em>, 
			<em title="Developer">Jan Kocoń</em>, 
			<em title="Developer">Marcin Ptak</em>,
			<em title="Developer">Mikołaj Szewczyk</em>,
			<a href="http://marcinoleksy.pl/" target="_blank" title="Logo design and substantive consultation">Marcin Oleksy</a>,
			<a href="http://wojciechrauk.pl/" target="_blank" title="Developer">Wojciech Rauk</a></br>
			<a href="http://nlp.pwr.wroc.pl">Grupa Technologii Językowych G4.19 Politechniki Wrocławskiej</a>, 2009&ndash;2019
			</span>
		</div>
	</div>
	
   </body>
</html>