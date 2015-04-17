{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{* Zamknięcie szablonu strony. Szablon rozpoczynający: inc_header *}

	    {if $page!="corpus"}
	    </div>
	    {/if}
		
		<div id="footer">
		    {if $config->log_sql}
		        <div style="text-align: left; background: red; color: white; padding: 3px;"><b>Warning:</b> SQL logging is ON. To disable it set <em style="color: yellow">$config->log_sql=false</em> in <em style="color: yellow">config.local.php</em>.</div>
		    {/if}
			<span style="float: left">This page was tested in <a href="http://www.mozilla.com/pl/firefox/">FireFox</a> <img src="gfx/firefox.png" title="FireFox" style="vertical-align: middle"/>. 
			  <span style="font-size: 0.8em; color: #555;">Page generated in {$page_generation_time} sec(s).</span> 
			</span>
			Developed by <a href="http://czuk.eu" title="Michała Marcińczuka home page">Michał Marcińczuk</a>,
			Adam Kaczmarek, 
			<a href="http://www.facebook.com/djkotu">Jan Kocoń</a>, Marcin Ptak, 2009&ndash;2015;</br>
			<a href="http://nlp.pwr.wroc.pl">Grupa Technologii Językowych G4.19 Politechniki Wrocławskiej</a>
		</div>
	</div>
	
   </body>
</html>